<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet5;

class PlanCard91 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('In the plant & water tower, circle all 3 symbols (the 2 plants + the water) in 2 levels.')
    ];
    $this->rewards = [0, 0];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $n = 0;
    foreach (Scoresheet5::$levels as $level) {
      if ($scoresheet->hasScribbledSlots(array_merge($level['waters'], $level['plants']))) {
        $n++;
      }

      if ($n >= 2) return true;
    }
    return false;
  }
}
