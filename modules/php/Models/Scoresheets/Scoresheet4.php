<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario4.php";

class Scoresheet4 extends Scoresheet
{
  protected int $scenario = 4;
  protected array $datas = DATAS4;
  protected array $increasingConstraints = [
    // Vertical
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
    [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
    [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36],
  ];

  // PHASE 5
  public static function phase5Check(): void {}
}
