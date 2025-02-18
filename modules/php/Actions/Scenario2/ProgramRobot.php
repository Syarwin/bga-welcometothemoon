<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class ProgramRobot extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_PROGRAM_ROBOT;
  }

  protected function getSlots(Player $player): array
  {
    return $player->scoresheet()->getSectionSlots('robots');
  }

  public function actProgramRobot(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $scribble = $scoresheet->addScribble($slot);
    Notifications::programRobot($player, $scribble);

    $multiplier = self::getMultiplierToAchieve($player, $slot);
    if (!is_null($multiplier)) {
      if ($scoresheet->hasScribbledSlot($multiplier)) {
        // Circling small multiplier instead
        $multiplier = self::$bigToSmallMultiplierMap[$multiplier];
      } else {
        // Register for phase 5
        $multipliers = Globals::getCircledMultipliers();
        $multipliers[] = $multiplier;
        Globals::setCircledMultipliers($multipliers);
      }
      $scribble = $scoresheet->addScribble($multiplier, SCRIBBLE_CIRCLE);
      Notifications::circleMultiplier($player, $scribble, self::getMultiplierValue($multiplier));
    }
  }

  /**
   * Find the big multiplier assigned to this robot slot id
   * @throws \BgaVisibleSystemException
   */
  private function getMultiplierToAchieve(Player $player, int $robotSlotId): int|null
  {
    $scoresheet = $player->scoresheet();
    $multiplierAffected = self::findMultiplierByRobot($robotSlotId);

    $scribbledMap = array_map(function ($slotId) use ($scoresheet) {
      return $scoresheet->hasScribbledSlot($slotId);
    }, self::$bigMultipliers[$multiplierAffected]);

    return in_array(false, $scribbledMap) ? null : $multiplierAffected;
  }

  private function findMultiplierByRobot(int $robotSlotId): int
  {
    foreach (self::$bigMultipliers as $multiplier => $robots) {
      if (in_array($robotSlotId, $robots)) {
        return $multiplier;
      }
    }
    throw new \BgaVisibleSystemException('findMultiplierByRobot: no multipliers found for slot id ' . $robotSlotId);
  }

  public static function getMultiplierValue(int $multiplierId): int
  {
    return self::$multipliersValues[$multiplierId];
  }

  private static array $bigMultipliers = [
    125 => [50, 51],
    126 => [52, 53, 54],
    127 => [55, 56, 57],
    128 => [58, 59, 60, 61],
  ];

  private static array $bigToSmallMultiplierMap = [
    125 => 129,
    126 => 130,
    127 => 131,
    128 => 132,
  ];

  private static array $multipliersValues = [
    125 => 6,
    126 => 9,
    127 => 8,
    128 => 10,
    129 => 5,
    130 => 7,
    131 => 6,
    132 => 7,
  ];
}
