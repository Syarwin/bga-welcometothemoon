<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario8.php";

class Scoresheet8 extends Scoresheet
{

  protected int $scenario = 8;
  protected array $datas = DATAS8;
  protected array $planets = [
    ['slots' => [1, 2, 3], 'type' => PLANET_TYPE_GREEN, 'moonSlots' => [79, 80], 'flag' => 93, 'final' => 223],
    ['slots' => [4, 5, 6, 7, 8], 'type' => PLANET_TYPE_GREY, 'moonSlots' => [81, 82], 'flag' => 94, 'final' => 224],
    ['slots' => [9, 10, 11, 12, 13], 'type' => PLANET_TYPE_BLUE, 'moonSlots' => [83, 84], 'flag' => 95, 'final' => 225],
    [
      'slots' => [14, 15, 16, 17, 18, 19, 20],
      'type' => PLANET_TYPE_GREY,
      'moonSlots' => [85, 86],
      'flag' => 96,
      'final' => 226
    ],
    [
      'slots' => [21, 22, 23, 24, 25],
      'type' => PLANET_TYPE_GREEN,
      'moonSlots' => [87, 88],
      'flag' => 97,
      'final' => 227
    ],
    [
      'slots' => [26, 27, 28, 29, 30],
      'type' => PLANET_TYPE_BLUE,
      'moonSlots' => [89, 90],
      'flag' => 98,
      'final' => 228
    ],
    ['slots' => [31, 32, 33], 'type' => PLANET_TYPE_GREEN, 'moonSlots' => [91, 92], 'flag' => 99, 'final' => 229],
  ];
  protected array $allowedBlocksByNumber = [
    0 => [0],
    1 => [0],
    2 => [0],
    3 => [0, 1],
    4 => [1],
    5 => [1, 2],
    6 => [2],
    7 => [2, 3],
    8 => [3],
    9 => [3, 4],
    10 => [4],
    11 => [4, 5],
    12 => [5],
    13 => [5, 6],
    14 => [6],
    15 => [6],
    16 => [6],
    17 => [6]
  ];

  protected array $planetsPlants = [[181], [184], [185, 186], [188, 189], [191, 192], [194, 195], [197, 198]];
  protected array $planetsWaters = [[182], [183], [187], [190], [193], [196], []];

  protected array $asteroidsBonuses = [
    51 => [63],
    52 => [64],
    53 => [65],
    54 => [66, 67],
    55 => [68, 69],
    56 => [70, 71],
    57 => [72, 73],
    58 => [74, 75],
    59 => [76],
    60 => [77],
    61 => [78],
  ];

  private function getEnergyScoringSlots()
  {
    return [
      1 => [
        [
          'name' => clienttranslate('green planets'),
          'slots' => [140 => 2, 141 => 3, 142 => 4, 143 => 6],
        ],
        [
          'name' => clienttranslate('blue planets'),
          'slots' => [144 => 2, 145 => 4, 146 => 6, 147 => 8],
        ],
        [
          'name' => clienttranslate('grey planets'),
          'slots' => [148 => 4, 149 => 5, 150 => 6, 151 => 9],
        ]
      ],
      2 => [
        [
          'name' => clienttranslate('green planets'),
          'slots' => [152 => 2, 153 => 3, 154 => 4, 155 => 6],
        ],
        [
          'name' => clienttranslate('blue planets'),
          'slots' => [156 => 2, 157 => 4, 158 => 6, 159 => 8],
        ],
        [
          'name' => clienttranslate('grey planets'),
          'slots' => [160 => 4, 161 => 5, 162 => 6, 163 => 9],
        ]
      ],
    ][$this->whoIsPlaying];
  }

  public function setupScenario(): void
  {
    $this->addScribble(221, SCRIBBLE_INSIGNAS[$this->player2->getNo()], false);
    $this->addScribble(222, SCRIBBLE_INSIGNAS[$this->player1->getNo()], false);
  }

  public function getNumberBlocks(): array
  {
    return array_map(fn($planet) => $planet['slots'], $this->planets);
  }

  public function getAvailableSlotsForNumber(int $number, string $action): array
  {
    // Merge available block for that number
    $blocks = $this->allowedBlocksByNumber[$number];
    $slots = [];
    foreach ($blocks as $blockId) {
      $slot = $this->getFirstUnscribbled($this->planets[$blockId]['slots']);
      if (!is_null($slot)) $slots[] = $slot;
    }

    return $slots;
  }

  private function getPlanetIdBySlot(int $slot): ?int
  {
    foreach ($this->planets as $planetId => $planet) {
      if (in_array($slot, $planet['slots'])) {
        return $planetId;
      }
    }
    return null;
  }

  // PHASE 5
  public static function phase5Check(): void {}

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    $slot = $scribble->getSlot();
    $player = $this->getCurrentPlayer();
    $reactions = [];

    // PLANNING markers
    if ($methodSource == 'actDrawOnMoon') {
      $reactions[] = [
        'action' => CIRCLE_NEXT_IN_ROW,
        'args' => [
          'symbol' => CIRCLE_SYMBOL_PLANNING,
          'slots' => $this->getPlayerSectionSlots('planningmarkers'),
          'scribbleType' => SCRIBBLE,
        ]
      ];
    }

    // Number slots with robot bonus action
    $robotBonusSlots = [8, 11, 20];
    if (in_array($slot, $robotBonusSlots)) {
      $reactions[] = $this->getRobotAction();
    }

    // Number slots with plant bonus action
    $plantBonusSlots = [3, 14, 18, 25, 33];
    $plantAsteroidBonusSlots = [63, 65, 76, 78];
    if (in_array($slot, [...$plantBonusSlots, ...$plantAsteroidBonusSlots])) {
      $reactions[] = $this->getWaterOrPlantAction(false);
    }

    // Number slots with water bonus action
    $waterBonusSlots = [13, 24, 30];
    $waterAsteroidBonusSlots = [64, 66, 74, 77];
    if (in_array($slot, [...$waterBonusSlots, ...$waterAsteroidBonusSlots])) {
      $reactions[] = $this->getWaterOrPlantAction(true);
    }

    // Number slots with energy bonus action
    $energyAsteroidBonusSlots = [67, 68, 69, 72, 73, 75];
    if (in_array($slot, $energyAsteroidBonusSlots)) {
      $reactions[] = $this->getEnergyAction();
    }

    // Number slots with planning bonus action
    $planningAsteroidBonusSlots = [70, 71];
    if (in_array($slot, $planningAsteroidBonusSlots)) {
      $reactions[] = $this->getPlanningAction(true);
    }

    // Asteroid bonuses
    if (in_array($slot, array_keys($this->asteroidsBonuses))) {
      $scribbles = [];
      foreach ($this->asteroidsBonuses[$slot] as $bonusSlot) {
        $bonusScribble = $this->addScribble($bonusSlot);
        $scribbles[] = $bonusScribble;
        $reactions[] = $this->getScribbleReactions($bonusScribble, 'getScribbleReactions');
      }
      Notifications::addScribbles($player, $scribbles);
    }

    // Finishing a planet
    $planetId = $this->getPlanetIdBySlot($slot);
    if (!is_null($planetId)) {
      $currentPlanet = $this->planets[$planetId];
      if ($this->countScribbledSlots($currentPlanet['slots']) === count($currentPlanet['slots'])) {
        $planetAndMoonSlots = [...$currentPlanet['slots'], ...$currentPlanet['moonSlots']];
        $insigniaFirstPlayer = SCRIBBLE_INSIGNAS[$this->player1->getNo()];
        $insigniaSecondPlayer = SCRIBBLE_INSIGNAS[$this->player2->getNo()];
        $insnCountFirstPlayer = $this->countScribbledSlots($planetAndMoonSlots, $insigniaFirstPlayer);
        $insnCountSecondPlayer = $this->countScribbledSlots($planetAndMoonSlots, $insigniaSecondPlayer);
        $scribbles = [$this->addScribble($currentPlanet['final'], SCRIBBLE_CIRCLE)];
        if ($insnCountFirstPlayer === $insnCountSecondPlayer) {
          $scribbles[] = $this->addScribble($currentPlanet['flag'], $insigniaFirstPlayer, false);
          $scribbles[] = $this->addScribble($currentPlanet['flag'], $insigniaSecondPlayer, false);
          Notifications::drawOnFlagDouble($player, $this->getOpponentPlayer(), $scribbles, $currentPlanet['type']);
        } else {
          $maxInsignias = max($insnCountFirstPlayer, $insnCountSecondPlayer);
          $controller = $insnCountFirstPlayer === $maxInsignias ? $this->player1 : $this->player2;
          $scribbles[] = $this->addScribble($currentPlanet['flag'], SCRIBBLE_INSIGNAS[$controller->getNo()], false);
          Notifications::drawOnFlagSingle($player, $controller, $scribbles, $currentPlanet['type']);
        }
      }
    }

    return [
      'type' => NODE_SEQ,
      'childs' => $reactions
    ];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case ASTRONAUT:
        return [
          'action' => CIRCLE_NEXT_IN_ROW,
          'args' => [
            'symbol' => CIRCLE_SYMBOL_ASTRONAUT,
            'slots' => $this->getPlayerSectionSlots('astronautmarkers'),
            'scribbleType' => SCRIBBLE_CIRCLE,
          ]
        ];
      case PLANNING:
        return $this->getPlanningAction();
      case ROBOT:
        return $this->getRobotAction();
      case ENERGY:
        return $this->getEnergyAction();
      case PLANT:
        $planet = $this->getPlanetIdBySlot($slot);
        if (is_null($planet)) {
          throw new \BgaVisibleSystemException('Adventure 8, plant action - planet not found. Should not happen');
        }
        return [
          'action' => S8_CIRCLE_NEXT_IN_ROW_PLANT_WATER,
          'args' => [
            'planet' => [
              'symbol' => CROSS_SYMBOL_PLANT_ON_PLANET_AND_SHEET,
              'slots' => $this->planetsPlants[$planet],
              'scribbleType' => SCRIBBLE,
            ],
            'scoring' => [
              'slots' => $this->getPlayerSectionSlots('plants'),
              'scribbleType' => SCRIBBLE,
            ],
          ],
        ];
      case WATER:
        $planet = $this->getPlanetIdBySlot($slot);
        if (is_null($planet)) {
          throw new \BgaVisibleSystemException('Adventure 8, water action - planet not found. Should not happen');
        }
        return [
          'action' => S8_CIRCLE_NEXT_IN_ROW_PLANT_WATER,
          'args' => [
            'planet' => [
              'symbol' => CROSS_SYMBOL_WATER_ON_PLANET_AND_SHEET,
              'slots' => $this->planetsWaters[$planet],
              'scribbleType' => SCRIBBLE,
            ],
            'scoring' => [
              'slots' => $this->getPlayerSectionSlots('waters'),
              'scribbleType' => SCRIBBLE,
            ],
          ],
        ];
    }
    return null;
  }

  private function getRobotAction(): array
  {
    $scribbleType = SCRIBBLE_INSIGNAS[$this->player1->getNo()];
    return [
      'action' => CIRCLE_NEXT_IN_ROW,
      'args' => [
        'symbol' => CIRCLE_INSIGNIA_ON_ASTEROID,
        'slots' => $this->getPlayerSectionSlots('asteroids'),
        'scribbleType' => $scribbleType,
      ]
    ];
  }

  private function getEnergyAction(): array
  {
    return [
      'action' => IMPROVE_BONUS,
      'args' => [
        'data' => $this->getEnergyScoringSlots(),
      ]
    ];
  }

  private function getPlanningAction(bool $isBonus = false): array
  {
    return [
      'action' => S8_DRAW_ON_MOON,
      'args' => [
        'planets' => $this->planets,
        'insignia' => SCRIBBLE_INSIGNAS[$this->player1->getNo()],
        'isBonus' => $isBonus,
      ]
    ];
  }

  private function getWaterOrPlantAction(bool $isWater): array
  {
    return [
      'action' => CIRCLE_NEXT_IN_ROW,
      'args' => [
        'symbol' => $isWater ? CROSS_SYMBOL_WATER_ON_SHEET : CROSS_SYMBOL_PLANT_ON_SHEET,
        'slots' => $isWater ? $this->getPlayerSectionSlots('waters') : $this->getPlayerSectionSlots('plants'),
        'scribbleType' => SCRIBBLE,
      ]
    ];
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
    // $scribbledErrors = $this->countScribblesInSection('errors');
    // $negativePoints = 5 * $scribbledErrors;
    // $data[] = ["slot" => 46, "v" => $negativePoints];
    // $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    // $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    // $data[] = [
    //   "slot" => 47,
    //   "score" => true,
    //   "overview" => "total",
    //   "v" => -$negativePoints,
    // ];

    return $data;
  }


  //////////////////////////////////////////
  /////// HANDLE DOUBLE PLAYER /////////////
  //////////////////////////////////////////
  protected Player|Astra $player1; // Player at the BOTTOM
  protected Player|Astra $player2; // Player at the TOP
  protected int $whoIsPlaying = 0;

  public function __construct(Player|Astra|null $player1, Player|Astra|null $player2 = null, int|null $whoIsPlaying = null)
  {
    if (is_null($player1)) return; // Used to extract datas
    $this->player1 = $player1;
    $this->player2 = $player2;
    $this->whoIsPlaying = $whoIsPlaying;
    $this->fetch();

    // Extract info from datas
    foreach ($this->datas['sections'] as $section) {
      $this->slotsBySection[$section['id']] = [];
      foreach ($section['elts'] as $elt) {
        $this->slotsBySection[$section['id']][] = $elt['id'];
      }
    }
  }

  public function getPId(): int
  {
    return $this->player1->getId();
  }

  public function getCurrentPlayer(): Player
  {
    return $this->whoIsPlaying === 1 ? $this->player1 : $this->player2;
  }

  public function getOpponentPlayer(): Player
  {
    return $this->whoIsPlaying === 1 ? $this->player2 : $this->player1;
  }

  public function addScribble($location, $type = SCRIBBLE, $overrideWithCurrentInsignia = true): Scribble
  {
    if ($overrideWithCurrentInsignia) {
      $currentInsignia = SCRIBBLE_INSIGNAS[$this->getCurrentPlayer()->getNo()];
      if (!in_array($type, [SCRIBBLE, SCRIBBLE_CIRCLE])) {
        $type = $currentInsignia;
      }
    }

    $scribble = Scribbles::add($this->player1, [
      'type' => $type,
      'location' => "slot-$location",
    ]);
    $this->scribbles[$scribble->getId()] = $scribble;
    $this->scribblesBySlots[$scribble->getSlot()][] = $scribble;
    return $scribble;
  }

  // Useful for section slots that are mirrored on top and bottom
  public function getPlayerSectionSlots(string $section): array
  {
    if (!isset($this->slotsBySection[$section . $this->whoIsPlaying])) {
      if ($this->whoIsPlaying === 1) {
        return $this->slotsBySection[$section];
      } else {
        return array_reverse($this->slotsBySection[$section]);
      }
    }
    return $this->slotsBySection[$section . $this->whoIsPlaying] ?? [];
  }


  public function isEndOfGameTriggered(): bool
  {
    // // System errors
    // if (is_null($this->getNextFreeSystemErrorSlot())) {
    //   Stats::setEnding(STAT_ENDING_SYSTEM_ERRORS);
    //   Notifications::endGameTriggered($this->player, 'errors');
    //   return true;
    // }

    // // Plans
    // $unsatisfiedPlans = PlanCards::getCurrent()->filter(fn($plan) => !$plan->isValidated($this->player));
    // if ($unsatisfiedPlans->empty()) {
    //   Stats::setEnding(STAT_ENDING_MISSIONS);
    //   Notifications::endGameTriggered($this->player, 'plans');
    //   return true;
    // }

    // // Full scoresheet
    // if ($this->countAllUnscribbledSlots() === 0) {
    //   Stats::setEnding(STAT_ENDING_FILLED_ALL);
    //   Notifications::endGameTriggered($this->player, 'houses');
    //   return true;
    // }

    return false;
  }

  //////////////////////////////////////////
  //////////////////////////////////////////
  //////////////////////////////////////////

}
