<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
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
    Stats::checkExistence();

    $this->activeNextPlayer();
  }

  // SETUP BRANCH : finish setup for first game or go to advanced setup to choose corpo/planet/private objectives
  public function stSetupBranch()
  {
    $debug = true;
    if ($debug) {
      $this->gamestate->jumpToState(ST_SETUP_DEBUG);
      return;
    }

    if (true) {
      $this->gamestate->jumpToState(ST_SETUP_SCENARIO);
    } else {
    }
  }

  // SETUP SCENARIO
  public function stSetupScenario()
  {
    die("test");
  }
}
