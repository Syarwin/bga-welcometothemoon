<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard81 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the water tanks in 2 quarters.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
