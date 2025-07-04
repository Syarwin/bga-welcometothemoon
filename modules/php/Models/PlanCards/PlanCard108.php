<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard108 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Using the robots, draw your insignia on 7 asteroids on the same sheet.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
