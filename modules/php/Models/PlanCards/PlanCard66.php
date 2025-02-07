<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard66 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number the Energy floor completely.')
    ];
    $this->rewards = [3, 2];
  }

  public function canAccomplish(Player $player): bool
  {
    $slots = [
      28,
      29,
      30,
      31,
      32,
      33,
      34,
      35,
      36,
      37
    ];
    return $player->scoresheet()->hasScribbledSlots($slots);
  }
}
