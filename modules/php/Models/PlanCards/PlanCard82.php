<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard82 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('In the mine, complete 5 extraction columns.')
    ];
    $this->rewards = [8, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    return $scoresheet->hasScribbledSomeSlots($scoresheet->getSectionSlots('extractors'), 5);
  }
}
