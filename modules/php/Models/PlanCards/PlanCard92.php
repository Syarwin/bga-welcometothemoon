<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard92 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Build all sections of the dome.')
    ];
    $this->rewards = [0, 0];
  }

  public function canAccomplish(Player $player): bool
  {
    return empty($player->scoresheet()->getUnbuiltDomeSections());
  }
}
