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
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

trait TurnTrait
{
  public function stStartTurn()
  {
    Stats::incTurns(1);
    Globals::incTurn();
    // Scenario 8 => clear cache for scoresheet to allow alternating
    if (Globals::getScenario() == 8) {
      foreach (Players::getAll() as $player) {
        $player->refreshScoresheet();
      }
    }

    $cards = ConstructionCards::newTurn();
    Notifications::newTurn(Globals::getTurn(), $cards);
    Log::checkpoint();


    // Any pending solo cards to resolve ?
    if (ConstructionCards::getPendingSoloCards()->count() > 0) {
      $this->stResolveSoloCards();
      return;
    }
    // Solo S8 => Astra take their turn
    else if (Globals::isSolo() && Globals::getScenario() == 8) {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->jumpToState(ST_START_ASTRA_S8_TURN_ENGINE);
    } else {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->jumpToState(ST_START_TURN_ENGINE);
    }
  }

  /////////////////////////////////////////
  // SOLO S8 ASTRA TURN
  function stStartAstraS8TurnEngine()
  {
    // Reshuffled twice ? Game is over !
    if (Globals::getSoloDraw() >= 2) {
      Notifications::endGameTriggered(null, 'soloDraw');
      $this->gamestate->jumpToState(ST_END_SCENARIO);
      return;
    }

    $player = Players::getAll()->first();
    $flows = [
      $player->getId() => ['action' => S8_ASTRA_TURN],
    ];
    Globals::setAstraTurn(true);
    Engine::multipleSetup($flows, ['method' => 'stEndAstraS8TurnEngine']);
  }

  public function stEndAstraS8TurnEngine()
  {
    if (ConstructionCards::getInLocation("stack-%")->count() < 3) {
      // We draw more cards
      $cards = ConstructionCards::newTurnAuxSoloScenario8();
      Notifications::newTurnAux($cards);

      // We need to recheck for solo cards here
      if (ConstructionCards::getPendingSoloCards()->count() > 0) {
        $this->stResolveSoloCards();
        return;
      }
    }

    Globals::setAstraTurn(false);
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
    ///////////////////////////////////////////////////////
    // Solo S8 => we might have drawn the solo card as the very first card!
    // If that's the case, trigger Astra's turn and draw more cards
    if (Globals::isSolo() && Globals::getScenario() == 8 && ConstructionCards::getInLocation("stack-%")->count() < 3) {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->jumpToState(ST_START_ASTRA_S8_TURN_ENGINE);
      return;
    }
    Globals::setAstraTurn(false);
    ///////////////////////////////////////////////////////

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

    // SCENARIO 1 HAS A PHASE 5 EFFECT
    // THAT WE MUST RESOLVE BEFORE CHECKING MISSION SATISFACTION
    if (Globals::getScenario() == 1) {
      $this->stCheckAccomplishablePlans();
      return;
    }

    // SCENARIO 6 HAS A PHASE 5 EFFECT
    // THAT WE MUST RESOLVE BEFORE CHECKING MISSION SATISFACTION
    if (Globals::getScenario() == 6) {
      $propagations = Globals::getPropagations();
      $players = Players::getAll();
      $flows = [];
      foreach ($players as $pId => $player) {
        $childs = [];
        for ($i = 0; $i < $propagations[$pId]; $i++) {
          $childs[] = [
            'action' => S6_PROPAGATE,
          ];
        }

        if (!empty($childs)) {
          $flows[$pId] = [
            'type' => NODE_SEQ,
            'childs' => $childs,
          ];
        }
      }
      Globals::setPropagations([]);

      if (!empty($flows)) {
        Engine::multipleSetup($flows, ['method' => 'stCheckAccomplishablePlans'], 'accomplishMission');
      } else {
        $this->stCheckAccomplishablePlans();
      }
      return;
    }

    $this->stEndTurn();
  }


  // Mission accomplishment
  function stCheckAccomplishablePlans()
  {
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
      Engine::multipleSetup($flows, ['state' => ST_END_TURN], 'accomplishMission');
    } else {
      $this->stEndTurn();
    }
  }

  // Now that everyone is done, proceed to the end of turn
  public function stEndTurn()
  {
    // SCENARIO 6 => we might have to go to phase 5 check again!
    if (Globals::getScenario() == 6 && !empty(Globals::getActivatedViruses())) {
      $this->stEndTurnEngine();
      return;
    }
    ///////////////////////////////////

    ConstructionCards::endOfTurn();

    // Check the end of a scenario
    $nextState = ST_START_TURN;
    foreach (Players::getAll() as $pId => $player) {
      PGlobals::setCombination($pId, []);
      if ($player->scoresheet()->isEndOfGameTriggered()) {
        Stats::setTriggeredEnd($player->getId(), true);
        $nextState = ST_END_SCENARIO;
      }
    }

    $flows = [];
    if ($nextState === ST_END_SCENARIO && Globals::getScenario() === 6) {
      $flows = Scoresheet6::getEndScenarioEvacuationFlows();
    }
    if (empty($flows)) {
      $this->gamestate->jumpToState($nextState);
    } else {
      Engine::multipleSetup($flows, ['state' => ST_END_TURN], '');
    }
  }
}
