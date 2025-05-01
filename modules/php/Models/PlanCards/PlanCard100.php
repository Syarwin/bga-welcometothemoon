<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard100 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number 3 greenhouses with their 3 circled plants.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
