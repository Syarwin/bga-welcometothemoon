<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard77 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number all the buildings in 3 horizontal rows.')
    ];
    $this->rewards = [12, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
