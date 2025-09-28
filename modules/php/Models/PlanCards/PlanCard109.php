<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

class PlanCard109 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Improve the value twice for each type of planet on the same sheet.')
    ];
    $this->rewards = [11, 6];
  }

  public function canAccomplish(Player $player): bool
  {
    /** @var Scoresheet8 $scoresheet */
    $scoresheet = new Scoresheet8($player, Players::getPrevOrAstra($player), 1);
    if ($scoresheet->hasAllPlanetsUpgradedTwice()) {
      return true;
    }
    $scoresheet = new Scoresheet8(Players::getNextOrAstra($player), $player, 2);
    if ($scoresheet->hasAllPlanetsUpgradedTwice()) {
      return true;
    }

    return false;
  }
}
