<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard97 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Quarantine the quarter with the green virus and the quarter with the blue virus, by closing all the walkways giving access to them.')
    ];
    $this->rewards = [12, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }
}
