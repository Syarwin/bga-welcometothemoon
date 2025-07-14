<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class CrossOffVirus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S6_CROSS_OFF_VIRUS;
  }

  protected array $slots = [224, 228, 216, 219];

  public function actCrossOffVirus(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    Scoresheet6::activateViruses([Scoresheet6::getVirusOfQuarter($slot)], [$player->getId() => 0]);
  }
}
