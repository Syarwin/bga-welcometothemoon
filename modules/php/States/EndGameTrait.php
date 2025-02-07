<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
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

trait EndGameTrait
{
  public function stEndOfScenario()
  {
    // TODO : notify winner of the scenario and branch
    $this->gamestate->nextState('');
  }


  function stPreEndOfGame()
  {
    //    Notifications::endOfGame();
    $this->gamestate->nextState('');
  }
}
