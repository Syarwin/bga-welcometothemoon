<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard81 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the water tanks in 2 quarters.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $quarterAntennaSlots = [
      [113, 114],
      [115, 116],
      [117, 118],
      [119, 120]
    ];
    $scoresheet = $player->scoresheet();
    $nQuarters = 0;
    foreach ($quarterAntennaSlots as $quarter => $slots) {
      if ($scoresheet->hasScribbledSlots($slots)) {
        $nQuarters++;
      }
    }

    return $nQuarters >= 2;
  }
}
