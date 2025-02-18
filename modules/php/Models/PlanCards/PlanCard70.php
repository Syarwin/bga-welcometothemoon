<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard70 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle a multiplier bonus for 3 different space stations with the robots.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $slots = array_merge($scoresheet->getSectionSlots('bigmultipliers'), $scoresheet->getSectionSlots('smallmultipliers'));
    return $scoresheet->hasScribbledSomeSlots($slots, 3, SCRIBBLE_CIRCLE);
  }
}
