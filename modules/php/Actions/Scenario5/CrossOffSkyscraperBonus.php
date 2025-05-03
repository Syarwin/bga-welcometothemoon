<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario5;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffSkyscraperBonus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S5_CROSS_OFF_SKYSCRAPER_BONUS;
  }

  protected array $slots = [
    90,
    92,
    94,
    96,
    98,
    100,
    102,
    104,
  ];

  public function actCrossOffSkyscraperBonus(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffSkyscraperBonusAstra($player, $scribble);
  }
}
