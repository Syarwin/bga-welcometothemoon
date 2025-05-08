<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;

trait SetupTrait
{
  /*
   * setupNewGame:
   */
  protected function setupNewGame($players, $options = [])
  {
    Globals::setMode(MODE_APPLY);
    Globals::setupNewGame($players, $options);
    Players::setupNewGame($players, $options);
    ConstructionCards::setupNewGame($players, $options);
    Stats::checkExistence();

    $this->activeNextPlayer();
  }

  // SETUP BRANCH : might be useful for later and can be used for debugging launch
  public function stSetupBranch()
  {
    $debug = false;
    if ($debug) {
      $this->gamestate->jumpToState(ST_SETUP_DEBUG);
      return;
    }

    // Single scenario mode
    if (true) {
      $this->gamestate->jumpToState(ST_SETUP_SCENARIO);
    }
    // Campaign mode
    else {
    }
  }

  // SETUP SCENARIO
  public function stSetupScenario()
  {
    $scenario = Globals::getScenario();
    ConstructionCards::setupScenario();
    PlanCards::setupScenario($scenario);
    Notifications::setupScenario($scenario);

    if (Globals::isSolo()) {
      Players::getAstra()->setupScenario();
    }

    // Scenario 8 => add INSIGNAS
    if ($scenario == 8) {
      foreach (Players::getAll() as $player) {
        $player->scoresheet()->setupScenario();
      }
    }

    $this->gamestate->jumpToState(ST_START_TURN);
  }
}
