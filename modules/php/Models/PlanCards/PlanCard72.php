<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard72 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Using the energy, create on your trajectory 2 zones of 6 numbered spaces.')
    ];
    $this->rewards = [11, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
