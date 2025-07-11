<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PlanCard95 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Evacuate all the quarters of one floor. These quarters can be partially or even totally infected.')
    ];
    $this->rewards = [8, 3];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $floors = Scoresheet6::getQuartersByFloor();
    foreach ($floors as $quarters) {
      // Extract list of quarter slots of the floor
      $slots = [];
      foreach ($quarters as $quarter) {
        $slots[] = $quarter[0];
      }

      if ($scoresheet->hasScribbledSlots($slots)) {
        return true;
      }
    }

    return false;
  }
}
