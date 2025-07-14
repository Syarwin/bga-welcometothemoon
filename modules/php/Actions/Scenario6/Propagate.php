<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class Propagate extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function stPropagate()
  {
    return [];
  }

  public function actPropagate()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $quarters = Scoresheet6::getQuarters();
    $viruses = [];
    $childs = [];
    foreach ($quarters as $i => $quarter) {
      $virusSlot = $quarter[5];
      if (!is_null($virusSlot) && $scoresheet->hasScribbledSlot($virusSlot, SCRIBBLE_CIRCLE)) {
        $viruses[] = Scoresheet6::getVirusOfQuarter($virusSlot);
        $childs[] = [
          'action' => S6_PROPAGATE_VIRUS,
          'args' => ['quarter' => $i]
        ];
      }
    }

    // ASTRA BONUS
    if (Globals::isSolo()) {
      $bonusScribble = Players::getAstra()->circleNextBonus();
      Notifications::gainOneSoloBonus($player, $bonusScribble);
    }

    $this->insertAsChild([
      'type' => NODE_SEQ,
      'childs' => $childs
    ]);

    Notifications::startPropagation($player, $viruses);
  }
}
