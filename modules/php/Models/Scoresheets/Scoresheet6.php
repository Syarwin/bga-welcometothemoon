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
    switch ($combination['action']) {
      // case ENERGY:
      //   return ['action' => CIRCLE_ENERGY];
      // case ROBOT:
      //   return ['action' => PROGRAM_ROBOT];
      // case PLANT:
      //   return ['action' => CIRCLE_PLANT, 'args' => ['slot' => $slot]];
      // case WATER:
      //   return [
      //     'action' => CIRCLE_SINGLE_LINKED,
      //     'args' => [
      //       'slot' => $this->waterTanksAtSlots[$slot] ?? null,
      //       'values' => $this->waterTanksValues,
      //       'type' => CIRCLE_TYPE_WATER_S2,
      //     ]
      //   ];
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
