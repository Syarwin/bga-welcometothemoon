<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario2\CirclePlant;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard71 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all the plants for 2 space stations. All the robots do not have to be necessarily circled.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $nCircled = 0;
    foreach (CirclePlant::$stationConnections as $slots) {
      if ($scoresheet->hasScribbledSlots($slots)) {
        $nCircled++;
      }
    }

    return $nCircled >= 2;
  }
}
