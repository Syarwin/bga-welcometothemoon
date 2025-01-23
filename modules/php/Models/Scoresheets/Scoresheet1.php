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
  public function getAvailableSlotsForNumber(int $number, string $action)
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


  protected array $quarters = [
    // ASTRONAUT
    [
      'slots' => [1, 2, 3],
      'bonus' => []
    ],
    [
      'slots' => [4, 5, 6],
      'bonus' => []
    ],
    // WATER
    [
      'slots' => [7, 8],
      'bonus' => []
    ],
    [
      'slots' => [9, 10, 11],
      'bonus' => []
    ],
    // ROBOT
    [
      'slots' => [12, 13],
      'bonus' => []
    ],
    [
      'slots' => [14, 15, 16],
      'bonus' => []
    ],
    [
      'slots' => [17, 18, 19, 20, 21],
      'bonus' => []
    ],
    // PLANNING
    [
      'slots' => [22, 23, 24, 25, 26, 27],
      'bonus' => []
    ],
    // ENERGY
    [
      'slots' => [28, 29, 30, 31, 32],
      'bonus' => []
    ],
    [
      'slots' => [33, 34],
      'bonus' => []
    ],
    [
      'slots' => [35, 36, 37],
      'bonus' => []
    ],
    // PLANT
    [
      'slots' => [38, 39],
      'bonus' => []
    ],
    [
      'slots' => [40, 41],
      'bonus' => []
    ],
    [
      'slots' => [42, 43],
      'bonus' => []
    ],
    [
      'slots' => [44, 45],
      'bonus' => []
    ],
    // JOKER
    [
      'slots' => [46, 47],
      'bonus' => []
    ],
    [
      'slots' => [48, 49],
      'bonus' => []
    ],
    [
      'slots' => [50, 51],
      'bonus' => []
    ],
    [
      'slots' => [52, 53],
      'bonus' => []
    ],
  ];
}
