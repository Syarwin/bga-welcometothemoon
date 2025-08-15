<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PlanCard96 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Quarantine 3 quarters by closing all the walkways giving access to them. Each quarter must be independently quarantined from one another.')
    ];
    $this->rewards = [11, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $quarantinedQuarters = 0;
    $scoresheet = $player->scoresheet();
    foreach (Scoresheet6::getQuarters() as $quarter) {
      // Is the quarter quarantined?
      $isQuarantine = true;
      foreach ($quarter['links'] as $linkSlot => $linkedQuarter) {
        if (!$scoresheet->hasScribbledSlot($linkSlot)) {
          $isQuarantine = false;
          break;
        }
      }

      if ($isQuarantine) {
        $quarantinedQuarters++;
        if ($quarantinedQuarters >= 3) {
          return true;
        }
      }
    }

    return false;
  }
}
