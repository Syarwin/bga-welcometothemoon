<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleEnergy extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isDoable(Player $player): bool
  {
    return !is_null($this->getNextSlot($player));
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function stCircleEnergy()
  {
    return [];
  }

  public function getDescription(): string
  {
    return clienttranslate('Circle an Energy symbol');
  }

  // Each entry is BOTTOM SLOT => [FIRST TOP SLOT, SECOND TOP SLOT] of an individual column
  public static array $slots = [
    135 => [133, 134],
    138 => [136, 137],
    141 => [139, 140],
    144 => [142, 143],
    147 => [145, 146],
    150 => [148, 149],
  ];

  // Find the next empty slot and whether it implies to build a wall or not
  public function getNextSlot(Player $player): ?array
  {
    $scoresheet = $player->scoresheet();
    foreach (self::$slots as $bonusSlot => $slots) {
      foreach ($slots as $i => $slotId) {
        if (!$scoresheet->hasScribbledSlot($slotId) && $slotId != 133) { // SLOT 133 is pre-scribbled
          return [$slotId, $i > 0, $bonusSlot];
        }
      }
    }

    return null;
  }

  public function actCircleEnergy()
  {
    $player = $this->getPlayer();
    list($slot, $mustBuildWall, $bonusSlot) = $this->getNextSlot($player);
    $scribble = $player->scoresheet()->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::circleEnergy($player, $scribble, $mustBuildWall);

    if ($mustBuildWall) {
      $this->insertAsChild([
        'action' => PLACE_ENERGY_WALL,
        'args' => ['slot' => $bonusSlot]
      ]);
    }
  }
}
