<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard94 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Evacuate 2 quarters. These quarters can be partially or even totally infected.')
    ];
    $this->rewards = [8, 3];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
