<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario4.php";

class Scoresheet4 extends Scoresheet
{
  protected int $scenario = 4;
  protected array $datas = DATAS4;
  protected array $numberBlocks = [
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
    [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
    [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36],
  ];
  protected array $linkedResources = [
    // First row
    1 => ['slot' => 158, 'type' => CIRCLE_TYPE_PEARL],
    2 => ['slot' => 163, 'type' => CIRCLE_TYPE_PEARL],
    4 => ['slot' => 170, 'type' => CIRCLE_TYPE_RUBY],
    6 => ['slot' => 177, 'type' => CIRCLE_TYPE_RUBY],
    7 => ['slot' => 180, 'type' => CIRCLE_TYPE_RUBY],
    8 => ['slot' => 183, 'type' => CIRCLE_TYPE_RUBY],
    9 => ['slot' => 187, 'type' => CIRCLE_TYPE_PEARL],
    10 => ['slot' => 191, 'type' => CIRCLE_TYPE_RUBY],
    11 => ['slot' => 195, 'type' => CIRCLE_TYPE_PEARL],
    12 => ['slot' => 199, 'type' => CIRCLE_TYPE_PEARL],
    // Second row
    13 => ['slot' => 160, 'type' => CIRCLE_TYPE_RUBY],
    15 => ['slot' => 168, 'type' => CIRCLE_TYPE_PEARL],
    17 => ['slot' => 174, 'type' => CIRCLE_TYPE_RUBY],
    19 => ['slot' => 181, 'type' => CIRCLE_TYPE_RUBY],
    20 => ['slot' => 185, 'type' => CIRCLE_TYPE_PEARL],
    22 => ['slot' => 193, 'type' => CIRCLE_TYPE_PEARL],
    24 => ['slot' => 201, 'type' => CIRCLE_TYPE_PEARL],
    // Third row
    25 => ['slot' => 162, 'type' => CIRCLE_TYPE_PEARL],
    26 => ['slot' => 166, 'type' => CIRCLE_TYPE_RUBY],
    27 => ['slot' => 169, 'type' => CIRCLE_TYPE_RUBY],
    28 => ['slot' => 173, 'type' => CIRCLE_TYPE_RUBY],
    29 => ['slot' => 176, 'type' => CIRCLE_TYPE_PEARL],
    30 => ['slot' => 179, 'type' => CIRCLE_TYPE_RUBY],
    33 => ['slot' => 190, 'type' => CIRCLE_TYPE_RUBY],
    35 => ['slot' => 198, 'type' => CIRCLE_TYPE_PEARL],
    36 => ['slot' => 203, 'type' => CIRCLE_TYPE_RUBY],
    // Filling bonuses
    110 => ['slot' => 224, 'type' => CIRCLE_TYPE_FILLING_BONUS],
    98 => ['slot' => 225, 'type' => CIRCLE_TYPE_FILLING_BONUS],
    126 => ['slot' => 226, 'type' => CIRCLE_TYPE_FILLING_BONUS],
    142 => ['slot' => 227, 'type' => CIRCLE_TYPE_FILLING_BONUS],
  ];

  protected array $linkedWater = [
    2 => [164],
    4 => [171],
    8 => [184],
    11 => [196],
    14 => [164],
    16 => [171],
    17 => [175],
    19 => [182],
    20 => [184, 186],
    22 => [194],
    23 => [196],
    29 => [175],
    31 => [182],
    32 => [186],
    34 => [194],
  ];

  protected array $linkedPlants = [
    1 => [159],
    3 => [167],
    6 => [178],
    9 => [188],
    10 => [192],
    12 => [200],
    13 => [159, 161],
    14 => [165],
    15 => [167],
    16 => [172],
    18 => [178],
    21 => [188, 189],
    22 => [192],
    23 => [197],
    24 => [200, 202],
    25 => [161],
    26 => [165],
    28 => [172],
    33 => [189],
    35 => [197],
    36 => [202],
  ];

  protected array $factories = [
    PLANT => [
      'section' => 'plants',
      'mults' => [2, 4],
      'multSlot' => 218,
      'bonus' => 8,
      'bonusSlot' => 224,
      'UIitems' => 51,
      'UImult' => 52,
      'UIbonus' => 228,
      'UItotal' => 38,
    ],
    WATER => [
      'section' => 'waters',
      'mults' => [3, 5],
      'multSlot' => 219,
      'bonus' => 10,
      'bonusSlot' => 225,
      'UIitems' => 53,
      'UImult' => 54,
      'UIbonus' => 229,
      'UItotal' => 39,
    ],
    RUBY => [
      'section' => 'rubies',
      'mults' => [2, 3],
      'multSlot' => 220,
      'bonus' => 8,
      'bonusSlot' => 226,
      'UIitems' => 55,
      'UImult' => 56,
      'UIbonus' => 230,
      'UItotal' => 40,
    ],
    PEARL => [
      'section' => 'pearls',
      'mults' => [2, 3],
      'multSlot' => 221,
      'bonus' => 12,
      'bonusSlot' => 227,
      'UIitems' => 57,
      'UImult' => 58,
      'UIbonus' => 231,
      'UItotal' => 41,
    ],
    ASTRONAUT => [
      'section' => 'astronauts',
      'mults' => [0, 3],
      'multSlot' => 222,
      'UIitems' => 59,
      'UImult' => 60,
      'UItotal' => 42,
    ],
    PLANNING => [
      'section' => 'plannings',
      'mults' => [3, 0],
      'multSlot' => 223,
      'UIitems' => 61,
      'UImult' => 62,
      'UItotal' => 43,
    ],
  ];

  public function getFactorySection($type): string
  {
    return $this->factories[$type]['section'];
  }

  // PHASE 5
  public static function phase5Check(): void {}

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    $reactions = [];

    $slot = $scribble->getSlot();
    $circle = $this->linkedResources[$slot] ?? null;
    if (isset($circle)) {
      $reactions[] =
        [
          'action' => CIRCLE_SINGLE_LINKED,
          'args' => [
            'slot' => $circle['slot'],
            'type' => $circle['type'],
          ]
        ];
    }

    // PLANNING markers
    if ($scribble->getNumber() === NUMBER_X && $methodSource == 'actWriteX') {
      $reactions[] = $this->getStandardPlanningReaction();
    }

    // FACTORIES
    foreach ($this->factories as $type => $infos) {
      if (!in_array($slot, $this->getSectionSlots($infos['section']))) continue;
    }

    // EXTRACTION
    if (in_array($slot, $this->getSectionSlots('numbers'))) {
      for ($i = 0; $i < 12; $i++) {
        $column = [$this->numberBlocks[0][$i], $this->numberBlocks[1][$i], $this->numberBlocks[2][$i]];
        if (!in_array($slot, $column)) continue;
        if (!$this->hasScribbledSlots($column)) continue;

        $slots = [];
        foreach ($column as $slotId) {
          $res = $this->linkedResources[$slotId] ?? null;
          if (!is_null($res)) $slots[] = $res;

          $waters = $this->linkedWater[$slotId] ?? null;
          if (!is_null($waters)) {
            foreach ($waters as $water) {
              $slots[] = ['slot' => $water, 'type' => WATER];
            }
          }

          $plants = $this->linkedPlants[$slotId] ?? null;
          if (!is_null($plants)) {
            foreach ($plants as $plant) {
              $slots[] = ['slot' => $plant, 'type' => PLANT];
            }
          }
        }

        // Enforce flatten node here for more fluid UX
        return [
          [
            'type' => NODE_SEQ,
            'childs' => [
              ...$reactions,
              [
                'action' => S4_EXTRACT_RESOURCES,
                'args' => [
                  'column' => $i,
                  'slots' => $slots,
                ]
              ]
            ]
          ]
        ];
      }
    }

    return $reactions;
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case PLANNING:
        return $this->getStandardPlanningAction();
      case ASTRONAUT:
        return $this->getStandardAstronautAction();
      case PLANT:
        return [
          'action' => S4_CIRCLE_PLANT_OR_WATER,
          'args' =>
          [
            'type' => PLANT,
            'slots' => $this->linkedPlants[$slot] ?? null,
          ]
        ];
      case WATER:
        return [
          'action' => S4_CIRCLE_PLANT_OR_WATER,
          'args' =>
          [
            'type' => WATER,
            'slots' => $this->linkedWater[$slot] ?? null,
          ]
        ];
      case ROBOT:
      case ENERGY:
        return [
          'action' => S4_FACTORY_UPGRADE,
          'args' => ['type' => $combination['action']],
        ];
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
    $data[] = ["slot" => 37, "v" => $missionPoints];

    // Factories
    $factoryPoints = 0;
    $negativePoints = 0;
    foreach ($this->factories as $type => $infos) {
      // Items count
      $n = $this->countScribblesInSection($infos['section']);
      $data[] = ["slot" => $infos['UIitems'], "v" => $n];

      // Multiplier
      $mult = $infos['mults'][$this->hasScribbledSlot($infos['multSlot']) ? 1 : 0];
      $data[] = ["slot" => $infos['UImult'], "v" => $mult];
      $score = $n * $mult;

      // Bonus, if any
      if (isset($infos['bonus'])) {
        $bonus = $this->hasScribbledSlot($infos['bonusSlot']) ? $infos['bonus'] : 0;
        $data[] = ["slot" => $infos['UIbonus'], "v" => $bonus];
        $score += $bonus;
      }

      $data[] = ["slot" => $infos['UItotal'], "v" => $score];
      if ($type == PLANNING) {
        $negativePoints += $score;
        $data[] = ["overview" => $infos['section'], "v" => -$score];
      } else {
        $factoryPoints += $score;
        $data[] = ["overview" => $infos['section'], "v" => $score];
      }
    }

    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 43, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 44,
      "score" => true,
      "overview" => "total",
      "v" => $missionPoints, // + $plantsWaterAntennasPoints + $quartersPoints + $sectionMajorityPoints - $planningNegativePoints - $negativePoints
    ];

    return $data;
  }
}
