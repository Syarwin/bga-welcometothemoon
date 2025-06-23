<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class InitGreyVirus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S6_INIT_GREY_VIRUS;
  }

  public function getSlots(Player $player): array
  {
    $quarters = Scoresheet6::getQuarters();
    $slots = [];
    foreach ([0, 3, 7, 10] as $quarter) {
      $slots = array_merge($slots, $quarters[$quarter][2]);
    }
    return $slots;
  }

  public function actInitGreyVirus(int $slot)
  {
    $this->sanityCheck($slot);

    // Which quarter is that ?
    $quarters = Scoresheet6::getQuarters();
    $quarterId = null;
    foreach ([0, 3, 7, 10] as $quarter) {
      if (in_array($slot, $quarters[$quarter][2])) {
        $quarterId = $quarter;
        break;
      }
    }
    $quarter = $quarters[$quarterId];
    $quarterSlot = $quarter[5];

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles = [];
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE);
    $scribbles[] = $scoresheet->addScribble($quarterSlot, SCRIBBLE_CIRCLE);
    $scribbles[] = $scoresheet->addScribble($quarterSlot, SCRIBBLE_CHECKMARK);


    Notifications::initGreyVirus($player, $scribbles, $quarterId);
  }
}
