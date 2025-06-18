<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard87 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle 5 waters in the mine, whether they are extracted or not.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    return $scoresheet->hasScribbledSomeSlots($scoresheet->getSectionSlots('resources/waters'), 5, SCRIBBLE_CIRCLE);
  }
}
