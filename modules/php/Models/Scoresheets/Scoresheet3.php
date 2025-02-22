<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../constants.inc.php";
include_once dirname(__FILE__) . "/../../Material/Scenario3.php";


class Scoresheet3 extends Scoresheet
{
  protected int $scenario = 3;
  protected array $datas = DATAS3;
  protected array $increasingConstraints = [
    // Vertical
    [1, 2, 3, 4, 5],
    [6, 7, 8, 9, 10, 11],
    [12, 13, 14, 15, 16],
    [12, 13, 14, 15, 16],
    [17, 18, 19, 20, 21],
    [22, 23, 24, 25, 26, 27],
    [28, 29, 30, 31, 32],
    // Horizontal
    [6, 12, 17, 22, 28],
    [1, 7, 13, 18, 23, 29],
    [2, 8, 14, 24, 30],
    [3, 9, 19, 25, 31],
    [4, 10, 15, 20, 26, 32],
    [5, 11, 16, 21, 27],
  ];

  private array $waterTanksAtSlots = [
    5 => 113,
    15 => 114,
    26 => 115,
    31 => 116,
    8 => 117,
    13 => 118,
    23 => 119,
    17 => 120,
  ];

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case WATER:
        return ['action' => STIR_WATER_TANKS, 'args' => ['slot' => $slot, 'waterTanksSlots' => $this->waterTanksAtSlots]];
    }
    return null;
  }
}
