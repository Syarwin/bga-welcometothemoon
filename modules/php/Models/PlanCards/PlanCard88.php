<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard88 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number all the levels of one skyscraper.')
    ];
    $this->rewards = [0, 0];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    foreach ($scoresheet->getNumberBlocks() as $skyscraper) {
      if ($scoresheet->hasScribbledSlots($skyscraper)) {
        return true;
      }
    }
    return false;
  }
}
