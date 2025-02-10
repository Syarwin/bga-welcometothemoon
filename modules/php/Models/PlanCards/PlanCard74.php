<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard74 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle 6 water tanks.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
