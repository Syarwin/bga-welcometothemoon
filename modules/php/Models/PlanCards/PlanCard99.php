<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard99 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the plants and all the water tanks on 2 floors.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
