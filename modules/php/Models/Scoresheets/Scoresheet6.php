<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario6.php";

class Scoresheet6 extends Scoresheet
{
  protected int $scenario = 6;
  protected array $datas = DATAS6;
  protected array $numberBlocks = [
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    [11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
    [21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35],
    [36, 37, 38, 39, 40, 41, 42, 43, 44, 45],
    [46, 47, 48, 49, 50, 51, 52, 53, 54, 55]
  ];

  public static function getQuarters(): array
  {
    // Slot / score / numbers / energy / links
    return [
      0 => [231, 66, [1, 2, 3, 4, 5], [146, 147], [101 => 1, 99 => 4, 98 => 7]],
      1 => [232, 67, [6, 7, 8, 9, 10], [148, 149], [101 => 0, 100 => 4, 102 => 5, 103 => 2]],
      //
      2 => [233, 68, [11, 12, 13, 14, 15], [150, 151], [103 => 1, 104 => 3, 105 => 6, 118 => 5]],
      3 => [234, 69, [16, 17, 18, 19, 20], [152, 153], [104 => 2, 116 => 6, 110 => 10]],
      //
      4 => [235, 70, [21, 22, 23, 24, 25], [154, 155], [99 => 0, 100 => 1, 106 => 5, 107 => 8, 117 => 7]],
      5 => [236, 71, [26, 27, 28, 29, 30], [156, 157], [118 => 2, 102 => 1, 109 => 6, 111 => 9, 108 => 8, 106 => 4]],
      6 => [237, 72, [31, 32, 33, 34, 35], [158, 159], [109 => 5, 105 => 2, 116 => 3, 112 => 10, 115 => 9]],
      //
      7 => [238, 73, [36, 37, 38, 39, 40], [160, 161], [98 => 0, 117 => 4, 114 => 8]],
      8 => [238, 74, [41, 42, 43, 44, 45], [162, 163], [114 => 7, 107 => 4, 108 => 5, 119 => 9]],
      //
      9 => [239, 75, [46, 47, 48, 49, 50], [164, 165], [119 => 8, 111 => 5, 115 => 6, 113 => 10]],
      10 => [240, 76, [51, 52, 53, 54, 55], [166, 167], [113 => 9, 112 => 6, 110 => 3]],
    ];
  }


  // PHASE 5
  public static function phase5Check(): void {}
  public function prepareForPhaseFive(array $args) {}

  private array $astronautsSlots = [80, 81, 83, 84, 86, 87];
  private array $planningSlots = [89, 90, 92, 93, 95, 96];

  protected array $jokers = [
    81 => 82,
    84 => 85,
    87 => 88,
    90 => 91,
    93 => 94,
    96 => 97,
  ];

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    $quarter = intdiv($slot - 1, 5);

    switch ($combination['action']) {
      case ENERGY:
        return ['action' => S6_CIRCLE_ENERGY, 'args' => ['quarter' => $quarter]];
      case ROBOT:
        return ['action' => S6_CLOSE_WALKWAY];
      case PLANT:
      case WATER:
        return [
          'action' => S6_CIRCLE_SYMBOL,
          'args' => [
            'slot' => $slot,
            'type' => $combination['action'],
          ]
        ];
      case ASTRONAUT:
        return $this->getStandardAstronautAction($this->jokers, $this->astronautsSlots);
      case PLANNING:
        return $this->getStandardPlanningAction();
    }
    return null;
  }

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    if ($scribble->getNumber() === NUMBER_X && $methodSource == 'actWriteX') {
      return $this->getStandardPlanningReaction($this->jokers, $this->planningSlots);
    }

    return [];
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

    // // Missions
    // $missionPoints = $this->computeMissionsUiData($data);
    // $data[] = ["slot" => 37, "v" => $missionPoints];


    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 64, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 65,
      "score" => true,
      "overview" => "total",
      "v" => -$negativePoints,
    ];

    return $data;
  }
}
