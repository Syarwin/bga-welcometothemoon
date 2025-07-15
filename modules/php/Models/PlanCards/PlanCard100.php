<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard100 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number 3 greenhouses with their 3 circled plants.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $greenhouses = $scoresheet->getGreenhouses();
    $finishedCount = 0;
    foreach ($greenhouses as $greenhouse) {
      if ($scoresheet->hasScribbledSlot($greenhouse['numberSlot']) && $scoresheet->getCircledPlants($greenhouse) === 3) {
        $finishedCount += 1;
      }
    }
    return $finishedCount >= 3;
  }
}
