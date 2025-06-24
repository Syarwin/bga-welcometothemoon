<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;

/* Class to manage all the Actions */

class Actions
{
  static $classes = [
    CHOOSE_CARDS,
    WRITE_NUMBER,
    ACCOMPLISH_MISSION,
    GIVE_CARD_TO_ASTRA,
    REPLACE_SOLO_CARD,
    CIRCLE_NEXT_IN_ROW,
    CIRCLE_SINGLE_LINKED,

    // Scenario 1
    TAKE_BONUS,
    CROSS_ROCKETS,
    WRITE_X,
    ROCKET_ACTIVATION,
    ACTIVATE_SABOTAGE,
    CROSS_OFF_SABOTAGE,

    // Scenario 2
    CIRCLE_ENERGY,
    PLACE_ENERGY_WALL,
    PROGRAM_ROBOT,
    CIRCLE_PLANT,
    CROSS_OFF_MULTIPLIER,

    // Scenario 3
    CIRCLE_GREENHOUSE,
    IMPROVE_BONUS,
    BUILD_ROBOT_TUNNEL,
    FILLED_QUARTER,
    CROSS_OFF_FILLED_QUARTER_BONUS,

    // Scenario 4
    S4_CIRCLE_PLANT_OR_WATER,
    S4_FACTORY_UPGRADE,
    S4_EXTRACT_RESOURCES,
    S4_CROSS_OFF_FACTORY_BONUS,

    // Scenario 5
    S5_SPLIT_DOME,
    S5_BUILD_DOME,
    S5_ENERGY_UPGRADE,
    S5_FILLED_SKYSCRAPER,
    S5_CROSS_OFF_SKYSCRAPER_BONUS,

    // Scenario 6
    S6_INIT_GREY_VIRUS,
    S6_CIRCLE_SYMBOL,
    S6_CIRCLE_ENERGY,
    S6_CLOSE_WALKWAY,
    S6_PROPAGATE,
    S6_PROPAGATE_VIRUS,
    S6_EVACUATE_QUARTER,
  ];

  public static function get($actionId, &$ctx = null)
  {
    // TODO: Revert the commit this was added after all tables will be started after 6/03/2025
    $actionId = str_replace('StirWaterTanks', 'CircleSingleLinked', $actionId);
    $actionId = str_replace('CircleOther', 'CircleNextInRow', $actionId);

    if (!in_array($actionId, self::$classes)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an atomic action not defined in Actions.php : ' . $actionId);
    }
    $name = '\Bga\Games\WelcomeToTheMoon\Actions\\' . $actionId;
    return new $name($ctx);
  }

  public static function isDoable($actionId, $ctx, $player)
  {
    $res = self::get($actionId, $ctx)->isDoable($player);
    return $res;
  }

  public static function getErrorMessage($actionId)
  {
    $actionId = Utils::ucfirst(mb_strtolower($actionId));
    $msg = sprintf(
      'Attempting to take an action (%s) that is not possible. Either another card erroneously flagged this action as possible, or this action was possible until another card interfered.',
      $actionId
    );
    return $msg;
  }

  public static function getState($actionId, $ctx)
  {
    return self::get($actionId, $ctx)->getState();
  }

  public static function getArgs($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    return array_merge($action->getArgs(), ['optionalAction' => $ctx->isOptional()]);
  }

  public static function takeAction($actionId, $actionName, $args, &$ctx, $automatic = false)
  {
    $player = self::getPlayer($ctx);
    if (!self::isDoable($actionId, $ctx, $player)) {
      throw new \BgaUserException(self::getErrorMessage($actionId) . ' / ' . $actionName . ' / ' . var_export($args, true));
    }

    // Check action
    if (!$automatic && Globals::getMode() == \MODE_PRIVATE) {
      Game::get()->checkAction($actionName);
      $stepId = Log::step();
      Notifications::newUndoableStep($player, $stepId);
    }

    // Run action
    $action = self::get($actionId, $ctx);
    $methodName = $actionName; //'act' . self::$classes[$actionId];
    $action->$methodName(...$args);

    // Resolve action
    $automatic = $ctx->isAutomatic($player);
    $checkpoint = $ctx->isIrreversible($player);
    $ctx = $action->getCtx();
    Engine::resolveAction(['actionName' => $actionName, 'args' => $args], $checkpoint, $ctx, $automatic);
    Engine::proceed($player->getId());
  }

  public static function getPlayer($node)
  {
    return Players::get($node->getRoot()->getPId());
  }

  public static function stAction($actionId, $ctx)
  {
    $player = self::getPlayer($ctx);
    if (!self::isDoable($actionId, $ctx, $player)) {
      if (!$ctx->isOptional()) {
        if (self::isDoable($actionId, $ctx, $player, true)) {
          Game::get()->gamestate->jumpToState(ST_IMPOSSIBLE_MANDATORY_ACTION);
          return;
        } else {
          throw new \BgaUserException(self::getErrorMessage($actionId));
        }
      } else {
        // Auto pass if optional and not doable
        Game::get()->actPassOptionalAction(true, $player->getId());
        return true;
      }
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'st' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $result = $action->$methodName();
      if (!is_null($result)) {
        $actionName = 'act' . $action->getClassName();
        self::takeAction($actionId, $actionName, $result, $ctx, true);
        return true; // We are changing state
      }
    }
  }

  public static function stPreAction($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'stPre' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
      $player = self::getPlayer($ctx);
      if ($ctx->isIrreversible($player)) {
        Engine::checkpoint($player->getId());
      }
    }
  }

  public static function pass($actionId, $ctx, $auto = false)
  {
    if (!$ctx->isOptional()) {
      var_dump($ctx->toArray());
      throw new \BgaVisibleSystemException('This action is not optional, ' . $actionId);
    }

    $player = self::getPlayer($ctx);
    // Check action
    if (!$auto && Globals::getMode() == \MODE_PRIVATE) {
      Game::get()->checkAction('actPassOptionalAction');
      $stepId = Log::step();
      Notifications::newUndoableStep($player, $stepId);
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'actPass' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    } else {
      Engine::resolveAction(PASS, false, $ctx, $auto);
    }
    Engine::proceed($player->getId());
  }
}
