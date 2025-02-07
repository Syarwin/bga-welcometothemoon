<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCard68 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Write down 10 X with the Building effects.')
    ];
    $this->rewards = [2, 1];
  }

  public function canAccomplish(Player $player): bool
  {
    return $player->scoresheet()->getScribbles()->filter(fn($scribble) => $scribble->getType() == NUMBER_X)->count() >= 10;
  }
}
