<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class EvacuateQuarter extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function stEvacuateQuarter()
  {
    return [];
  }

  public function actEvacuateQuarter()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $quarterId = $this->getCtxArg('quarter');
    $quarter = Scoresheet6::getQuarters()[$quarterId];
    $scribbles = [];

    // Score it
    $nEnergy = 1 + $scoresheet->countScribbledSlots($quarter[3]);
    $nHousing = 5 - $scoresheet->countScribbledSlots($quarter[2], SCRIBBLE);
    $score = $nEnergy * $nHousing;
    $scribbles[] = $scoresheet->addScribble($quarter[1], $score);
    // Scribble it
    $scribbles[] = $scoresheet->addScribble($quarter[0], SCRIBBLE_RECTANGLE);

    Notifications::evacuateQuarter($player, $scribbles, $score, $quarterId);
  }
}
