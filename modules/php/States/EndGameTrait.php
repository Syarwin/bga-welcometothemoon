<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;

trait EndGameTrait
{
  public function stEndOfScenario()
  {
    // Update score into database
    foreach (Players::getAll() as $player) {
      $score = $player->scoresheet()->getScore();
      if (Globals::isSolo()) {
        $astra = Players::getAstra();
        $score -= $astra->getScore();
      }
      $player->setScore($score);
      Notifications::setFinalScore($player, $score);
    }

    $this->gamestate->nextState('');
  }


  function stPreEndOfGame()
  {
    //    Notifications::endOfGame();
    $this->gamestate->nextState('');
  }
}
