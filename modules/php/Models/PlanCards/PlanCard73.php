<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard73 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Using the energy, create on your trajectory 3 zones of numbered spaces: 1 zone of 6 spaces, 1 zone of 4 spaces and 1 zone of 2 spaces.')
    ];
    $this->rewards = [12, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
