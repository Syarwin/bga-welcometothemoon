<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

class PlanCard110 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Cross off 6 scoring boxes for the plants and 4 scoring boxes for the water on the same sheet.')
    ];
    $this->rewards = [10, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    /** @var Scoresheet8 $scoresheet */
    $scoresheet = new Scoresheet8($player, Players::getPrevOrAstra($player), 1);
    if ($scoresheet->has6PlantsAnd4Water()) {
      return true;
    }
    $scoresheet = new Scoresheet8(Players::getNextOrAstra($player), $player, 2);
    if ($scoresheet->has6PlantsAnd4Water()) {
      return true;
    }

    return false;
  }
}
