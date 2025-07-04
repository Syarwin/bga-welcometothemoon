<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard106 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Control 4 planets on the same sheet.')
    ];
    $this->rewards = [12, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
