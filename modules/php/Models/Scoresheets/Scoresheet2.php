<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario2\CirclePlant;
use Bga\Games\WelcomeToTheMoon\Actions\Scenario2\ProgramRobot;
use Bga\Games\WelcomeToTheMoon\Actions\Scenario2\StirWaterTanks;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../constants.inc.php";
include_once dirname(__FILE__) . "/../../Material/Scenario2.php";


class Scoresheet2 extends Scoresheet
{
  protected int $scenario = 2;
  protected array $datas = DATAS2;

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case ENERGY:
        return ['action' => CIRCLE_ENERGY];
      case ROBOT:
        return ['action' => PROGRAM_ROBOT];
      case PLANT:
        return ['action' => CIRCLE_PLANT, 'args' => ['slot' => $slot]];
      case WATER:
        return ['action' => STIR_WATER_TANKS, 'args' => ['slot' => $slot]];
    }
    return null;
  }

  // NUMBER SLOTS IDS ARE 1 - 36
  // WALL SLOTS IDS ARE 90 - 124
  /**
   * getSections: based on current walls, compute the different sections
   */
  public function getSections()
  {
    $sections = [];
    $section = [1];
    for ($numberSlot = 2; $numberSlot <= 36; $numberSlot++) {
      $wallSlotId = 90 + ($numberSlot - 2);
      if ($this->hasScribbledSlot($wallSlotId)) {
        $sections[] = $section;
        $section = [$numberSlot];
      } else {
        $section[] = $numberSlot;
      }
    }
    $sections[] = $section;
    return $sections;
  }

  public function getNumberedSections()
  {
    $sections = [];
    foreach ($this->getSections() as $section) {
      if ($this->hasScribbledSlots($section)) {
        $sections[] = $section;
      }
    }

    return $sections;
  }

  public function getNumberedSectionsBySize()
  {
    $sizes = [];
    foreach ($this->getNumberedSections() as $section) {
      $sizes[count($section)][] = $section;
    }
    return $sizes;
  }

  public function getIncreasingSequencesConstraints()
  {
    return $this->getSections();
  }

  // PHASE 5
  public static function phase5Check(): void
  {
    $circledMultipliersIds = array_unique(Globals::getCircledMultipliers());
    if (count($circledMultipliersIds) > 0) {
      $players = Players::getAll();
      foreach ($circledMultipliersIds as $multiplierId) {
        /** @var Player $player */
        foreach ($players as $player) {
          $scoresheet = $player->scoresheet();
          if (!$scoresheet->hasScribbledSlot($multiplierId)) {
            $scribble = $scoresheet->addScribble($multiplierId);
            Notifications::crossOffMultiplier($player, $scribble, ProgramRobot::getMultiplierValue($multiplierId));
          }
        }
      }
    }

    Globals::setCircledMultipliers([]);
  }


  // DYNAMIC SLOTS
  public function computeUiData(): array
  {
    $data = [];

    // Missions
    $missionPoints = 0;
    foreach ($this->getSectionSlots('plans') as $slot) {
      $scribbles = $this->scribblesBySlots[$slot] ?? [];
      if (!empty($scribbles)) {
        $missionPoints += $scribbles[0]->getType();
      }
    }
    $data[] = ["slot" => 37, "v" => $missionPoints];

    // Stations TODO
    $stationPoints = 0;
    for ($station = 1; $station <= 4; $station++) {
      $mult = 0; // TODO
      $nPlants = 0; // TODO
      $points = $mult * $nPlants;
      $stationPoints += $points;
      $data[] = ["slot" => 168 + $station, "v" => $points];
    }
    $data[] = ["slot" => 38, "v" => $stationPoints];

    // Water
    $waterPoints = 0;
    foreach (StirWaterTanks::$waterTanksValues as $slot => $value) {
      if ($this->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
        $waterPoints += $value;
      }
    }
    $data[] = ["slot" => 39, "v" => $waterPoints];

    // Longest station
    $sectionsBySizes = $this->getNumberedSectionsBySize();
    $maxSectionSize = max(array_keys($sectionsBySizes));
    $data[] = ["slot" => 40, "v" => $maxSectionSize];

    // Most station TODO
    $sectionMajorityPoints = 0;
    $data[] = ["slot" => 41, "v" => $sectionMajorityPoints];


    // System errors
    $negativePoints = 0;
    foreach ($this->getSectionSlots('errors') as $slot) {
      if ($this->hasScribbledSlot($slot)) {
        $negativePoints += 5;
      }
    }
    $data[] = ["slot" => 42, "v" => $negativePoints];

    // Total score
    $data[] = [
      "slot" => 43,
      "score" => true,
      "v" => $missionPoints + $stationPoints + $waterPoints + $maxSectionSize + $sectionMajorityPoints - $negativePoints
    ];

    return $data;
  }
}
