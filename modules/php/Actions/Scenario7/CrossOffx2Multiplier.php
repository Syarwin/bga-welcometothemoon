<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario7;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffx2Multiplier extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S7_CROSS_OFF_MULTIPLIER;
  }

  protected array $slots = [154, 155, 156, 157, 158, 159];

  public function actCrossOffx2Multiplier(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffx2MultiplierAstra($player, $scribble);
  }
}
