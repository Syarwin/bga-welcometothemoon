<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard96 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Quarantine 3 quarters by closing all the walkways giving access to them. Each quarter must be independently quarantined from one another.')
    ];
    $this->rewards = [11, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
