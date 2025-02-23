<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario3;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Quarter;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet3;

class FilledQuarter extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function getDescription(): string|array
  {
    return clienttranslate("Get filled quarter points");
  }

  public function stFilledQuarter()
  {
    return [];
  }

  private function getQuarter(): Quarter
  {
    $quarterId = $this->getCtxArg('quarterId');
    return Scoresheet3::getQuarters()[$quarterId];
  }

  public function actFilledQuarter()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $quarter = $this->getQuarter();
    $slots = $quarter->getPointsSlots();
    foreach ($slots as $i => $slot) {
      if ($scoresheet->hasScribbledSlot($slot)) continue;

      $firstToFill = $i == 0;

      // Register for phase 5 if first
      if ($firstToFill) {
        $filledQuarters = Globals::getFilledQuarters();
        $filledQuarters[] = $quarter->getId();
        Globals::setFilledQuarters($filledQuarters);
      }

      $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
      Notifications::filledQuarter($player, $scribble, $quarter, $firstToFill);

      // ASTRA BONUS
      if (Globals::isSolo() && $firstToFill) {
        $astra = Players::getAstra();
        $bonusScribbles = [];
        $bonusScribbles[] = $astra->circleNextBonus();
        $bonusScribbles[] = $astra->circleNextBonus();
        Notifications::circleStationHighMultAstra($player, $bonusScribbles); // The msg is generic
      }
      break;
    }
  }
}
