<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard107 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Control 1 planet of each type on the same sheet.')
    ];
    $this->rewards = [9, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
