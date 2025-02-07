<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard64 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number the 3 Astronaut & Water floors completely.')
    ];
    $this->rewards = [3, 2];
  }

  public function canAccomplish(Player $player): bool
  {
    $slots = [
      1,
      2,
      3,
      4,
      5,
      6,
      7,
      8,
      9,
      10,
      11
    ];
    return $player->scoresheet()->hasScribbledSlots($slots);
  }
}
