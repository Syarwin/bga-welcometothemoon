<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;

trait TurnTrait
{
  public function stStartTurn()
  {
    Stats::incTurns(1);
    ConstructionCards::newTurn();

    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->jumpToState(ST_START_TURN_ENGINE);
  }


  /////////////////////////////////////////////////////////////////
  //  ____  _             _     _____             _
  // / ___|| |_ __ _ _ __| |_  | ____|_ __   __ _(_)_ __   ___
  // \___ \| __/ _` | '__| __| |  _| | '_ \ / _` | | '_ \ / _ \
  //  ___) | || (_| | |  | |_  | |___| | | | (_| | | | | |  __/
  // |____/ \__\__,_|_|   \__| |_____|_| |_|\__, |_|_| |_|\___|
  //                                        |___/
  /////////////////////////////////////////////////////////////////

  /**
   * Boot engine for all players
   */
  function stStartTurnEngine()
  {
    $players = Players::getAll();

    $flows = [];
    $endOfGameTriggered = false;

    foreach ($players as $pId => $player) {
      $flows[$pId] = [
        'action' => WRITE_NUMBER,
      ];
    }

    Engine::multipleSetup($flows, ['method' => 'stEndTurnEngine']);
  }

  //////////////////////////////////////////////////////////
  //  _____           _   _____             _
  // | ____|_ __   __| | | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__,_| |_____|_| |_|\__, |_|_| |_|\___|
  //                                  |___/
  ///////////////////////////////////////////////////////////

  /**
   * End of turn : check plan cards
   */
  function stEndTurnEngine()
  {
    die("test");
  }

  public function initCivCardTurn($nextState)
  {
    // Compute the list of players with endOfTurn actions and wake themp up in turn order
    $order = [];
    $firstPlayer = Globals::getFirstPlayer();
    $pId = $firstPlayer;
    do {
      if (Players::get($pId)->getEndOfTurnActions()) {
        $order[] = $pId;
      }
      $pId = Players::getNextId($pId);
    } while ($pId != $firstPlayer);

    $this->initCustomTurnOrder('civCardTurn', $order, 'stChooseCivCard', $nextState);
  }

  // Boot the engine for that single awaken player
  public function stChooseCivCard()
  {
    $player = Players::getActive();
    Globals::setPhase(END_OF_TURN_PHASE);
    Engine::setup(
      [
        'type' => NODE_PARALLEL,
        'childs' => $player->getEndOfTurnActions(),
      ],
      ['order' => 'civCardTurn'],
      'CivCard',
      [$player->getId()]
    );
  }

  // Now that everyone is done, proceed to the end of turn
  public function stEndTurn()
  {
    Globals::setPhase(NORMAL_PHASE);

    Susan::refill();

    // Update first player
    $pId = Globals::getFirstPlayer();
    $nextId = Players::getNextId($pId);
    if ($pId != $nextId) {
      Globals::setFirstPlayer($nextId);
      Notifications::changeFirstPlayer($nextId);
      $this->gamestate->changeActivePlayer($nextId);
    }

    // Notify end of turn
    Notifications::endOfTurn();

    // Clear endOfTurn actions
    $players = Players::getAll();
    foreach ($players as $pId => $player) {
      $player->emptyEndOfTurnActions();
    }

    // Game end if one depot is empty or if gameEnded flag is true (if a player couldn't play any tile or all event cards were played)
    $nextState = Susan::hasEmptyDepot() || Globals::isGameEndTriggered() ? ST_PRE_END_GAME_TURN : ST_START_TURN;
    $this->gamestate->jumpToState($nextState);
  }
}
