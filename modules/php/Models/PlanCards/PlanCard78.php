<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard78 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Using the robots, connect the tunnel network to the observatory in the top right corner of your sheet.')
    ];
    $this->rewards = [9, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
