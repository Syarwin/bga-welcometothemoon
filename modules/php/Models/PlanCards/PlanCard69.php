<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard69 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Have 5 System Error boxes circled and not crossed off.')
    ];
    $this->rewards = [3, 2];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $slots = $scoresheet->getSectionSlots('errors');
    $n = 0;
    foreach ($slots as $slot) {
      // Is the slot a circled one not crossed off yet?
      if ($scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE) && !$scoresheet->hasScribbledSlot($slot, SCRIBBLE)) {
        $n++;
      }
    }

    return $n >= 5;
  }
}
