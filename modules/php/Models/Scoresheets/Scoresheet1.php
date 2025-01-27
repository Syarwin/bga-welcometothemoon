<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario1.php";


class Scoresheet1 extends Scoresheet
{
  protected int $scenario = 1;
  protected array $datas = DATAS;

  protected array $increasingConstraints = [
    // ASTRONAUT
    [1, 2, 3],
    [4, 5, 6],
    // WATER
    [7, 8, 9, 10, 11],
    // ROBOT
    [12, 13, 14, 15, 16],
    [17, 18, 19, 20, 21],
    // PLANNING
    [22, 23, 24, 25, 26, 27],
    // ENERGY
    [28, 29, 30, 31, 32, 33, 34, 35, 36, 37],
    // PLANT
    [38, 39, 40, 41, 42, 43, 44, 45],
    // JOKER
    [46, 47, 48, 49, 50, 51, 52, 53]
  ];
  public function getAvailableSlotsForNumber(int $number, string $action): array
  {
    $allSlots = parent::getAvailableSlotsForNumber($number, $action);
    if ($action == JOKER) return $allSlots;

    // Row for each action (corresponding to increasing constraint)
    $mapping = [
      ASTRONAUT => [0, 1, 8],
      WATER => [2, 8],
      ROBOT => [3, 4, 8],
      PLANNING => [5, 8],
      ENERGY => [6, 8],
      PLANT => [7, 8],
    ];
    // Merge these slots
    $availableSlots = [];
    foreach ($mapping[$action] as $rowIndex) {
      $availableSlots = array_merge($availableSlots, $this->increasingConstraints[$rowIndex]);
    }
    // Take the intersection
    $allSlots = array_values(array_intersect($allSlots, $availableSlots));
    return $allSlots;
  }

  public function getScribbleReactions($scribble): array
  {
    $slot = $scribble->getSlot();

    // Number => check quarters
    if (1 <= $slot && $slot <= 53) {
      foreach ($this->quarters as $quarter) {
        if ($this->hasFilledQuarter($quarter['slots'], $slot)) {
          return $this->convertQuarterBonuses($quarter);
        }
      }
    }

    return [];
  }


  /**
   * Is quarter filled-up now thanks to scribble in slot $slot ??
   */
  protected function hasFilledQuarter($slots, $slot)
  {
    $inQuarter = false;
    foreach ($slots as $slot2) {
      if (!$this->hasScribbledSlot($slot2)) {
        return false;
      }

      if ($slot2 == $slot) $inQuarter = true;
    }

    return $inQuarter;
  }

  protected function convertQuarterBonuses($quarter)
  {
    $actions = [];
    foreach ($quarter['bonuses'] as $slot => $bonus) {
      $actions[] = [
        'action' => TAKE_BONUS,
        'args' => [
          'slot' => $slot,
          'bonus' => $bonus,
        ]
      ];
    }

    return $actions;
  }

  protected array $quarters = [
    // ASTRONAUT
    [
      'slots' => [1, 2, 3],
      'bonuses' => []
    ],
    [
      'slots' => [4, 5, 6],
      'bonuses' => []
    ],
    // WATER
    [
      'slots' => [7, 8],
      'bonuses' => []
    ],
    [
      'slots' => [9, 10, 11],
      'bonuses' => []
    ],
    // ROBOT
    [
      'slots' => [12, 13],
      'bonuses' => []
    ],
    [
      'slots' => [14, 15, 16],
      'bonuses' => [
        66 => [ROCKET => 3],
        //        72 => []
      ]
    ],
    [
      'slots' => [17, 18, 19, 20, 21],
      'bonuses' => []
    ],
    // PLANNING
    [
      'slots' => [22, 23, 24, 25, 26, 27],
      'bonuses' => []
    ],
    // ENERGY
    [
      'slots' => [28, 29, 30, 31, 32],
      'bonuses' => []
    ],
    [
      'slots' => [33, 34],
      'bonuses' => []
    ],
    [
      'slots' => [35, 36, 37],
      'bonuses' => []
    ],
    // PLANT
    [
      'slots' => [38, 39],
      'bonuses' => []
    ],
    [
      'slots' => [40, 41],
      'bonuses' => []
    ],
    [
      'slots' => [42, 43],
      'bonuses' => []
    ],
    [
      'slots' => [44, 45],
      'bonuses' => []
    ],
    // JOKER
    [
      'slots' => [46, 47],
      'bonuses' => []
    ],
    [
      'slots' => [48, 49],
      'bonuses' => []
    ],
    [
      'slots' => [50, 51],
      'bonuses' => []
    ],
    [
      'slots' => [52, 53],
      'bonuses' => []
    ],
  ];
}
