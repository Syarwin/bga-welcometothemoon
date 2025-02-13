<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;

class ProgramRobot extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_PROGRAM_ROBOT;
  }

  public function argsProgramRobot()
  {
    $player = $this->getPlayer();

    return [
      'slots' => $player->scoresheet()->getSectionFreeSlots('robots'),
    ];
  }

  public function actProgramRobot(int $slot)
  {
    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slot);
    Notifications::programRobot($player, $scribble);
//    list($slot, $mustBuildWall, $bonusSlot) = $this->getNextSlot($player);


  }
}
