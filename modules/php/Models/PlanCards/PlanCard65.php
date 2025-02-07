<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard65 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number the 3 Robot & Planning floors completely.')
    ];
    $this->rewards = [4, 2];
  }

  public function canAccomplish(Player $player): bool
  {
    $slots = [
      12,
      13,
      14,
      15,
      16,
      17,
      18,
      19,
      20,
      21,
      22,
      23,
      24,
      25,
      26,
      27
    ];
    return $player->scoresheet()->hasScribbledSlots($slots);
  }
}
