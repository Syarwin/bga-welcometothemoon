<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard93 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Write down an X in 4 levels with Planning actions.')
    ];
    $this->rewards = [0, 0];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
