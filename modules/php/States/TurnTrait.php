<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\ActionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Meeples;
use Bga\Games\WelcomeToTheMoon\Managers\Scores;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Managers\Cards;
use Bga\Games\WelcomeToTheMoon\Managers\Susan;
use Bga\Games\WelcomeToTheMoon\Managers\Tiles;
use Bga\Games\WelcomeToTheMoon\Managers\ZooCards;

trait TurnTrait
{
  public function stStartTurn()
  {
    Stats::incTurns(1);
    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->jumpToState(ST_CHOOSE_ROTATION);
  }

  ///////////////////////////////////////////////
  //  ____       _        _   _
  // |  _ \ ___ | |_ __ _| |_(_) ___  _ __
  // | |_) / _ \| __/ _` | __| |/ _ \| '_ \
  // |  _ < (_) | || (_| | |_| | (_) | | | |
  // |_| \_\___/ \__\__,_|\__|_|\___/|_| |_|
  ///////////////////////////////////////////////

  public function stChooseRotation()
  {
    $playerCount = Players::count();
    if ($playerCount >= 3) {
      $this->giveExtraTime(Players::getActiveId());
      return;
    }

    $rotation = Globals::getSusanRotation();
    $rotation = ($rotation + 5) % 6;

    Susan::rotate($rotation);

    // Go to tile selection OR resolve event card
    $transition = Globals::getEventCardsGame();
    $this->gamestate->nextState($transition);
  }

  public function actChooseRotation($rotation)
  {
    $this->checkAction('actChooseRotation');
    $player = Players::getActive();
    Susan::rotate((($rotation % 6) + 6) % 6, $player);

    // Go to tile selection OR resolve event card
    $transition = Globals::getEventCardsGame();
    $this->gamestate->nextState($transition);
  }

  //////////////////////////////////
  //  _____                 _
  // | ____|_   _____ _ __ | |_
  // |  _| \ \ / / _ \ '_ \| __|
  // | |___ \ V /  __/ | | | |_
  // |_____| \_/ \___|_| |_|\__|
  //////////////////////////////////

  public function stRevealEventCard()
  {
    Globals::setTurnSpecialRule('');
    $card = Cards::pickOneForLocation('deck_event', 'discard_event', Cards::countInLocation('discard_event'));

    //if deck_event is empty -> last round
    if (!Cards::countInLocation('deck_event') && !Globals::isGameEndTriggered()) {
      Globals::setGameEndTriggered(true);
      Notifications::endOfGameTriggeredEventCard();
    }

    Notifications::newEventCard($card);
    Log::checkpoint();

    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->nextState('');
  }

  function stPlayAfterEventCard()
  {
    $pIds = Players::getAll()->getIds();
    $card = Cards::getTopOf('discard_event')->first();

    $effect = $card->effect();
    if (is_array($effect)) {
      //if each player have special flow
      if (isset($effect['nestedFlows'])) {
        Engine::multipleSetup($effect['nestedFlows'], ['method' => 'stEndOfEventTurn'], 'endOfTurn');
      } else {
        Engine::setup($effect, ['method' => 'stEndOfEventTurn'], 'endOfTurn', $pIds);
      }
    } else {
      $this->gamestate->jumpToState(ST_START_TURN_ENGINE);
    }
  }

  /**
   * End of eventTurn : start engine
   */
  function stEndOfEventTurn()
  {
    $this->initCivCardTurn(ST_START_TURN_ENGINE);
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
    Susan::refill();
    Notifications::endOfTurn(); // Send notif

    Globals::setPhase(NORMAL_PHASE);
    $players = Players::getAll();

    $flows = [];
    $endOfGameTriggered = false;

    foreach ($players as $pId => $player) {
      $player->corporation()->resetFlags();
      $player->emptyEndOfTurnActions(); //because it should have been used after the event card

      // Check if end of game is triggered or not
      if (!$endOfGameTriggered && !$player->canTakeAction(PLACE_TILE, [])) {
        $endOfGameTriggered = true;
        Notifications::endOfGameTriggered($player);
        Globals::setGameEndTriggered(true);

        // if ($this->getGameProgression() < 50) {
        //   $name = $player->getName();
        //   die("The end of game would be triggered in the next round because $name wont be able to place any tile. Since the overall progression is low, this might be a bug. If indeed THIS IS NOT SUPPOSED to trigger end of game, please create a NEW bug report linked to that table so we can find what the issue is. Otherwise, please just wait AND DONT CREATE ANY BUG REPORT.");
        // }
      }

      $flows[$pId] = [
        'action' => \PLACE_TILE,
        'args' => ['type' => 'normal'],
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
   * End of turn : replenish and check if any CIV card need to be taken
   */
  function stEndTurnEngine()
  {
    Susan::refill();
    Notifications::endOfTurn();

    $this->initCivCardTurn(ST_END_TURN);
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
