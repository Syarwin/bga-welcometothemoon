<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\AstraAdventures\Astra8;
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

  public function getS8Planets(): array
  {
    return $this->planets;
  }

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

  public array $planetsPlants = [[181], [184], [185, 186], [188, 189], [191, 192], [194, 195], [197, 198]];
  public array $planetsWaters = [[182], [183], [187], [190], [193], [196], []];

  protected array $asteroidsBonuses = [
    50 => [],
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
    62 => [],
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

  private function getPlantsSlots($thisPlayer = true): array
  {
    $leftPlantsMap = [
      100 => 2,
      101 => 4,
      102 => 7,
      103 => 10,
      104 => 13,
      105 => 17,
      106 => 21,
      107 => 25,
      108 => 30,
      109 => 35,
      110 => 40
    ];
    $rightPlantsMap = [
      111 => 2,
      112 => 4,
      113 => 7,
      114 => 10,
      115 => 13,
      116 => 17,
      117 => 21,
      118 => 25,
      119 => 30,
      120 => 35,
      121 => 40
    ];
    return $thisPlayer ? $leftPlantsMap : $rightPlantsMap;
  }

  private function getWatersSlots($thisPlayer = true): array
  {
    $leftWatersMap = [122 => 3, 123 => 6, 124 => 10, 125 => 14, 126 => 19, 127 => 24, 128 => 30, 129 => 37, 130 => 45];
    $rightWatersMap = [131 => 3, 132 => 6, 133 => 10, 134 => 14, 135 => 19, 136 => 24, 137 => 30, 138 => 37, 139 => 45];
    return $thisPlayer ? $leftWatersMap : $rightWatersMap;
  }

  private function getEnergySlots($planetType, $thisPlayer = true): array
  {
    $leftEnergyMap = [
      PLANET_TYPE_GREEN => [140 => 3, 141 => 4, 142 => 6, 143 => 8],
      PLANET_TYPE_BLUE => [144 => 4, 145 => 6, 146 => 8, 147 => 10],
      PLANET_TYPE_GREY => [148 => 5, 149 => 6, 150 => 9, 151 => 12],
    ];
    $rightEnergyMap = [
      PLANET_TYPE_GREEN => [152 => 3, 153 => 4, 154 => 6, 155 => 8],
      PLANET_TYPE_BLUE => [156 => 4, 157 => 6, 158 => 8, 159 => 10],
      PLANET_TYPE_GREY => [160 => 5, 161 => 6, 162 => 9, 163 => 12],
    ];
    return $thisPlayer ? $leftEnergyMap[$planetType] : $rightEnergyMap[$planetType];
  }

  private function getPlanningSlots($thisPlayer = true): array
  {
    $leftPlanningMap = [164 => 3, 165 => 6, 166 => 9, 167 => 15, 168 => 21, 169 => 28, 170 => 36];
    $rightPlanningMap = [171 => 3, 172 => 6, 173 => 9, 174 => 15, 175 => 21, 176 => 28, 177 => 36];
    return $thisPlayer ? $leftPlanningMap : $rightPlanningMap;
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

  public function getPlanetIdBySlot(int $slot): ?int
  {
    foreach ($this->planets as $planetId => $planet) {
      if (in_array($slot, $planet['slots'])) {
        return $planetId;
      }
    }
    return null;
  }

  public function getPlanetOfSlot(int $slot): ?array
  {
    $planetId = $this->getPlanetIdBySlot($slot);
    return is_null($planetId) ? null : $this->planets[$planetId];
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
      if (!empty($scribbles)) {
        Notifications::addScribbles($player, $scribbles);
      }
    }

    // Finishing a planet
    $planetId = $this->getPlanetIdBySlot($slot);
    if (!is_null($planetId)) {
      $reactions[] = [
        'action' => S8_RESOLVE_PLANET_WINNER,
        'args' => ['planetId' => $planetId]
      ];
    }

    return [
      'type' => NODE_SEQ,
      'childs' => $reactions
    ];
  }

  public function getCurrentPlanetStatus(int $planetId): array
  {
    $currentPlanet = $this->planets[$planetId];
    $planetAndMoonSlots = [...$currentPlanet['slots'], ...$currentPlanet['moonSlots']];
    $insigniaFirstPlayer = SCRIBBLE_INSIGNAS[$this->player1->getNo()];
    $insigniaSecondPlayer = SCRIBBLE_INSIGNAS[$this->player2->getNo()];
    $insnCountFirstPlayer = $this->countScribbledSlots($planetAndMoonSlots, $insigniaFirstPlayer);
    $insnCountSecondPlayer = $this->countScribbledSlots($planetAndMoonSlots, $insigniaSecondPlayer);
    return [$insnCountFirstPlayer, $insnCountSecondPlayer];
  }

  public function resolvePlanetWinnerIfNeeded(Astra|Player $player, int $planetId, bool $endOfGame): array
  {
    $currentPlanet = $this->planets[$planetId];
    if (!$endOfGame && $this->countScribbledSlots($currentPlanet['slots']) !== count($currentPlanet['slots'])) return [];

    [$insnCountFirstPlayer, $insnCountSecondPlayer] = $this->getCurrentPlanetStatus($planetId);

    $insigniaFirstPlayer = SCRIBBLE_INSIGNAS[$this->player1->getNo()];
    $insigniaSecondPlayer = SCRIBBLE_INSIGNAS[$this->player2->getNo()];
    $scribbles = [$this->addScribble($currentPlanet['final'], SCRIBBLE_CIRCLE)];
    if ($insnCountFirstPlayer === $insnCountSecondPlayer && !Globals::isSolo()) {
      $scribbles[] = $this->addScribble($currentPlanet['flag'], $insigniaFirstPlayer, false);
      $scribbles[] = $this->addScribble($currentPlanet['flag'], $insigniaSecondPlayer, false);
      Notifications::drawOnFlagDouble($player, $this->getOpponentPlayer(), $scribbles, $currentPlanet['type'], $endOfGame);
    } else {
      $maxInsignias = max($insnCountFirstPlayer, $insnCountSecondPlayer);
      $controller = $insnCountFirstPlayer === $maxInsignias ? $this->player1 : $this->player2;
      if (Globals::isSolo() && $insnCountFirstPlayer === $insnCountSecondPlayer && $controller instanceof Astra) {
        $controller = $this->player2;
      }
      $scribbles[] = $this->addScribble($currentPlanet['flag'], SCRIBBLE_INSIGNAS[$controller->getNo()], false);
      Notifications::drawOnFlagSingle($player, $controller, $scribbles, $currentPlanet['type'], $endOfGame);
    }

    // SOLO EFFECT
    if (Globals::isSolo() && !$endOfGame) {
      // Have we won this planet ?
      $types = array_map(fn($scribble) => $scribble->getType(), $scribbles);
      if (in_array(SCRIBBLE_INSIGNA_SQUARE, $types)) {
        $scoresheet1 = Players::getSolo()->scoresheetForScore();
        $scoresheet2 = Players::getAstra()->scoresheetForScore();
        $controlledPlanets = $scoresheet1->getControlledPlanetsAmount($scoresheet1, SCRIBBLE_INSIGNA_SQUARE) + $scoresheet1->getControlledPlanetsAmount($scoresheet2, SCRIBBLE_INSIGNA_SQUARE);
        if ($controlledPlanets % 2 == 0) {
          $bonusScribble = Players::getAstra()->circleNextBonus();
          Notifications::gainOneSoloBonus($player, $bonusScribble);
        }
      }
    }

    return $scribbles;
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

  // Plans
  // We calculate everything here because this scoresheet has too many fragile moving items, and would be too many problems to make them all public
  // Plan Card 106
  public function is4PlanetControlledOnAnySheet(): bool
  {
    /** @var Scoresheet $scoresheet */
    foreach ([$this->player1->scoresheet(), $this->player2->scoresheet()] as $scoresheet) {
      if ($this->getControlledPlanetsAmount($scoresheet, $this->getMyInsignia()) >= 4) {
        return true;
      }
    }
    return false;
  }

  // Plan Card 107
  public function isPlanetOfEachTypeControlled(): bool
  {
    /** @var Scoresheet $scoresheet */
    foreach ([$this->player1->scoresheet(), $this->player2->scoresheet()] as $scoresheet) {
      $greenControlled = $this->getControlledPlanetsAmount($scoresheet, $this->getMyInsignia(), PLANET_TYPE_GREEN);
      $blueControlled = $this->getControlledPlanetsAmount($scoresheet, $this->getMyInsignia(), PLANET_TYPE_BLUE);
      $greyControlled = $this->getControlledPlanetsAmount($scoresheet, $this->getMyInsignia(), PLANET_TYPE_GREY);
      if ($greenControlled >= 1 && $blueControlled >= 1 && $greyControlled >= 1) {
        return true;
      }
    }
    return false;
  }

  // Plan Card 108
  public function is7AsteroidsOnAnySheet(): bool
  {
    /** @var Scoresheet $scoresheet */
    foreach ([$this->player1->scoresheet(), $this->player2->scoresheet()] as $scoresheet) {
      $asteroidSlots = array_keys($this->asteroidsBonuses);
      if ($scoresheet->countScribbledSlots($asteroidSlots, $this->getMyInsignia()) >= 7) {
        return true;
      }
    }
    return false;
  }

  // Plan Card 109
  // public function isAllPlanetsUpgradedTwice(): bool
  // {
  //   $player = $this->getCurrentPlayer();
  //   /** @var Scoresheet $scoresheet */
  //   foreach ([$player->scoresheet(), $this->getOpponentPlayer()->scoresheet()] as $index => $scoresheet) {
  //     $greenPlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_GREEN, $index === 1));
  //     $greenPlanetsUpgradesScribbled = $scoresheet->countScribbledSlots($greenPlanetsUpgrades);
  //     $bluePlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_BLUE, $index === 1));
  //     $bluePlanetsUpgradesScribbled = $scoresheet->countScribbledSlots($bluePlanetsUpgrades);
  //     $greyPlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_GREY, $index === 1));
  //     $greyPlanetsUpgradesScribbled = $scoresheet->countScribbledSlots($greyPlanetsUpgrades);
  //     if ($greenPlanetsUpgradesScribbled >= 2 && $bluePlanetsUpgradesScribbled >= 2 && $greyPlanetsUpgradesScribbled >= 2) {
  //       return true;
  //     }
  //   }
  //   return false;
  // }
  public function hasAllPlanetsUpgradedTwice(): bool
  {
    $greenPlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_GREEN, $this->whoIsPlaying === 1));
    $greenPlanetsUpgradesScribbled = $this->countScribbledSlots($greenPlanetsUpgrades);
    $bluePlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_BLUE, $this->whoIsPlaying === 1));
    $bluePlanetsUpgradesScribbled = $this->countScribbledSlots($bluePlanetsUpgrades);
    $greyPlanetsUpgrades = array_keys($this->getEnergySlots(PLANET_TYPE_GREY, $this->whoIsPlaying === 1));
    $greyPlanetsUpgradesScribbled = $this->countScribbledSlots($greyPlanetsUpgrades);

    return ($greenPlanetsUpgradesScribbled >= 2 && $bluePlanetsUpgradesScribbled >= 2 && $greyPlanetsUpgradesScribbled >= 2);
  }

  // Plan Card 110
  // public function isAnySheetHas6PlantsAnd4Water(): bool
  // {
  //   /** @var Scoresheet $scoresheet */
  //   foreach ([$this->player1->scoresheet(), $this->player2->scoresheet()] as $index => $scoresheet) {
  //     $currentPlayerPlantsSlots = array_keys($this->getPlantsSlots($index === 1));
  //     $plantsSlotsScribbled = $scoresheet->countScribbledSlots($currentPlayerPlantsSlots);
  //     $currentPlayerWatersSlots = array_keys($this->getWatersSlots($index === 1));
  //     $watersSlotsScribbled = $scoresheet->countScribbledSlots($currentPlayerWatersSlots);
  //     // var_dump($plantsSlotsScribbled, $watersSlotsScribbled);
  //     if ($plantsSlotsScribbled >= 6 && $watersSlotsScribbled >= 4) {
  //       return true;
  //     }
  //   }
  //   return false;
  // }
  public function has6PlantsAnd4Water(): bool
  {
    $currentPlayerPlantsSlots = array_keys($this->getPlantsSlots($this->whoIsPlaying == 1));
    $plantsSlotsScribbled = $this->countScribbledSlots($currentPlayerPlantsSlots);
    $currentPlayerWatersSlots = array_keys($this->getWatersSlots($this->whoIsPlaying == 1));
    $watersSlotsScribbled = $this->countScribbledSlots($currentPlayerWatersSlots);

    return $plantsSlotsScribbled >= 6 && $watersSlotsScribbled >= 4;
  }


  // Plan Card 111
  public function isAnySheetHasInsigniaOn4Moons(): bool
  {
    /** @var Scoresheet $scoresheet */
    foreach ([$this->player1->scoresheet(), $this->player2->scoresheet()] as $scoresheet) {
      $moonsWithInsignia = 0;
      foreach ($this->planets as $planet) {
        if ($scoresheet->countScribbledSlots($planet['moonSlots'], $this->getMyInsignia()) > 0) {
          $moonsWithInsignia++;
        }
      }
      if ($moonsWithInsignia >= 4) {
        return true;
      }
    }
    return false;
  }

  private function getPlanets(int $planetType): array
  {
    return array_filter($this->planets, function ($planet) use ($planetType) {
      return $planet['type'] === $planetType;
    });
  }

  public function getControlledPlanetsAmount(Scoresheet $scoresheet, int $insignia, ?int $planetType = null): int
  {
    $planets = is_null($planetType) ? $this->planets : $this->getPlanets($planetType);
    $planetsMap = array_map(fn($planet) => $scoresheet->hasScribbledSlot($planet['flag'], $insignia), $planets);
    return count(array_filter($planetsMap));
  }

  public static function getEndScenarioPlanetMajorityFlows()
  {
    $flows = [];
    foreach (Players::getAll() as $pId => $player) {
      $scoresheet = $player->scoresheetForScore();
      $planets = $scoresheet->getS8Planets();
      $childs = [];
      foreach ($planets as $planetId => $planet) {
        if (!$scoresheet->hasScribbledSlot($planet['flag'])) {
          $childs[] = [
            'action' => S8_RESOLVE_PLANET_WINNER,
            'args' => ['planetId' => $planetId, 'endOfGame' => true]
          ];
        }
      }

      if (!empty($childs)) {
        $flows[$pId] = [
          'type' => NODE_SEQ,
          'childs' => $childs,
        ];
      }
    }

    // ASTRA
    if (Globals::isSolo()) {
      $player = Players::getAstra();
      $scoresheet = $player->scoresheetForScore();
      $planets = $scoresheet->getS8Planets();
      foreach ($planets as $planetId => $planet) {
        if (!$scoresheet->hasScribbledSlot($planet['flag'])) {
          $scoresheet->resolvePlanetWinnerIfNeeded($player, $planetId, true);
        }
      }
    }


    return $flows;
  }


  /**
   * UI DATA
   */
  public function computeUiData(): array
  {
    $data = [];
    $p1Insignia = SCRIBBLE_INSIGNAS[$this->getOpponentPlayer()->getNo()];
    $p2Insignia = SCRIBBLE_INSIGNAS[$this->getCurrentPlayer()->getNo()];
    $b1 = $this->player2->scoresheetForScore();
    $b2 = $this->player1->scoresheetForScore();
    $data[] = [$b1->getPId(), $b2->getPId()];

    // Player 1

    // Number of numbered slots
    $nNumberedSlots1 = $b1->countScribblesInSection('numbers');
    $nNumberedSlots2 = $b2->countScribblesInSection('numbers');
    $data[] = ["overview" => "numbers1", "v" => $nNumberedSlots1, 'max' => count($this->getSectionSlots('numbers'))];
    $data[] = ["overview" => "numbers2", "v" => $nNumberedSlots2, 'max' => count($this->getSectionSlots('numbers'))];
    $data[] = ["panel" => "numbers", "v" => $nNumberedSlots1 . " | " . $nNumberedSlots2];

    // Missions
    $b2missionPoints = $this->computeMissionsUiData($data, $b2);
    $dataCopy = $data;
    $b1missionPoints = $this->computeMissionsUiData($dataCopy, $b1);
    $data[] = ["slot" => 199, "v" => $b2missionPoints];

    // ********* LEFT SIDE OF THE BOARD (if you place a board so numbers on planets are aligned and readable) *********
    // Plants
    $p1b1plantsPoints = 0;
    $p2b2plantsPoints = 0;
    foreach ($this->getPlantsSlots() as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1plantsPoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2plantsPoints = $points;
      }
    }

    $data[] = ["slot" => 200, "v" => $p2b2plantsPoints];

    // Water
    $p1b1waterPoints = 0;
    $p2b2waterPoints = 0;
    foreach ($this->getWatersSlots() as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1waterPoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2waterPoints = $points;
      }
    }
    $data[] = ["slot" => 201, "v" => $p2b2waterPoints];

    // Energy
    // Green planets
    $p1b1greenMultiplier = 2;
    $p2b2greenMultiplier = 2;
    foreach ($this->getEnergySlots(PLANET_TYPE_GREEN) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1greenMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2greenMultiplier = $multiplier;
      }
    }
    $p1b1greenPlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p1Insignia, PLANET_TYPE_GREEN);
    $p1b1greenPlanetsScore = $p1b1greenPlanetsControlledAmount * $p1b1greenMultiplier;
    $p2b2greenPlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p2Insignia, PLANET_TYPE_GREEN);
    $p2b2greenPlanetsScore = $p2b2greenPlanetsControlledAmount * $p2b2greenMultiplier;
    $data[] = ["slot" => 215, "v" => $p2b2greenPlanetsControlledAmount];
    $data[] = ["slot" => 202, "v" => $p2b2greenPlanetsScore];

    // Blue planets
    $p1b1blueMultiplier = 2;
    $p2b2blueMultiplier = 2;
    foreach ($this->getEnergySlots(PLANET_TYPE_BLUE) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1blueMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2blueMultiplier = $multiplier;
      }
    }
    $p1b1bluePlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p1Insignia, PLANET_TYPE_BLUE);
    $p1b1bluePlanetsScore = $p1b1bluePlanetsControlledAmount * $p1b1blueMultiplier;
    $p2b2bluePlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p2Insignia, PLANET_TYPE_BLUE);
    $p2b2bluePlanetsScore = $p2b2bluePlanetsControlledAmount * $p2b2blueMultiplier;
    $data[] = ["slot" => 216, "v" => $p2b2bluePlanetsControlledAmount];
    $data[] = ["slot" => 203, "v" => $p2b2bluePlanetsScore];

    // Grey planets
    $p1b1greyMultiplier = 4;
    $p2b2greyMultiplier = 4;
    foreach ($this->getEnergySlots(PLANET_TYPE_GREY) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1greyMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2greyMultiplier = $multiplier;
      }
    }
    $p1b1greyPlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p1Insignia, PLANET_TYPE_GREY);
    $p1b1greyPlanetsScore = $p1b1greyPlanetsControlledAmount * $p1b1greyMultiplier;
    $p2b2greyPlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p2Insignia, PLANET_TYPE_GREY);
    $p2b2greyPlanetsScore = $p2b2greyPlanetsControlledAmount * $p2b2greyMultiplier;
    $data[] = ["slot" => 217, "v" => $p2b2greyPlanetsControlledAmount];
    $data[] = ["slot" => 204, "v" => $p2b2greyPlanetsScore];

    // Planning
    $p1b1planningNegativePoints = 0;
    $p2b2planningNegativePoints = 0;
    foreach ($this->getPlanningSlots() as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p1b1planningNegativePoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p2b2planningNegativePoints = $points;
      }
    }
    $data[] = ["slot" => 205, "v" => $p2b2planningNegativePoints];

    // System errors
    $p1b1scribbledErrors = $b1->countScribblesInSectionS8('errors', null, true);
    $p1b1errorsNegativePoints = 5 * $p1b1scribbledErrors;
    $p2b2scribbledErrors = $b2->countScribblesInSectionS8('errors', null, true);
    $p2b2errorsNegativePoints = 5 * $p2b2scribbledErrors;
    $data[] = ["slot" => 206, "v" => $p2b2errorsNegativePoints];


    // ********* RIGHT SIDE OF THE BOARD *********
    // Plants
    $p2b1plantsPoints = 0;
    $p1b2plantsPoints = 0;
    foreach ($this->getPlantsSlots(false) as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1plantsPoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2plantsPoints = $points;
      }
    }
    $data[] = ["slot" => 207, "v" => $p1b2plantsPoints];
    // Water
    $p2b1waterPoints = 0;
    $p1b2waterPoints = 0;
    foreach ($this->getWatersSlots(false) as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1waterPoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2waterPoints = $points;
      }
    }
    $data[] = ["slot" => 208, "v" => $p1b2waterPoints];

    // Energy
    // Green planets
    $p2b1greenMultiplier = 2;
    $p1b2greenMultiplier = 2;
    foreach ($this->getEnergySlots(PLANET_TYPE_GREEN, false) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1greenMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2greenMultiplier = $multiplier;
      }
    }
    $p2b1greenPlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p2Insignia, PLANET_TYPE_GREEN);
    $p2b1greenPlanetsScore = $p2b1greenPlanetsControlledAmount * $p2b1greenMultiplier;
    $p1b2greenPlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p1Insignia, PLANET_TYPE_GREEN);
    $p1b2greenPlanetsScore = $p1b2greenPlanetsControlledAmount * $p1b2greenMultiplier;
    $data[] = ["slot" => 218, "v" => $p1b2greenPlanetsControlledAmount];
    $data[] = ["slot" => 209, "v" => $p1b2greenPlanetsScore];

    // Blue planets
    $p2b1blueMultiplier = 2;
    $p1b2blueMultiplier = 2;
    foreach ($this->getEnergySlots(PLANET_TYPE_BLUE, false) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1blueMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2blueMultiplier = $multiplier;
      }
    }
    $p2b1bluePlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p2Insignia, PLANET_TYPE_BLUE);
    $p2b1bluePlanetsScore = $p2b1bluePlanetsControlledAmount * $p2b1blueMultiplier;
    $p1b2bluePlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p1Insignia, PLANET_TYPE_BLUE);
    $p1b2bluePlanetsScore = $p1b2bluePlanetsControlledAmount * $p1b2blueMultiplier;
    $data[] = ["slot" => 219, "v" => $p1b2bluePlanetsControlledAmount];
    $data[] = ["slot" => 210, "v" => $p1b2bluePlanetsScore];

    // Grey planets
    $p2b1greyMultiplier = 4;
    $p1b2greyMultiplier = 4;
    foreach ($this->getEnergySlots(PLANET_TYPE_GREY, false) as $slot => $multiplier) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1greyMultiplier = $multiplier;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2greyMultiplier = $multiplier;
      }
    }
    $p2b1greyPlanetsControlledAmount = $this->getControlledPlanetsAmount($b1, $p2Insignia, PLANET_TYPE_GREY);
    $p2b1greyPlanetsScore = $p2b1greyPlanetsControlledAmount * $p2b1greyMultiplier;
    $p1b2greyPlanetsControlledAmount = $this->getControlledPlanetsAmount($b2, $p1Insignia, PLANET_TYPE_GREY);
    $p1b2greyPlanetsScore = $p1b2greyPlanetsControlledAmount * $p1b2greyMultiplier;

    $data[] = ["slot" => 220, "v" => $p1b2greyPlanetsControlledAmount];
    $data[] = ["slot" => 211, "v" => $p1b2greyPlanetsScore];

    // Planning
    $p2b1planningNegativePoints = 0;
    $p1b2planningNegativePoints = 0;
    foreach ($this->getPlanningSlots(false) as $slot => $points) {
      if ($b1->hasScribbledSlot($slot)) {
        $p2b1planningNegativePoints = $points;
      }
      if ($b2->hasScribbledSlot($slot)) {
        $p1b2planningNegativePoints = $points;
      }
    }
    $data[] = ["slot" => 212, "v" => $p1b2planningNegativePoints];

    // System errors
    $p2b1scribbledErrors = $b1->countScribblesInSectionS8('errors', null, false);
    $p2b1errorsNegativePoints = 5 * $p2b1scribbledErrors;
    $p1b2scribbledErrors = $b2->countScribblesInSectionS8('errors', null, false);
    $p1b2errorsNegativePoints = 5 * $p1b2scribbledErrors;
    $data[] = ["slot" => 213, "v" => $p1b2errorsNegativePoints];

    // Total Player 1 score
    $data[] = [
      "slot" => 214,
      "overview" => "total",
      "v" => $b1missionPoints + $p1b2plantsPoints + $p1b2waterPoints + $p1b2greenPlanetsScore + $p1b2bluePlanetsScore
        + $p1b2greyPlanetsScore - $p1b2planningNegativePoints - $p1b2errorsNegativePoints
        + $p1b1plantsPoints + $p1b1waterPoints + $p1b1greenPlanetsScore + $p1b1bluePlanetsScore
        + $p1b1greyPlanetsScore - $p1b1planningNegativePoints - $p1b1errorsNegativePoints,
    ];

    $data[] = [
      "score" => true,
      "v" => $b2missionPoints + $p2b2plantsPoints + $p2b2waterPoints + $p2b2greenPlanetsScore + $p2b2bluePlanetsScore
        + $p2b2greyPlanetsScore - $p2b2planningNegativePoints - $p2b2errorsNegativePoints
        + $p2b1plantsPoints + $p2b1waterPoints + $p2b1greenPlanetsScore + $p2b1bluePlanetsScore
        + $p2b1greyPlanetsScore - $p2b1planningNegativePoints - $p2b1errorsNegativePoints,
    ];

    $data[] = [
      "overview" => "errors1",
      "v" => -$p1b1errorsNegativePoints,
      "details" => ($p1b1scribbledErrors . " / 2"),
      "subdetails" => true
    ];
    $data[] = [
      "overview" => "errors2",
      "v" => -$p1b2errorsNegativePoints,
      "details" => ($p1b2scribbledErrors . " / 2"),
      "subdetails" => true
    ];
    $data[] = ["panel" => "errors", "v" => $p1b1scribbledErrors . " | " . $p1b2scribbledErrors];

    $data[] = [
      "overview" => "plants",
      "v" => $p1b1plantsPoints + $p1b2plantsPoints,
      "details" => ($p1b1plantsPoints . "+" . $p1b2plantsPoints),
      "subdetails" => true
    ];

    $data[] = [
      "overview" => "waters",
      "v" => $p1b1waterPoints + $p1b2waterPoints,
      "details" => ($p1b1waterPoints . "+" . $p1b2waterPoints),
      "subdetails" => true
    ];

    $data[] = [
      "overview" => "plant-planets",
      "v" => $p1b1greenPlanetsScore + $p1b2greenPlanetsScore,
      "details" => ($p1b1greenPlanetsScore . "+" . $p1b2greenPlanetsScore),
      "subdetails" => true
    ];
    $data[] = [
      "overview" => "water-planets",
      "v" => $p1b1bluePlanetsScore + $p1b2bluePlanetsScore,
      "details" => ($p1b1bluePlanetsScore . "+" . $p1b2bluePlanetsScore),
      "subdetails" => true
    ];
    $data[] = [
      "overview" => "robot-planets",
      "v" => $p1b1greyPlanetsScore + $p1b2greyPlanetsScore,
      "details" => ($p1b1greyPlanetsScore . "+" . $p1b2greyPlanetsScore),
      "subdetails" => true
    ];

    $data[] = [
      "overview" => "plannings",
      "v" => -$p1b1planningNegativePoints - $p1b2planningNegativePoints,
      "details" => ($p1b1planningNegativePoints . "+" . $p1b2planningNegativePoints),
      "subdetails" => true
    ];

    // Filter out Astra useless slots
    $slots = [];
    if ($this->getOpponentPlayer() instanceof Astra8) {
      $slots = [207, 208, 209, 210, 211, 212, 213, 214, 218, 219, 220];
    }
    if ($this->getCurrentPlayer() instanceof Astra8) {
      $slots = [199, 200, 201, 202, 203, 204, 205, 206, 215, 216, 217];
    }
    Utils::filter($data, fn($entry) => !in_array($entry['slot'] ?? null, $slots));

    return $data;
  }


  //////////////////////////////////////////
  /////// HANDLE DOUBLE PLAYER /////////////
  //////////////////////////////////////////
  protected Player|Astra $player1; // Player at the BOTTOM
  protected Player|Astra $player2; // Player at the TOP
  public int $whoIsPlaying = 0;

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

  public function getCurrentPlayer(): Player|Astra8
  {
    return $this->whoIsPlaying === 1 ? $this->player1 : $this->player2;
  }

  public function getOpponentPlayer(?Player $player = null): Player|Astra8
  {
    if (is_null($player)) {
      $player = $this->getCurrentPlayer();
    }
    return $this->player1->getId() === $player->getId() ? $this->player2 : $this->player1;
  }

  public function getWhoIsNotPlaying(): int
  {
    return $this->whoIsPlaying === 1 ? 2 : 1;
  }

  public function addScribble($location, $type = SCRIBBLE, $overrideWithCurrentInsignia = true): Scribble
  {
    $planSlots = [178, 179, 180];
    if ($overrideWithCurrentInsignia && !in_array($location, $planSlots)) {
      $currentInsignia = SCRIBBLE_INSIGNAS[$this->getCurrentPlayer()->getNo()];
      if (!in_array($type, [SCRIBBLE, SCRIBBLE_CIRCLE])) {
        $type = $currentInsignia;
      }
    }

    $player = in_array($location, $planSlots) ? $this->getCurrentPlayer() : $this->player1;
    $scribble = Scribbles::add($player, [
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

  public function countScribblesInSectionS8(string $section, ?int $type = null, ?bool $thisPlayer = null): int
  {
    if (is_null($thisPlayer)) {
      return parent::countScribblesInSection($section, $type);
    }
    $playerNumber = $thisPlayer ? 1 : 2;
    return parent::countScribblesInSection($section . $playerNumber, $type);
  }

  private function getMyInsignia(): int
  {
    return SCRIBBLE_INSIGNAS[$this->getCurrentPlayer()->getNo()];
  }

  public function getNextFreeSystemErrorSlot(): ?int
  {
    $slots = $this->getSectionFreeSlots($this->whoIsPlaying == 1 ? 'errors1' : 'errors2');
    return empty($slots) ? null : $slots[0];
  }

  public function getScoreAux(): int
  {
    $b1 = $this->player2->scoresheetForScore();
    $b2 = $this->player1->scoresheetForScore();
    $p2b1scribbledErrors = $b1->countScribblesInSectionS8('errors', null, false);
    $p2b2scribbledErrors = $b2->countScribblesInSectionS8('errors', null, true);

    return 4 - $p2b1scribbledErrors - $p2b2scribbledErrors;
  }

  public function isEndOfGameTriggered(): bool
  {
    // System errors
    if (empty($this->getSectionFreeSlots('errors1'))) {
      Stats::setEnding(STAT_ENDING_SYSTEM_ERRORS);
      Notifications::endGameTriggered($this->player1, 'errors');
      return true;
    }
    if (empty($this->getSectionFreeSlots('errors2'))) {
      Stats::setEnding(STAT_ENDING_SYSTEM_ERRORS);
      Notifications::endGameTriggered($this->player2, 'errors');
      return true;
    }

    // Plans
    $unsatisfiedPlans = PlanCards::getCurrent()->filter(fn($plan) => !$plan->isValidated($this->player1));
    if ($unsatisfiedPlans->empty()) {
      Stats::setEnding(STAT_ENDING_MISSIONS);
      Notifications::endGameTriggered($this->player1, 'plans');
      return true;
    }
    $unsatisfiedPlans = PlanCards::getCurrent()->filter(fn($plan) => !$plan->isValidated($this->player2));
    if ($unsatisfiedPlans->empty()) {
      Stats::setEnding(STAT_ENDING_MISSIONS);
      Notifications::endGameTriggered($this->player2, 'plans');
      return true;
    }

    // Full scoresheet
    if ($this->countAllUnscribbledSlots() === 0) {
      Stats::setEnding(STAT_ENDING_FILLED_ALL);
      $player = $this->player1;
      if ($player->getId() == 0) $player = $this->player2;
      Notifications::endGameTriggered($player, 'houses');
      return true;
    }

    return false;
  }

  //////////////////////////////////////////
  //////////////////////////////////////////
  //////////////////////////////////////////

}
