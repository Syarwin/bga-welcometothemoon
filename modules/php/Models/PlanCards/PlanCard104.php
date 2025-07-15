<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard104 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Circle all reactors of 3 starships.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $starshipReactorsAll = $scoresheet->getReactors();
    $starshipsFinished = array_map(function ($reactorsOfStarship) use ($scoresheet) {
      return $scoresheet->countScribbledSlots($reactorsOfStarship) === count($reactorsOfStarship);
    }, $starshipReactorsAll);
    return count(array_filter($starshipsFinished)) >= 3;
  }
}
