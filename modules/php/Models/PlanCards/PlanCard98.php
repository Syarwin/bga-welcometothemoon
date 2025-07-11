<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PlanCard98 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the energies on 2 floors.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $floors = Scoresheet6::getQuartersByFloor();
    $n = 0;
    foreach ($floors as $quarters) {
      // Extract list of quarter slots of the floor
      $slots = [];
      foreach ($quarters as $quarter) {
        $slots = array_merge($slots, $quarter[3]);
      }

      if ($scoresheet->hasScribbledSlots($slots)) {
        $n++;
        if ($n >= 2) return true;
      }
    }

    return false;
  }
}
