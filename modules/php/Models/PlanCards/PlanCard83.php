<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard83 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('In the mine, complete 4 adjacent extraction columns.')
    ];
    $this->rewards = [9, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $slots = $scoresheet->getSectionSlots('extractors');
    $n = 0;
    foreach ($slots as $slot) {
      if ($scoresheet->hasScribbledSlot($slot)) {
        $n++;
      } else {
        $n = 0;
      }

      if ($n >= 4) return true;
    }
    return false;
  }
}
