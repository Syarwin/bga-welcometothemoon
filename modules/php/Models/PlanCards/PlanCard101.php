<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard101 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number the greenhouse of the highest starship with its 3 circled plants.')
    ];
    $this->rewards = [7, 3];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
