<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario4\FactoryUpgrade;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard84 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Activate the 5 secondary factories located on the top of your sheet.')
    ];
    $this->rewards = [11, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    foreach (FactoryUpgrade::$factories as $factory) {
      if ($factory['type'] != FACTORY_TYPE_SECONDARY) continue;

      if (!$scoresheet->hasScribbledSlots($factory['slots'])) return false;
    }

    return true;
  }
}
