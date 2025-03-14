<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;
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
    Log::checkpoint();

    // Any pending solo cards to resolve ?
    if (ConstructionCards::getPendingSoloCards()->count() > 0) {
      $this->stResolveSoloCards();
      return;
    }

    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->jumpToState(ST_START_TURN_ENGINE);
  }


  /////////////////////////////////////////////////////
  //  ____        _          ____              _     
  // / ___|  ___ | | ___    / ___|__ _ _ __ __| |___ 
  // \___ \ / _ \| |/ _ \  | |   / _` | '__/ _` / __|
  //  ___) | (_) | | (_) | | |__| (_| | | | (_| \__ \
  // |____/ \___/|_|\___/   \____\__,_|_|  \__,_|___/
  /////////////////////////////////////////////////////
  public function stResolveSoloCards()
  {
    $cards = ConstructionCards::getPendingSoloCards();
    $astra = Players::getAstra();
    $root = [
      'type' => NODE_SEQ,
      'childs' => []
    ];
    foreach ($cards as $card) {
      $root['childs'][] = $astra->onDrawingSoloCard($card);
    }

    $this->gamestate->setAllPlayersMultiactive();
    $pId = Players::getCurrentId();
    Engine::multipleSetup([$pId => $root], ['method' => 'stEndResolveSoloCardsEngine']);
  }

  public function stEndResolveSoloCardsEngine()
  {
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
    // Reshuffled twice ? Game is over !
    if (Globals::getSoloDraw() >= 2) {
      Notifications::endGameTriggered(null, 'soloDraw');
      $this->gamestate->jumpToState(ST_END_SCENARIO);
      return;
    }

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
    // Phase 5
    Scoresheet::phase5Check();

    // SCENARIO 1 AND 6 HAVE A PHASE 5 EFFECT
    // THAT WE MUST RESOLVE BEFORE CHECKING MISSION SATISFACTION
    if (in_array(Globals::getScenario(), [1, 6])) {

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
    foreach (Players::getAll() as $pId => $player) {
      PGlobals::setCombination($pId, []);
      if ($player->scoresheet()->isEndOfGameTriggered()) {
        Stats::setTriggeredEnd($player->getId(), true);
        $nextState = ST_END_SCENARIO;
      }
    }

    $this->gamestate->jumpToState($nextState);
  }
}
