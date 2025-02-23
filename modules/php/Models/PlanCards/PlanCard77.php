<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet3;

class PlanCard77 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number all the buildings in 3 horizontal rows.')
    ];
    $this->rewards = [12, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $grid = Scoresheet3::$grid;

    $nFilled = 0;
    // For each row
    for ($row = 0; $row <= 10; $row += 2) {
      // Go through each column
      $filled = true;
      for ($column = 0; $column <= 10; $column += 2) {
        $slot = $grid[$row][$column];
        if ($slot != -1 && $slot != TOP_RIGHT_CORNER_SLOT && !$scoresheet->hasScribbledSlot($slot)) {
          $filled = false;
          break;
        }
      }

      // Is it filled up?
      if ($filled) {
        $nFilled++;
        if ($nFilled >= 3) {
          return true;
        }
      }
    }

    return false;
  }
}
