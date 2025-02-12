<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

trait TurnTrait
{
  public function stStartTurn()
  {
    Stats::incTurns(1);
    Globals::incTurn();
    $cards = ConstructionCards::newTurn();
    Notifications::newTurn(Globals::getTurn(), $cards);

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
    foreach ($players as $pId => $player) {
      $flow = [
        'type' => NODE_SEQ,
        'childs' => [
          ['action' => CHOOSE_CARDS],
        ]
      ];

      // For all scenarios except 1 and 6, checking mission can be done drectly since there are no phase 5 effect
      if (!in_array(Globals::getScenario(), [1, 6])) {
        $flow['childs'][] = ['action' => ACCOMPLISH_MISSION];
      }

      // For solo mode, add the action corresponding to giving a card to Astra
      if (Globals::isSolo()) {
        $flow['childs'][] = ['action' => GIVE_CARD_TO_ASTRA];
      }

      $flows[$pId] = $flow;
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
    // SCENARIO 1 AND 6 HAVE A PHASE 5 EFFECT
    // THAT WE MUST RESOLVE BEFORE CHECKING MISSION SATISFACTION
    if (in_array(Globals::getScenario(), [1, 6])) {
      // Phase 5
      Scoresheet::phase5Check();

      // Mission accomplishment
      $players = Players::getAll();
      $flows = [];
      foreach ($players as $pId => $player) {
        if (PlanCards::getAccomplishablePlans($player)->count() > 0) {
          $flows[$pId] = [
            'action' => ACCOMPLISH_MISSION,
          ];
        }
      }

      // Launch engine only if some players have plans to validate
      if (!empty($flows)) {
        Engine::multipleSetup($flows, ['method' => 'stEndTurn'], 'accomplishMission');
        return;
      }
    }

    $this->stEndTurn();
  }


  // Now that everyone is done, proceed to the end of turn
  public function stEndTurn()
  {
    ConstructionCards::endOfTurn();

    // Check end of scenario
    $nextState = ST_START_TURN;
    foreach (Players::getAll() as $player) {
      if ($player->scoresheet()->isEndOfGameTriggered()) {
        $nextState = ST_END_SCENARIO;
      }
    }

    $this->gamestate->jumpToState($nextState);
  }
}
