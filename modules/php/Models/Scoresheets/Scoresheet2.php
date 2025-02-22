<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario2\CircleOther;
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
      case ASTRONAUT:
        return ['action' => CIRCLE_OTHER, 'args' => ['actionType' => $combination['action']]];
      case PLANNING:
        return ['action' => WRITE_X, 'args' => [
          'actionType' => $combination['action'],
          'source' => ['name' => clienttranslate('Planning action')],
        ]];
    }
    return null;
  }

  // NUMBER SLOTS IDS ARE 1 - 36
  // WALL SLOTS IDS ARE 90 - 124
  /**
   * getSections: based on current walls, compute the different sections
   */
  public function getSections(): array
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

  public function getNumberedSections(): array
  {
    $sections = [];
    foreach ($this->getSections() as $section) {
      if ($this->hasScribbledSlots($section)) {
        $sections[] = $section;
      }
    }

    return $sections;
  }

  public function getNumberedSectionsBySize(): array
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

  public function getScribbleReactions($scribble): array
  {
    if ($scribble->getNumber() === NUMBER_X) {
      return [
        [
          'action' => CIRCLE_OTHER,
          'args' => ['actionType' => PLANNING]
        ]
      ];
    }
    if (in_array($scribble->getSlot(), [44, 45])) {
      return [['action' => CIRCLE_ENERGY]];
    }

    return [];
  }

  public function canUseJoker(): bool
  {
    return $this->getFirstUnscribbledJoker() !== null;
  }

  public function getFirstUnscribbledJoker(): int|null
  {
    foreach (CircleOther::$jokers as $jokerSlot) {
      if ($this->hasScribbledSlot($jokerSlot, SCRIBBLE_CIRCLE) && !$this->hasScribbledSlot($jokerSlot, SCRIBBLE)) {
        return $jokerSlot;
      }
    }
    return null;
  }

  public function getCompleteSectionsCount(): int
  {
    return count($this->getNumberedSections());
  }

  // DYNAMIC SLOTS

  /**
   * @throws \BgaVisibleSystemException
   */
  public function computeUiData(): array
  {
    $data = [];

    // Number of numbered slots
    $nNumberedSlots = $this->countScribblesInSection('numbers');
    $data[] = ["overview" => "numbers", "v" => $nNumberedSlots, 'max' => count($this->getSectionSlots('numbers'))];
    $data[] = ["panel" => "numbers", "v" => $nNumberedSlots];

    // Missions
    $missionPoints = $this->computeMissionsUiData($data);
    $data[] = ["slot" => 37, "v" => $missionPoints];

    // Stations
    $stationPoints = 0;
    for ($station = 1; $station <= 4; $station++) {
      $stationSlot = array_flip(CirclePlant::$stationNumbers)[$station];
      $plantsSlots = CirclePlant::$stationConnections[$stationSlot];
      $nPlants = $this->countScribbledSlots($plantsSlots);

      $bigMultiplier = array_keys(ProgramRobot::$bigToSmallMultiplierMap)[$station - 1];
      $stationMultipliers = [$bigMultiplier, ProgramRobot::$bigToSmallMultiplierMap[$bigMultiplier]];
      $circledMultipliersCount = $this->countScribbledSlots($stationMultipliers, SCRIBBLE_CIRCLE);
      if ($circledMultipliersCount > 1) {
        throw new \BgaVisibleSystemException("both station multipliers are circled for station {$station}, expected 0 or 1");
      }
      if ($circledMultipliersCount === 0) {
        $multiplier = 0;
      } else {
        $multiplierSlot = array_values(array_filter($stationMultipliers, function ($multiplier) {
          return $this->hasScribbledSlot($multiplier);
        }))[0];
        $multiplier = ProgramRobot::$multipliersValues[$multiplierSlot];
      }

      $points = $multiplier * $nPlants;
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
    $data[] = ["slot" => 39, "v" => $waterPoints, "overview" => "waters"];

    // Longest complete zone
    $sectionsBySizes = $this->getNumberedSectionsBySize();
    $maxSectionSize = empty($sectionsBySizes) ? 0 : max(array_keys($sectionsBySizes));
    $data[] = ["slot" => 40, "v" => $maxSectionSize, "overview" => "longest-section"];

    // Most zones complete
    $thisPlayerOrder = Players::getOrderNumberMostZonesComplete($this->player->getId());
    $sectionMajorityPoints = [
      0 => 0,
      1 => 20,
      2 => 10,
      3 => 5,
    ][$thisPlayerOrder];
    $data[] = ["slot" => 41, "v" => $sectionMajorityPoints];
    $data[] = ["overview" => "most-sections", "v" => $sectionMajorityPoints, "count" => 0];
    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 42, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / " . 3)];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 43,
      "score" => true,
      "overview" => "total",
      "v" => $missionPoints + $stationPoints + $waterPoints + $maxSectionSize + $sectionMajorityPoints - $negativePoints
    ];

    // Panel

    return $data;
  }
}
