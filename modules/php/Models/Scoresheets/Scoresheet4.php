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
}
