<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffMultiplier extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_CROSS_OFF_MULTIPLIER;
  }

  protected array $slots = [
    125,
    126,
    127,
    128,
  ];

  public function actCrossOffSabotage(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffMultiplierAstra($player, $scribble);
  }
}
