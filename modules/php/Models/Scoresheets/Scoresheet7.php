<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario7.php";

class Scoresheet7 extends Scoresheet
{
  protected int $scenario = 7;
  protected array $datas = DATAS7;
  protected array $numberBlocks = [
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
    [20, 21, 22, 23, 24, 25, 26],
    [27, 28, 29, 30, 31, 32],
    [33, 34, 35, 36, 37, 38],
    [39, 40, 41, 42, 43],
  ];

  private array $waterTanksAtSlots = [
    3 => 72,
    7 => 73,
    11 => 74,
    14 => 75,
    18 => 76,
    22 => 77,
    26 => 78,
    27 => 79,
    31 => 80,
    34 => 81,
    37 => 82,
    39 => 83,
    41 => 84
  ];

  private array $reactorsByStarship = [
    0 => [118, 117, 116],
    1 => [121, 120, 119],
    2 => [124, 123, 122],
    3 => [126, 125],
    4 => [128, 127],
    5 => [129],
  ];

  // Starship / Type / slots / links
  protected static array $blocks = [
    0 => [0, BLOCK_MODULE, [1, 2, 3, 4], [132 => 3, 133 => 4, 130 => 1]],
    1 => [0, BLOCK_GREENHOUSE, [5], [130 => 0, 131 => 2]],
    2 => [0, BLOCK_MODULE, [6, 7, 8, 9], [131 => 1, 134 => 6]],
    //
    3 => [1, BLOCK_MODULE, [10, 11, 12], [132 => 0, 135 => 4, 138 => 8]],
    4 => [1, BLOCK_MODULE, [13, 14, 15], [135 => 3, 133 => 0, 136 => 5, 139 => 9]],
    5 => [1, BLOCK_GREENHOUSE, [16], [136 => 4, 137 => 6]],
    6 => [1, BLOCK_MODULE, [17, 18, 19], [137 => 5, 134 => 2]],
    //
    7 => [2, BLOCK_GREENHOUSE, [20], [140 => 8]],
    8 => [2, BLOCK_MODULE, [21, 22, 23], [140 => 7, 138 => 3, 141 => 9, 142 => 10]],
    9 => [2, BLOCK_MODULE, [24, 25, 26], [141 => 8, 139 => 4, 143 => 12]],
    //
    10 => [3, BLOCK_MODULE, [27, 28], [142 => 8, 144 => 11, 146 => 13]],
    11 => [3, BLOCK_GREENHOUSE, [29], [144 => 10, 145 => 12]],
    12 => [3, BLOCK_MODULE, [30, 31, 32], [145 => 11, 143 => 9, 147 => 15]],
    //
    13 => [4, BLOCK_MODULE, [33, 34, 35], [146 => 10, 148 => 14, 150 => 16]],
    14 => [4, BLOCK_GREENHOUSE, [36], [148 => 13, 149 => 15]],
    15 => [4, BLOCK_MODULE, [37, 38], [149 => 14, 147 => 12, 151 => 17]],
    //
    16 => [5, BLOCK_MODULE, [39, 40], [150 => 13, 152 => 17]],
    17 => [5, BLOCK_MODULE, [41, 42], [152 => 16, 151 => 15, 153 => 18]],
    18 => [5, BLOCK_GREENHOUSE, [43], [153 => 17]],
  ];

  private array $greenhouses = [
    ['starship' => 0, 'plants' => [85, 86, 87], 'numberSlot' => 5, 'x2Slot' => 154, 'scoreSlot' => 62],
    ['starship' => 1, 'plants' => [88, 89, 90], 'numberSlot' => 16, 'x2Slot' => 155, 'scoreSlot' => 59],
    ['starship' => 2, 'plants' => [91, 92], 'numberSlot' => 20, 'x2Slot' => 156, 'scoreSlot' => 61],
    ['starship' => 3, 'plants' => [93, 94], 'numberSlot' => 29, 'x2Slot' => 157, 'scoreSlot' => 58],
    ['starship' => 4, 'plants' => [95, 96], 'numberSlot' => 36, 'x2Slot' => 158, 'scoreSlot' => 60],
    ['starship' => 5, 'plants' => [97], 'numberSlot' => 43, 'x2Slot' => 159, 'scoreSlot' => 57],
  ];

  public function getGreenhouses(): array
  {
    return $this->greenhouses;
  }

  public function getReactors()
  {
    return $this->reactorsByStarship;
  }

  public static function getBlockInfos(): array
  {
    $infos = [];
    foreach (static::$blocks as $blockId => $block) {
      $infos[$blockId] = [
        'starship' => $block[0],
        'type' => $block[1],
        'slots' => $block[2],
        'links' => $block[3]
      ];
    }
    return $infos;
  }

  public static function getConnectedBlocks(Scoresheet $scoresheet): array
  {
    $blockInfos = static::getBlockInfos();
    $blocks = [];
    $queue = [0, 2];
    while (!empty($queue)) {
      $blockId = array_shift($queue);
      if (in_array($blockId, $blocks)) continue;

      $blocks[] = $blockId;
      foreach ($blockInfos[$blockId]['links'] as $slot => $linkedBlockId) {
        if ($scoresheet->hasScribbledSlot($slot)) {
          $queue[] = $linkedBlockId;
        }
      }
    }

    return $blocks;
  }

  public function getAvailableSlotsForNumber(int $number, string $action)
  {
    $slots = parent::getAvailableSlotsForNumber($number, $action);
    if (empty($slots)) return [];

    // Slots much be linked by airlocks
    $validSlots = [];
    $blockInfos = static::getBlockInfos();
    foreach (static::getConnectedBlocks($this) as $blockId) {
      $validSlots = array_merge($validSlots, $blockInfos[$blockId]['slots']);
    }

    return array_values(array_intersect($slots, $validSlots));
  }

  public function getBlockBySlot(int $slot): ?array
  {
    foreach (static::getBlockInfos() as $block) {
      if (in_array($slot, $block['slots'])) {
        return $block;
      }
    }
    return null;
  }

  // PHASE 5
  public static function phase5Check(): void
  {
    $raceScribbles = static::resolveRaceSlots();
    foreach ($raceScribbles as $raceScribble) {
      Notifications::addScribble(null, $raceScribble);
    }
  }

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    $slot = $scribble->getSlot();

    // Number slots with robot bonus action
    $robotBonusSlots = [1, 8, 12, 19, 24, 28, 38, 40];
    $systemErrorsWithRobots = [69, 70];
    if (in_array($slot, [...$robotBonusSlots, ...$systemErrorsWithRobots])) {
      return ['action' => S7_ACTIVATE_AIRLOCK, 'args' => ['bonus' => true]];
    }

    // Check greenhouse x2 bonus
    $greenhousesNumberSlots = array_map(fn($gh) => $gh['numberSlot'], $this->greenhouses);
    if (in_array($slot, $greenhousesNumberSlots)) {
      return [
        'action' => S7_CIRCLE_GREENHOUSE_MULTIPLIER,
        'args' => ['slot' => $this->getGreenhouseByNumberSlot($slot)['x2Slot']]
      ];
    }

    if ($scribble->getNumber() === NUMBER_X && $methodSource == 'actWriteX') {
      return $this->getStandardPlanningReaction();
    }

    return [];
  }

  public function getCircledPlants(array $greenhouse): int
  {
    $builtInCircleCount = 3 - count($greenhouse['plants']);
    return $this->countScribbledSlots($greenhouse['plants']) + $builtInCircleCount;
  }

  public function isCompletelyNumbered(int $blockId): bool
  {
    $block = static::getBlockInfos()[$blockId];
    return $this->countScribbledSlots($block['slots']) === count($block['slots']);
  }

  private function getGreenhouseByNumberSlot($slot): ?array
  {
    foreach ($this->greenhouses as $greenhouse) {
      if ($greenhouse['numberSlot'] === $slot) {
        return $greenhouse;
      }
    }
    return null;
  }

  private array $astronautsSlots = [98, 99, 100, 101, 102, 103];
  protected array $jokers = [
    99 => 104,
    101 => 105,
    103 => 106,
  ];

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case ENERGY:
        $starshipNumber = static::getBlockBySlot($slot)['starship'];
        return [
          'action' => CIRCLE_NEXT_IN_ROW,
          'args' => [
            'slots' => $this->reactorsByStarship[$starshipNumber],
            'symbol' => CIRCLE_SYMBOL_REACTOR,
          ]
        ];
      case ROBOT:
        return ['action' => S7_ACTIVATE_AIRLOCK];
      case PLANT:
        $starshipNumber = static::getBlockBySlot($slot)['starship'];
        return [
          'action' => CIRCLE_NEXT_IN_ROW,
          'args' => [
            'slots' => $this->greenhouses[$starshipNumber]['plants'],
            'symbol' => CIRCLE_SYMBOL_PLANT,
          ]
        ];
      case WATER:
        return [
          'action' => CIRCLE_SINGLE_LINKED,
          'args' => [
            'slot' => $this->waterTanksAtSlots[$slot] ?? null,
            'type' => CIRCLE_TYPE_WATER_TANK,
          ]
        ];
      case ASTRONAUT:
        return $this->getStandardAstronautAction($this->jokers, $this->astronautsSlots);
      case PLANNING:
        return $this->getStandardPlanningAction();
    }
    return null;
  }

  /**
   * UI DATA
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
    $data[] = ["slot" => 47, "v" => $missionPoints];

    // Greenhouses
    $greenhousesScores = [];
    foreach ($this->greenhouses as $greenhouse) {
      if ($this->hasScribbledSlot($greenhouse['numberSlot'])) {
        $plantsScoreMap = [0 => 0, 1 => 2, 2 => 4, 3 => 7];
        $score = $plantsScoreMap[$this->getCircledPlants($greenhouse)];
        if ($this->hasScribbledSlot($greenhouse['x2Slot'], SCRIBBLE_CIRCLE)) {
          $score = $score * 2;
        }
        $data[] = ["slot" => $greenhouse['scoreSlot'], "v" => $score];
        $greenhousesScores[$greenhouse['scoreSlot']] = $score;
      }
    }

    $pairsOfGreenhousesMapping = [
      48 => [57, 60],
      49 => [58, 61],
      50 => [59, 62],
    ];
    $greenhousesTotal = 0;
    foreach ($pairsOfGreenhousesMapping as $scoreSlot => $slots) {
      $scoresPair = array_map(function ($slot) use ($greenhousesScores) {
        return $greenhousesScores[$slot] ?? 0;
      }, $slots);
      $data[] = ["slot" => $scoreSlot, "v" => array_sum($scoresPair)];
      $greenhousesTotal += array_sum($scoresPair);
    }
    $data[] = ["overview" => "plants", "v" => $greenhousesTotal];

    // Starships
    $blockInfos = array_filter(static::getBlockInfos(), function ($block) {
      return $block['type'] === BLOCK_MODULE;
    });
    $modulesScoreMap = [0 => 0, 1 => 2, 2 => 4, 3 => 7];
    $starshipsScoresMap = [];
    foreach ($blockInfos as $blockInfo) {
      $starship = $blockInfo['starship'];
      if ($this->countScribbledSlots($blockInfo['slots']) === count($blockInfo['slots'])) { // All are numbered
        if (!isset($starshipsScoresMap[$starship])) {
          $starshipsScoresMap[$starship] = 0;
        }
        // Find out how many reactors are circled
        $builtInCircleCount = 3 - count($this->reactorsByStarship[$starship]);
        $reactorsCircled = $this->countScribbledSlots($this->reactorsByStarship[$starship]) + $builtInCircleCount;
        $score = $modulesScoreMap[$reactorsCircled];

        // If a linked water tank circled - double the score
        $linkedWaterTankKey = array_values(array_intersect($blockInfo['slots'], array_keys($this->waterTanksAtSlots)))[0];
        $linkedWaterTankSlot = $this->waterTanksAtSlots[$linkedWaterTankKey];
        if ($this->hasScribbledSlot($linkedWaterTankSlot, SCRIBBLE_CIRCLE)) {
          $score = $score * 2;
        }
        $starshipsScoresMap[$starship] += $score;
      }
    }

    $starshipsTotal = 0;
    $scoreSlotsByStarship = [68, 65, 67, 64, 66, 63];
    $pairsOfStarshipsMapping = [
      51 => [4, 5],
      52 => [2, 3],
      53 => [0, 1],
    ];
    foreach ($pairsOfStarshipsMapping as $scoreSlot => $starshipsPair) {
      $scoresPair = array_map(function ($slot) use ($starshipsScoresMap) {
        return $starshipsScoresMap[$slot] ?? 0;
      }, $starshipsPair);
      foreach ($starshipsPair as $starshipId) {
        if (isset($starshipsScoresMap[$starshipId])) {
          $data[] = ["slot" => $scoreSlotsByStarship[$starshipId], "v" => $starshipsScoresMap[$starshipId]];
        }
      }
      $data[] = ["slot" => $scoreSlot, "v" => array_sum($scoresPair)];
      $starshipsTotal += array_sum($scoresPair);
    }
    $data[] = ["overview" => "starships", "v" => $starshipsTotal];

    // Planning
    $planningNegativePoints = 0;
    $planningMap = [107 => 1, 108 => 3, 109 => 6, 110 => 9, 111 => 12, 112 => 16, 113 => 20, 114 => 24, 115 => 28];
    foreach ($planningMap as $slot => $points) {
      if ($this->hasScribbledSlot($slot)) {
        $planningNegativePoints = $points;
      }
    }
    $data[] = ["slot" => 54, "v" => $planningNegativePoints];
    $data[] = ["overview" => "planning", "v" => -$planningNegativePoints];

    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 55, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 56,
      "score" => true,
      "overview" => "total",
      "v" => $missionPoints + $greenhousesTotal + $starshipsTotal - $planningNegativePoints - $negativePoints,
    ];

    return $data;
  }
}
