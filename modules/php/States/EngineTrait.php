<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;

trait EngineTrait
{
  function stInitPrivateEngine()
  {
    return true; // AVOID SENDING CHANGE OF STATE
  }

  function argsSetupEngine()
  {
    return [
      'descSuffix' => Globals::getEngineWaitingDescriptionSuffix(),
    ];
  }

  function addCommonArgs($pId, &$args)
  {
    $args['previousEngineChoices'] = PGlobals::getEngineChoices($pId);
    $args['previousSteps'] = Log::getUndoableSteps($pId);
  }

  /**
   * Trying to get the atomic action corresponding to the state where the game is
   */
  function getCurrentAtomicAction($pId)
  {
    $node = Engine::getNextUnresolved($pId);
    return $node->getAction();
  }

  /**
   * Ask the corresponding atomic action for its args
   */
  function argsAtomicAction($pId)
  {
    $player = Players::get($pId);
    $node = Engine::getNextUnresolved($pId);
    if (is_null($node)) {
      return ['noNode' => true];
    }

    $action = $this->getCurrentAtomicAction($pId);
    $args = Actions::getArgs($action, $node);
    $args['automaticAction'] = Actions::get($action, $node)->isAutomatic($player);
    $this->addCommonArgs($pId, $args);
    $this->addArgsAnytimeAction($pId, $args, $action);

    return $args;
  }

  /**
   * Add anytime actions
   */
  function addArgsAnytimeAction($pId, &$args, $action)
  {
    // If the action is auto => don't display anytime buttons
    if ($args['automaticAction'] ?? false) {
      return;
    }
    $player = Players::get($pId);
    $actions = $player->corporation()->getAnytimeActions();

    // Keep only doable actions
    $anytimeActions = [];
    foreach ($actions as $flow) {
      $flow['pId'] = $pId;
      $tree = Engine::buildTree($flow);
      if ($tree->isDoable($player)) {
        $anytimeActions[] = [
          'flow' => $flow,
          'desc' => $flow['desc'] ?? $tree->getDescription(true),
          'optionalAction' => $tree->isOptional(),
          'independentAction' => $tree->isIndependent($player),
          'irreversibleAction' => $tree->isIrreversible($player),
          'source' => $tree->getSource(),
        ];
      }
    }

    $args['anytimeActions'] = $anytimeActions;
  }

  function actAnytimeAction($choiceId, $auto = false)
  {
    $pId = self::getCurrentPId();
    $args = $this->gamestate->getPrivateState($pId)['args'];
    if (!isset($args['anytimeActions'][$choiceId])) {
      throw new \BgaVisibleSystemException('You can\'t take this anytime action');
    }

    $flow = $args['anytimeActions'][$choiceId]['flow'];
    if (!$auto) {
      PGlobals::incEngineChoices($pId);
    }
    Engine::insertBeforeCurrent($pId, $flow);

    // Flag
    $flag = $flow['flag'] ?? null;
    if (!is_null($flag)) {
      $flags = PGlobals::getFlags($pId);
      $flags[$flag] = true;
      PGlobals::setFlags($pId, $flags);
    }

    Engine::proceed($pId);
  }

  /**
   * Pass the argument of the action to the atomic action
   */
  function actTakeAtomicAction($actionName, $args)
  {
    self::checkAction($actionName);
    $pId = Players::getCurrentId();
    $action = $this->getCurrentAtomicAction($pId);
    $ctx = Engine::getNextUnresolved($pId);
    Actions::takeAction($action, $actionName, $args, $ctx);
  }

  /**
   * To pass if the action is an optional one
   */
  function actPassOptionalAction($auto = false, $pId = null)
  {
    if (!$auto) {
      self::checkAction('actPassOptionalAction');
    }

    $pId = $pId ?? Players::getCurrentId();
    $action = $this->getCurrentAtomicAction($pId);
    $ctx = Engine::getNextUnresolved($pId);
    Actions::pass($action, $ctx, $auto);
  }

  /**
   * Pass the argument of the action to the atomic action
   */
  function stAtomicAction($pId)
  {
    $action = $this->getCurrentAtomicAction($pId);
    return Actions::stAction($action, Engine::getNextUnresolved($pId));
  }

  /********************************
   ********************************
   ********** FLOW CHOICE *********
   ********************************
   ********************************/
  function argsResolveChoice($pId)
  {
    $player = Players::get($pId);
    $node = Engine::getNextUnresolved($pId);
    $args = array_merge($node->getArgs() ?? [], [
      'choices' => Engine::getNextChoice($player),
      'allChoices' => Engine::getNextChoice($player, true),
    ]);
    if ($node instanceof \Bga\Games\WelcomeToTheMoon\Core\Engine\XorNode) {
      $args['descSuffix'] = 'xor';
    }
    // $sourceId = $node->getSourceId() ?? null;
    // if (!isset($args['source']) && !is_null($sourceId)) {
    //   $args['sourceId'] = $sourceId;
    //   $args['source'] = ZooCards::get($sourceId)->getName();
    // }
    $this->addCommonArgs($pId, $args);
    $this->addArgsAnytimeAction($pId, $args, 'resolveChoice');
    return $args;
  }

  function actChooseAction($choiceId)
  {
    $player = Players::getCurrent();
    Engine::chooseNode($player, $choiceId);
  }

  public function stResolveStack() {}

  public function stResolveChoice() {}

  function argsImpossibleAction($pId)
  {
    $node = Engine::getNextUnresolved($pId);
    $args = [
      'desc' => $node->getDescription(),
    ];
    $this->addCommonArgs($pId, $args);
    $this->addArgsAnytimeAction($pId, $args, 'impossibleAction');
    return $args;
  }

  /*******************************
   ******* CONFIRM / RESTART ******
   ********************************/
  public function argsConfirmTurn($pId)
  {
    $data = [
      'previousEngineChoices' => PGlobals::getEngineChoices($pId),
      'previousSteps' => Log::getUndoableSteps($pId),
      'automaticAction' => false,
    ];
    $this->addCommonArgs($pId, $data);
    $this->addArgsAnytimeAction($pId, $data, 'confirmTurn');
    return $data;
  }

  public function stConfirmTurn($pId)
  {
    // Check user preference to bypass if DISABLED is picked
    $pref = Players::get($pId)->getPref(OPTION_CONFIRM);
    if ($pref == OPTION_CONFIRM_DISABLED && (!isset($this->_isCancel) || !$this->_isCancel)) {
      $this->actConfirmTurn(true, $pId);
      return true; // SKIP ENTERING STATE IN UI
    }
  }

  public function actConfirmTurn($auto = false, $pId = null)
  {
    if (!$auto) {
      self::checkAction('actConfirmTurn');
      $pId = Players::getCurrentId();
    }
    Engine::confirm($pId);
  }

  public function actRestart()
  {
    self::checkAction('actRestart');
    $pId = Players::getCurrentId();
    if (PGlobals::getEngineChoices($pId) < 1) {
      throw new \BgaVisibleSystemException('No choice to undo');
    }
    Engine::restart($pId);
  }

  public function actUndoToStep($stepId)
  {
    self::checkAction('actRestart');
    $pId = Players::getCurrentId();
    Engine::undoToStep($pId, $stepId);
  }

  public function actCancelEngine()
  {
    $pId = Players::getCurrentId();
    $this->gamestate->setPlayersMultiactive([$pId], '');
    $this->_isCancel = true;
    $state = PGlobals::getState($pId);
    $this->gamestate->setPrivateState($pId, $state);
  }

  public function stApplyEngine()
  {
    Engine::apply();
    Engine::callback();
  }
}
