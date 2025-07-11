<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario5.php";

class Scoresheet5 extends Scoresheet
{
  protected int $scenario = 5;
  protected array $datas = DATAS5;
  protected array $numberBlocks = [
    [1, 2, 3, 4, 5, 6, 7, 8, 9],
    [10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
    [20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
    [30, 31, 32, 33, 34, 35, 36, 37, 38],
  ];
  public static array $levels = [
    [
      'numbers' => [17, 26, 38],
      'plants' => [66, 67],
      'waters' => [82],
    ],
    [
      'numbers' => [9, 16, 25, 37],
      'plants' => [68, 69],
      'waters' => [83],
    ],
    [
      'numbers' => [8, 15, 24, 36],
      'plants' => [70, 71],
      'waters' => [84],
    ],
    [
      'numbers' => [7, 14, 23, 35],
      'plants' => [72, 73],
      'waters' => [85],
    ],
    [
      'numbers' => [6, 13, 22, 34],
      'plants' => [74, 75],
      'waters' => [86],
    ],
    [
      'numbers' => [5, 12, 21, 33],
      'plants' => [76, 77],
      'waters' => [87],
    ],
    [
      'numbers' => [4, 11, 20, 32],
      'plants' => [78, 79],
      'waters' => [88],
    ],
    [
      'numbers' => [3, 10, 31],
      'plants' => [80, 81],
      'waters' => [89],
    ],
  ];
  protected array $subdomes = [
    114 => [122, 123],
    115 => [124, 125],
    116 => [126, 127],
    117 => [128, 129],
    118 => [130, 131],
    119 => [132, 133],
    120 => [134, 135],
    121 => [136, 137],
  ];
  protected array $multipliers = [
    'planFirst' => [
      'mults' => [138 => 8, 140 => 10, 142 => 12, 144 => 15],
      'maxMult' => 18,
    ],
    'planSecond' => [
      'mults' => [139 => 4, 141 => 6, 143 => 8, 145 => 9],
      'maxMult' => 10,
    ],
    'waterPlant1' => [
      'mults' => [146 => 2, 149 => 2, 152 => 3, 155 => 3],
      'maxMult' => 4,
    ],
    'waterPlant2' => [
      'mults' => [147 => 5, 150 => 6, 153 => 7, 156 => 8],
      'maxMult' => 10,
    ],
    'waterPlant3' => [
      'mults' => [148 => 9, 151 => 10, 154 => 12, 157 => 15],
      'maxMult' => 18,
    ],
    'astronautFirst' => [
      'mults' => [158 => -10, 160 => 0, 162 => 10, 164 => 20],
      'maxMult' => 30,
    ],
    'astronautSecond' => [
      'mults' => [159 => 0, 161 => 2, 163 => 4, 165 => 7],
      'maxMult' => 10,
    ],
    'dome' => [
      'mults' => [166 => 6, 167 => 5, 168 => 4, 169 => 3],
      'maxMult' => 2,
    ],
  ];
  public static array $planSymbols = [
    51 => [182, 183],
    52 => [184, 185],
    53 => [186, 187]
  ];

  public function getAvailableSlotsForNumber(int $number, string $action)
  {
    $slots = parent::getAvailableSlotsForNumber($number, $action);
    if (empty($slots)) return [];

    // Slots much be touching a previous number, or be on base ground
    $neighbourSlots = [6, 7, 13, 14, 22, 23, 34, 35];
    foreach ($this->numberBlocks as $skyscraper) {
      foreach ($skyscraper as $i => $slotId) {
        $scribble = $this->scribblesBySlots[$slotId][0] ?? null;
        if (is_null($scribble)) continue;

        if ($i > 0) $neighbourSlots[] = $skyscraper[$i - 1];
        if ($i < count($skyscraper) - 1) $neighbourSlots[] = $skyscraper[$i + 1];
      }
    }

    return array_values(array_intersect($slots, $neighbourSlots));
  }

  public function getUnbuiltDomeSections(): array
  {
    $sections = [];
    foreach ($this->getSectionSlots('domes') as $i => $slotId) {
      $parity = ($i + 1) % 2;

      // Normal line => it's built
      if ($this->hasScribbledSlot($slotId, SCRIBBLE_LINE)) continue;

      // Orthogonal line => check subsections
      if ($this->hasScribbledSlot($slotId, SCRIBBLE_LINE_ORTHOGONAL)) {
        foreach ($this->subdomes[$slotId] as $slot) {
          if (!$this->hasScribbledSlot($slot)) {
            $sections[] = [$slot, $parity];
          }
        }
      } // Otherwise, normal unbuilt section
      else {
        $sections[] = [$slotId, $parity];
      }
    }

    return $sections;
  }

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    $slot = $scribble->getSlot();
    if (!in_array($slot, $this->getSectionSlots('numbers'))) return [];

    $reactions = [];

    // PLANNING markers
    if ($scribble->getNumber() === NUMBER_X && $methodSource == 'actWriteX') {
      $reactions[] = ['action' => S5_SPLIT_DOME];
    }


    // Check full skyscraper
    if (in_array($slot, [1, 9, 10, 19, 20, 29, 30, 38])) {
      $reactions[] = [
        'action' => S5_FILLED_SKYSCRAPER,
        'args' => ['slot' => $slot]
      ];
    }

    return [
      'type' => NODE_SEQ,
      'childs' => $reactions
    ];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case PLANNING:
        return $this->getStandardPlanningAction();
      case ROBOT:
        return [
          'action' => S5_BUILD_DOME,
          'args' => ['parity' => $combination['number'] % 2],
        ];
      case ASTRONAUT:
        return $this->getStandardAstronautAction();
      case ENERGY:
        return [
          'action' => S5_ENERGY_UPGRADE
        ];

      case PLANT:
      case WATER:
        $action = $combination['action'];
        $symbol = $action == PLANT ? CIRCLE_SYMBOL_PLANT : CIRCLE_SYMBOL_WATER;
        foreach (self::$levels as $level) {
          if (in_array($slot, $level['numbers'])) {
            return [
              'action' => CIRCLE_NEXT_IN_ROW,
              'args' => [
                'slots' => $level[$action == PLANT ? 'plants' : 'waters'],
                'symbol' => $symbol,
              ],
            ];
          }
        }
        return null;
    }
    return null;
  }

  public function prepareForPhaseFive(array $args)
  {
    // Register for phase 5
    $raceSlots = Globals::getRaceSlots();
    $raceSlots[] = $args['slot'];
    Globals::setRaceSlots($raceSlots);
  }

  // PHASE 5
  public static function phase5Check(): void
  {
    $fillingBonuses = [
      92 => [1, 20],
      90 => [1, 6],
      96 => [2, 10],
      94 => [2, 20],
      100 => [3, 6],
      98 => [3, 25],
      104 => [4, 15],
      102 => [4, 10],
    ];

    $scribbles = static::resolveRaceSlots();
    foreach ($scribbles as $scribble) {
      $player = Players::get($scribble->getPId());
      $slot = $scribble->getSlot();
      [$value, $skyscraper] = $fillingBonuses[$slot];
      Notifications::crossOffSkyscraperFillingBonus($player, $scribble, $value, $skyscraper);
    }
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
    $planFirstSlots = [182, 184, 186];
    $nPlanFirst = $this->countScribbledSlots($planFirstSlots, SCRIBBLE_CIRCLE);
    $multPlanFirst = $this->getMultiplierOfType('planFirst');

    $planSecondSlots = [183, 185, 187];
    $nPlanSecond = $this->countScribbledSlots($planSecondSlots, SCRIBBLE_CIRCLE);
    $multPlanSecond = $this->getMultiplierOfType('planSecond');

    $missionPoints = $nPlanFirst * $multPlanFirst + $nPlanSecond * $multPlanSecond;
    $data[] = ["slot" => 39, "v" => $missionPoints];

    $stacks = ['stack-A', 'stack-B', 'stack-C'];
    foreach ($stacks as $i => $stack) {
      $missionPoint = 0;
      if ($this->hasScribbledSlot($planFirstSlots[$i], SCRIBBLE_CIRCLE)) $missionPoint = $multPlanFirst;
      if ($this->hasScribbledSlot($planSecondSlots[$i], SCRIBBLE_CIRCLE)) $missionPoint = $multPlanSecond;
      $data[] = ['overview' => $stack, 'v' => $missionPoint];
    }

    // Water/plants
    $waterPlantCounts = [0, 0, 0, 0];
    foreach (self::$levels as $level) {
      $slots = array_merge($level['plants'], $level['waters']);
      $n = $this->countScribbledSlots($slots);
      $waterPlantCounts[$n]++;
    }
    $data[] = ["slot" => 54, "v" => $waterPlantCounts[1]];
    $data[] = ["slot" => 55, "v" => $waterPlantCounts[2]];
    $data[] = ["slot" => 56, "v" => $waterPlantCounts[3]];

    $waterPlantMult1 = $this->getMultiplierOfType('waterPlant1');
    $scoreWaterPlant1 = $waterPlantCounts[1] * $waterPlantMult1;
    $data[] = ["slot" => 40, "v" => $scoreWaterPlant1];

    $waterPlantMult2 = $this->getMultiplierOfType('waterPlant2');
    $scoreWaterPlant2 = $waterPlantCounts[2] * $waterPlantMult2;
    $data[] = ["slot" => 41, "v" => $scoreWaterPlant2];

    $waterPlantMult3 = $this->getMultiplierOfType('waterPlant3');
    $scoreWaterPlant3 = $waterPlantCounts[3] * $waterPlantMult3;
    $data[] = ["slot" => 42, "v" => $scoreWaterPlant3];

    $scoreWaterPlants = $scoreWaterPlant1 + $scoreWaterPlant2 + $scoreWaterPlant3;
    $data[] = ['overview' => 'waterplants', 'v' => $scoreWaterPlants];

    // Scryscraper
    $scoreSkyscraper = 0;
    $fillingBonuses = [
      57 => [90 => 6, 91 => 3],
      58 => [92 => 20, 93 => 10],
      59 => [94 => 20, 95 => 10],
      60 => [96 => 10, 97 => 5],
      61 => [98 => 25, 99 => 12],
      62 => [100 => 6, 101 => 3],
      63 => [102 => 10, 103 => 5],
      64 => [104 => 15, 105 => 7],
    ];
    foreach ($fillingBonuses as $slot => $bonuses) {
      $bonus = 0;
      foreach ($bonuses as $bonusSlot => $bonusValue) {
        if ($this->hasScribbledSlot($bonusSlot, SCRIBBLE_CIRCLE)) {
          $bonus = $bonusValue;
        }
      }
      $scoreSkyscraper += $bonus;
      $data[] = ["slot" => $slot, "v" => $bonus > 0 ? $bonus : ""];
    }
    $data[] = ["slot" => 43, "v" => $scoreSkyscraper];
    $data[] = ['overview' => 'skyscrapers', 'v' => $scoreSkyscraper];

    // Most astronauts
    [$thisPlayerOrder, $nAstronauts] = self::getMostAstronautsRankAndAmount($this->player->getId());
    $sectionMajorityPoints = $thisPlayerOrder == 1 ? $this->getMultiplierOfType('astronautFirst') : $this->getMultiplierOfType('astronautSecond');
    if ($thisPlayerOrder == 0) $sectionMajorityPoints = 0;
    $data[] = ["slot" => 44, "v" => $sectionMajorityPoints];
    $data[] = ["overview" => "astronaut", "v" => $sectionMajorityPoints, "details" => $nAstronauts];

    // Missing domes
    $nUnbuiltDomes = count($this->getUnbuiltDomeSections());
    $data[] = ["slot" => 65, "v" => $nUnbuiltDomes];
    $negativeDomePoints = $nUnbuiltDomes * $this->getMultiplierOfType('dome');
    $data[] = ["slot" => 45, "v" => $negativeDomePoints];
    $data[] = ['overview' => 'domes', 'v' => -$negativeDomePoints, "details" => $nUnbuiltDomes];

    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 46, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 47,
      "score" => true,
      "overview" => "total",
      "v" => $missionPoints + $scoreWaterPlants + $scoreSkyscraper + $sectionMajorityPoints - $negativeDomePoints - $negativePoints,
    ];

    return $data;
  }
}
