<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleSingleLinked extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_CIRCLE_SINGLE_LINKED;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    return !is_null($this->getArgs()['slot']);
  }

  public function getDescription(): string
  {
    return clienttranslate("Circle a connected slot");
  }

  public function stCircleSingleLinked()
  {
    $pref = Players::getCurrent()->getPref(OPTION_CIRCLE_LINKED_SLOT);
    return $pref === OPTION_CIRCLE_AUTOMATIC ? [] : null;
  }

  public function argsCircleSingleLinked()
  {
    $scenario = Globals::getScenario();
    $args = [...$this->getCtxArgs(), 'descSuffix' => "A{$scenario}Water"];
    return $args;
  }

  public function actCircleSingleLinked()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $args = $this->getArgs();
    $slot = $args['slot'];
    $values = $args['values'] ?? null;
    $type = $args['type'] ?? null;
    if ($scoresheet->hasScribbledSlot($slot)) return;

    // Circle the slot
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::circleSingleLinked($player, $scribble, $type, $values[$slot] ?? null);

    // CIRCLE_TYPE_FILLING_BONUS is used in S4 only
    if (isset($args['type']) && $args['type'] === CIRCLE_TYPE_FILLING_BONUS) {
      $scoresheet->prepareForPhaseFive($args);

      // ASTRA
      if (Globals::isSolo()) {
        $astra = Players::getAstra();
        $bonusScribbles = [];
        $bonusScribbles[] = $astra->circleNextBonus();
        $bonusScribbles[] = $astra->circleNextBonus();
        Notifications::circleStationHighMultAstra($player, $bonusScribbles); // The msg is generic
      }
    }
  }

  // TODO: Revert the commit this was added after all tables will be started after 6/03/2025
  public function actStirWaterTanks()
  {
    $this->actCircleSingleLinked();
  }
}
