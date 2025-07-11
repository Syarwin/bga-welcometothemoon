<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PlanCard97 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Quarantine the quarter with the green virus and the quarter with the blue virus, by closing all the walkways giving access to them.')
    ];
    $this->rewards = [12, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $quarantinedQuarters = 0;
    $scoresheet = $player->scoresheet();
    $quarters = Scoresheet6::getQuarters();
    $quarters = [$quarters[2], $quarters[8]];
    foreach ($quarters as $quarter) {
      foreach ($quarter[4] as $linkSlot => $linkedQuarter) {
        if (!$scoresheet->hasScribbledSlot($linkSlot)) {
          return false;
        }
      }
    }
    return true;
  }
}
