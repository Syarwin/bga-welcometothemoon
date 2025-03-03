<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario3\BuildRobotTunnel;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Quarter;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;

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

  public static $grid = [
    [5, 170, 11, 176, 16, 182, 21, 188, 27, 194, TOP_RIGHT_CORNER_SLOT],
    [140, 0, 145, 0, 150, 0, 155, 0, 160, 0, 165],
    [4, 169, 10, 175, 15, 181, 20, 187, 26, 193, 32],
    [139, 0, 144, 0, 149, 0, 154, 0, 159, 0, 164],
    [3, 168, 9, 174, -1, 180, 19, 186, 25, 192, 31],
    [138, 0, 143, 0, 148, 0, 153, 0, 158, 0, 163],
    [2, 167, 8, 173, 14, 179, -1, 185, 24, 191, 30],
    [137, 0, 142, 0, 147, 0, 152, 0, 157, 0, 162],
    [1, 166, 7, 172, 13, 178, 18, 184, 23, 190, 29],
    [-1, 0, 141, 0, 146, 0, 151, 0, 156, 0, 161],
    [-1, -1, 6, 171, 12, 177, 17, 183, 22, 189, 28],
  ];

  public static function getQuarters(): array
  {
    return array_map(function ($data) {
      return new Quarter($data);
    }, [
      [0, clienttranslate('top-left'), [3, 4, 5, 9, 10, 11, 15, 16], [[63], [64], [65], [66, 67]], [55, 59]],
      [1, clienttranslate('top-right'), [19, 20, 21, 25, 26, 27, 31, 32], [[68], [69], [70], [71, 72]], [56, 60]],
      [2, clienttranslate('bottom-left'), [1, 2, 6, 7, 8, 12, 13, 14], [[73], [74], [75], [76, 77]], [57, 61]],
      [3, clienttranslate('bottom-right'), [17, 18, 22, 23, 24, 28, 29, 30], [[78], [79], [80], [81, 82]], [58, 62]],
    ]);
  }

  /**
   * @throws \BgaUserException
   */
  public static function getQuarterOfSlot(int $slot): Quarter
  {
    foreach (self::getQuarters() as $quarter) {
      if ($quarter->hasSlot($slot)) {
        return $quarter;
      }
    }
    throw new \BgaUserException("Quarter has not been found for slot {$slot}. Should not happen.");
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case WATER:
        return [
          'action' => CIRCLE_SINGLE_LINKED,
          'args' => [
            'slot' => $this->waterTanksAtSlots[$slot] ?? null,
            'type' => CIRCLE_TYPE_WATER,
          ]
        ];
      case PLANT:
        $quarterId = self::getQuarterOfSlot($slot)->getId();
        return ['action' => CIRCLE_GREENHOUSE, 'args' => ['quarterId' => $quarterId]];
      case ENERGY:
        return ['action' => IMPROVE_BONUS];
      case PLANNING:
        return [
          'action' => WRITE_X,
          'args' => [
            'source' => [
              'name' => clienttranslate('Planning action'),
            ],
          ]
        ];
      case ROBOT:
        return ['action' => BUILD_ROBOT_TUNNEL];
      case ASTRONAUT:
        return [
          'action' => CIRCLE_NEXT_IN_ROW,
          'args' => ['actionType' => ASTRONAUT, 'slots' => $this->getSectionSlots('astronautmarkers')]
        ];
    }
    return null;
  }

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    $slot = $scribble->getSlot();
    if (!in_array($slot, $this->getSectionSlots('numbers'))) return [];

    // Check antennas
    $scribbles = BuildRobotTunnel::scribblesConnectedAntennas($this);
    if (!empty($scribbles)) {
      Notifications::circleAntennas($this->player, $scribbles);
    }

    $reactions = [];

    // Check full quarter
    $quarter = self::getQuarterOfSlot($slot);
    if ($this->hasScribbledSlots($quarter->getSlots())) {
      $reactions[] = [
        'action' => FILLED_QUARTER,
        'args' => ['quarterId' => $quarter->getId()]
      ];
    }

    // PLANNING markers
    if ($scribble->getNumber() === NUMBER_X && $methodSource == 'actWriteX') {
      $reactions[] = [
        'action' => CIRCLE_NEXT_IN_ROW,
        'args' => [
          'actionType' => PLANNING,
          'slots' => $this->getSectionSlots('planningmarkers'),
        ]
      ];
    }

    return $reactions;
  }


  // PHASE 5
  public static function phase5Check(): void
  {
    $filledQuartersIds = array_unique(Globals::getFilledQuarters());
    if (count($filledQuartersIds) > 0) {
      $players = Players::getAll();
      foreach ($filledQuartersIds as $quarterId) {
        $quarter = self::getQuarters()[$quarterId];
        $slotId = $quarter->getPointsSlots()[0];

        $affectedPlayers = [];
        $scribbles = [];
        foreach ($players as $player) {
          $scoresheet = $player->scoresheet();
          if (!$scoresheet->hasScribbledSlot($slotId)) {
            $scribbles[] = $scoresheet->addScribble($slotId);
            $affectedPlayers[] = $player;
          }
        }

        if (!empty($affectedPlayers)) {
          Notifications::crossOffQuarterPoints($affectedPlayers, $scribbles, $quarter);
        }
      }
    }

    Globals::setFilledQuarters([]);
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
    $data[] = ["slot" => 33, "v" => $missionPoints];

    // Plants/Water/Antennas
    $plantsWaterAntennasPoints = 0;
    $scoringCategories = [
      [
        'section' => 'plants',
        'slotNbr' => 48,
        'slotPoints' => 34,
        'slotsMult' => [101 => 1, 102 => 2, 103 => 3, 104 => 4],
      ],
      [
        'section' => 'waters',
        'slotNbr' => 49,
        'slotPoints' => 35,
        'slotsMult' => [105 => 2, 106 => 4, 107 => 6, 108 => 8],
      ],
      [
        'section' => 'antennas',
        'slotNbr' => 50,
        'slotPoints' => 36,
        'slotsMult' => [109 => 1, 110 => 2, 111 => 4, 112 => 6],
      ]
    ];
    foreach ($scoringCategories as $category) {
      // Amount of scribbles
      $n = $this->countScribblesInSection($category['section']);
      $data[] = ["slot" => $category['slotNbr'], "v" => $n];
      // Multiplier
      $mult = 0;
      foreach ($category['slotsMult'] as $slot => $m) {
        if ($this->hasScribbledSlot($slot)) $mult = $m;
      }
      // Points
      $points = $n * $mult;
      $data[] = ["slot" => $category['slotPoints'], "v" => $points, "overview" => $category['section']];
      $plantsWaterAntennasPoints += $points;
    }

    // Quarters
    $quarters = $this->getQuarters();
    $quartersPoints = 0;
    $nQuartersFilled = 0;
    foreach ([51, 52, 53, 54] as $quarterId => $slotScore) {
      $points = 0;
      foreach ($quarters[$quarterId]->getPointsSlots() as $i => $slot) {
        if ($this->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
          $points = $i == 0 ? 15 : 5;
          $nQuartersFilled++;
        }
      }
      $data[] = ["slot" => $slotScore, "v" => $points];
      $quartersPoints += $points;
    }
    $data[] = ["slot" => 37, "v" => $quartersPoints, "overview" => 'quarters', "details" => "$nQuartersFilled / 4"];

    // Most astronauts
    [$thisPlayerOrder, $nAstronauts] = self::getMostAstronautsRankAndAmount($this->player->getId());
    $sectionMajorityPoints = [0 => 0, 1 => 20, 2 => 10, 3 => 5][$thisPlayerOrder];
    $data[] = ["slot" => 38, "v" => $sectionMajorityPoints];
    $data[] = ["overview" => "astronaut", "v" => $sectionMajorityPoints, "details" => $nAstronauts];

    // Planning
    $planningNegativePoints = 0;
    $planningMap = [83 => 1, 84 => 3, 85 => 6, 86 => 9, 87 => 12, 88 => 16, 89 => 20, 90 => 24, 91 => 28];
    foreach ($planningMap as $slot => $points) {
      if ($this->hasScribbledSlot($slot)) {
        $planningNegativePoints = $points;
      }
    }
    $data[] = ["slot" => 39, "v" => $planningNegativePoints];
    $data[] = ["overview" => "planning", "v" => -$planningNegativePoints];

    // System errors
    $scribbledErrors = $this->countScribblesInSection('errors');
    $negativePoints = 5 * $scribbledErrors;
    $data[] = ["slot" => 40, "v" => $negativePoints];
    $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    $data[] = [
      "slot" => 41,
      "score" => true,
      "overview" => "total",
      "v" => $missionPoints + $plantsWaterAntennasPoints + $quartersPoints + $sectionMajorityPoints - $planningNegativePoints - $negativePoints
    ];

    return $data;
  }

  public static function getMostAstronautsRankAndAmount(int $pId): array
  {
    $astronauts = [];
    /** @var Player $player */
    foreach (Players::getAll() as $player) {
      $astronautsCount = $player->scoresheet()->countScribblesInSection('astronautmarkers');
      if ($astronautsCount > 0) {
        $astronauts[$player->getId()] = $astronautsCount;
      }
    }
    if (Globals::isSolo()) {
      $astra = Players::getAstra();
      $astronauts['astra'] = $astra->getCardsByActionMap()[ASTRONAUT];
    }

    return Utils::getRankAndAmountOfKey($astronauts, $pId);
  }
}
