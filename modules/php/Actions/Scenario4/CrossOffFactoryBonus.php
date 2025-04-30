<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario4;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffFactoryBonus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_CROSS_OFF_FILLED_QUARTER_BONUS;
  }

  protected array $slots = [
    224,
    225,
    226,
    227,
  ];

  public function actCrossOffFactoryBonus(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffFactoryBonusAstra($player, $scribble);
  }
}
