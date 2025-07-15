<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard105 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all water tanks of 3 starships.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $waterTanks = [
      [72, 73],
      [74, 75, 76],
      [77, 78],
      [79, 80],
      [81, 82],
      [83, 84],
    ];
    $scoresheet = $player->scoresheet();
    $starshipsFinished = array_map(function ($tanksOfAStarship) use ($scoresheet) {
      return $scoresheet->countScribbledSlots($tanksOfAStarship) === count($tanksOfAStarship);
    }, $waterTanks);
    return count(array_filter($starshipsFinished)) >= 3;
  }
}
