<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard76 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number all the buildings in 3 vertical columns.')
    ];
    $this->rewards = [12, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
