<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffSabotage extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_CROSS_OFF_SABOTAGE;
  }

  protected array $slots = [
    59,
    61,
    70,
    71,
    75,
    78,
    79,
    91,
  ];

  public function actCrossOffSabotage(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles[] = $scoresheet->addScribble($slot);

    // Also scribble next system error
    $systemErrorSlot = $scoresheet->getNextFreeSystemErrorSlot();
    if (!is_null($systemErrorSlot)) {
      $scribbles[] = $player->scoresheet()->addScribble($systemErrorSlot, SCRIBBLE_CIRCLE);
    }

    Notifications::crossOffSabotage($player, $scribbles);
  }
}
