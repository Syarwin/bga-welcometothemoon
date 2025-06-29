<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario3;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CrossOffFilledQuarterBonus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_CROSS_OFF_FILLED_QUARTER_BONUS;
  }

  protected array $slots = [
    55,
    56,
    57,
    58,
  ];

  // SHOULDNT BE NECESSARY
  // public function isDoable(Player $player): bool
  // {
  //   return !$player->scoresheet()->hasScribbledSomeSlots($this->slots, count($this->slots));
  // }

  public function actCrossOffFilledQuarterBonus(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffFilledQuarterBonusAstra($player, $scribble);
  }
}
