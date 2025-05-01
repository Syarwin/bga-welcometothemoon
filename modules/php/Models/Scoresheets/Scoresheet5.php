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
      }
      // Otherwise, normal unbuilt section
      else {
        $sections[] = [$slotId, $parity];
      }
    }

    return $sections;
  }


  // PHASE 5
  public static function phase5Check(): void
  {
    // $fillingBonuses = [
    //   224 => ['factoryNumber' => 1, 'value' => 8],
    //   225 => ['factoryNumber' => 2, 'value' => 10],
    //   226 => ['factoryNumber' => 3, 'value' => 8],
    //   227 => ['factoryNumber' => 4, 'value' => 12],
    // ];
    // $scribbles = static::resolveRaceSlots();
    // foreach ($scribbles as $scribble) {
    //   $player = Players::get($scribble->getPId());
    //   $slot = $scribble->getSlot();
    //   $value = $fillingBonuses[$slot]['value'];
    //   $factoryNumber = $fillingBonuses[$slot]['factoryNumber'];
    //   Notifications::crossOffFillingBonus($player, $scribble, $value, $factoryNumber);
    // }
  }

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    return [];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case PLANNING:
        return [
          'type' => NODE_SEQ,
          'childs' => [
            $this->getStandardPlanningAction(),
            ['action' => S5_SPLIT_DOME]
          ]
        ];
      case ROBOT:
        return [
          'action' => S5_BUILD_DOME,
          'args' => ['parity' => $combination['number'] % 2],
        ];
      case ASTRONAUT:
        return $this->getStandardAstronautAction();
        //   case PLANT:
        //     return [
        //       'action' => S4_CIRCLE_PLANT_OR_WATER,
        //       'args' =>
        //       [
        //         'type' => PLANT,
        //         'slots' => $this->linkedPlants[$slot] ?? null,
        //       ]
        //     ];
        //   case WATER:
        //     return [
        //       'action' => S4_CIRCLE_PLANT_OR_WATER,
        //       'args' =>
        //       [
        //         'type' => WATER,
        //         'slots' => $this->linkedWater[$slot] ?? null,
        //       ]
        //     ];
        // case ENERGY:
    }
    return null;
  }

  public function prepareForPhaseFive(array $args)
  {
    // // Register for phase 5
    // $raceSlots = Globals::getRaceSlots();
    // $raceSlots[] = $args['slot'];
    // Globals::setRaceSlots($raceSlots);
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
    $data[] = ["slot" => 46, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 47,
      "score" => true,
      "overview" => "total",
      "v" => -$negativePoints,
    ];

    return $data;
  }
}
