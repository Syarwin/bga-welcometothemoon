<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard99 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the plants and all the water tanks on 2 floors.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $floors = [
      [136, 137, 120, 121],
      [138, 139, 122, 123, 124],
      [140, 141, 125, 126, 127, 128, 129, 130],
      [142, 143, 131, 132, 133],
      [144, 145, 134, 135],
    ];

    $n = 0;
    foreach ($floors as $slots) {
      if ($scoresheet->hasScribbledSlots($slots)) {
        $n++;
        if ($n >= 2) return true;
      }
    }

    return false;
  }
}
