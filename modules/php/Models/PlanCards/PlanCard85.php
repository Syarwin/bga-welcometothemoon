<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario4\FactoryUpgrade;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard85 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Upgrade or activate a total of 6 factories, either main or secondary.')
    ];
    $this->rewards = [9, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $n = 0;
    foreach (FactoryUpgrade::$factories as $factory) {
      if ($scoresheet->hasScribbledSlots($factory['slots'])) {
        $n++;
      }
    }

    return $n >= 6;
  }
}
