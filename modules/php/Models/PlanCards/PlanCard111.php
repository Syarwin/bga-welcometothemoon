<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard111 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Using the Planning actions, draw your insignia on 4 different moons on the same sheet.')
    ];
    $this->rewards = [10, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
