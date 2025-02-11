<?php

namespace Bga\Games\WelcomeToTheMoon\States;

use Bga\Games\WelcomeToTheMoon\Managers\Players;


trait EndGameTrait
{
  public function stEndOfScenario()
  {
    // Update score into database
    // $maxScore = 0;
    // $maxPlayers = [];
    foreach (Players::getAll() as $player) {
      $score = $player->scoresheet()->getScore();
      $player->setScore($score);
      // if($score > $maxScore){
      //   $maxScore = $score;
      //   $maxPlayers = [$player];
      // } else if($score == $maxScore){
      //   $maxPlayers[] = $player;
      // }
    }

    $this->gamestate->nextState('');
  }


  function stPreEndOfGame()
  {
    //    Notifications::endOfGame();
    $this->gamestate->nextState('');
  }
}
