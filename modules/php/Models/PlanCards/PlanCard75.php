<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard75 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle 4 consecutive water tanks on your trajectory.')
    ];
    $this->rewards = [11, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
