<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard67 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number the Plant and Wild floors completely.')
    ];
    $this->rewards = [3, 2];
  }

  public function canAccomplish(Player $player): bool
  {
    $slots = [
      38,
      39,
      40,
      41,
      42,
      43,
      44,
      45,
      46,
      47,
      48,
      49,
      50,
      51,
      52,
      53
    ];
    return $player->scoresheet()->hasScribbledSlots($slots);
  }
}
