<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet5;

class PlanCard90 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('In the plant & water tower, circle at least 2 symbols (2 plants or 1 plant + 1 water) in 3 levels.')
    ];
    $this->rewards = [0, 0];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $n = 0;
    foreach (Scoresheet5::$levels as $level) {
      if ($scoresheet->hasScribbledSomeSlots(array_merge($level['waters'], $level['plants']), 2)) {
        $n++;
      }

      if ($n >= 3) return true;
    }
    return false;
  }
}
