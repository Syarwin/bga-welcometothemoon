<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard95 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Evacuate all the quarters of one floor. These quarters can be partially or even totally infected.')
    ];
    $this->rewards = [8, 3];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
