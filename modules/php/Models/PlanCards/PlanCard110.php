<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard110 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Cross off 6 scoring boxes for the plants and 4 scoring boxes for the water on the same sheet.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
