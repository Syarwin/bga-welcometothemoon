<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

class CirclePlant extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_CIRCLE_PLANT;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $slot = $this->getCtxArgs()['slot'];

    $stationsInSection = $this->getStationsInSection($scoresheet, $slot);
    $plantsAtStations = array_map(function ($stationSlot) {
      return self::$stationConnections[$stationSlot];
    }, $stationsInSection);

    return array_merge(...$plantsAtStations);
  }

  private function getStationsInSection(Scoresheet $scoresheet, int $slot): array
  {
    $section = $this->getSectionBySlot($scoresheet, $slot);
    return array_filter(
      array_keys(self::$stationConnections),
      fn($stationSlot) => in_array($stationSlot, $section)
    );
  }

  private function getSectionBySlot(Scoresheet $scoresheet, int $slot): array
  {
    $sections = $scoresheet->getIncreasingSequencesConstraints();
    foreach ($sections as $section) {
      if (in_array($slot, $section)) {
        return $section;
      }
    }

    throw new \BgaVisibleSystemException('getSectionBySlot: no section found for slot id ' . $slot);
  }

  public function actCirclePlant(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    $stationSlot = 0;
    foreach (self::$stationConnections as $station => $plantsSlots) {
      if (in_array($slot, $plantsSlots)) {
        $stationSlot = $station;
      }
    }

    Notifications::circlePlant($player, $scribble, self::$stationNumbers[$stationSlot]);
  }

  public static array $stationConnections = [
    6 => [74, 75, 76, 77, 78],
    16 => [79, 80, 81],
    21 => [82, 83, 84, 85],
    31 => [86, 87, 88, 89],
  ];

  public static array $stationNumbers = [
    6 => 1,
    16 => 2,
    21 => 3,
    31 => 4,
  ];
}
