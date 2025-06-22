<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class CircleEnergy extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S6_CIRCLE_ENERGY;
  }

  public function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $quarters = Scoresheet6::getQuarters();

    // Quarter specified => only consider this quarter
    $quarter = $this->getCtxArg('quarter');
    if (!is_null($quarter)) {
      $quarters = [$quarters[$quarter]];
    }

    $slots = [];
    foreach ($quarters as $quarterInfo) {
      // If the quarter is complete => skip
      $quarterSlot = $quarterInfo[0];
      if ($scoresheet->hasScribbledSlot($quarterSlot)) {
        continue;
      }

      // Take first unscribbled energy slot
      foreach ($quarterInfo[3] as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot)) {
          $slots[] = $slot;
          break;
        }
      }
    }

    return $slots;
  }

  public function getDescription(): string
  {
    return clienttranslate("Circle an energy in a non-complete quarter");
  }

  public function stCircleEnergy()
  {
    $player = $this->getPlayer();
    $slots = $this->getFreeSlots($player);
    $singleChoice = count($slots) === 1;
    return $singleChoice ? ['slot' => $slots[0]] : null;
  }

  public function actCircleEnergy(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::circleEnergy($player, $scribble, false);
  }
}
