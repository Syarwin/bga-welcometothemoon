<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard79 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the parabolic antennas in 2 quarters.')
    ];
    $this->rewards = [9, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $quarterAntennaSlots = [
      [125, 121, 123],
      [122, 126, 124, 134, 135, 136],
      [129, 127, 132],
      [130, 133, 128, 131]
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
