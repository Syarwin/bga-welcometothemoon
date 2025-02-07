<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario1.php";


class Scoresheet1 extends Scoresheet
{
  protected int $scenario = 1;
  protected array $datas = DATAS;

  // PHASE 5
  public static function phase5Check(): void
  {
    $sabotages = array_unique(Globals::getTriggeredSabotages());
    $players = Players::getAll();

    // For each targeted sabotage
    foreach ($sabotages as $slot) {
      $scribbles = [];
      $affectedPlayers = [];

      // Find corresponding quarter name
      $name = '';
      foreach (self::getQuarters() as $quarter) {
        if (isset($quarter['bonuses'][$slot])) {
          $name = $quarter['name'];
          break;
        }
      }

      // Check each player
      foreach ($players as $player) {
        // Scribble the sabotage bonus (with a squiggle)
        $scribbles[] = $player->scoresheet()->addScribble($slot);

        // Is the player going to suffer the negative effect ?
        if (!$player->scoresheet()->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
          $systemErrorSlot = $player->scoresheet()->getFreeSystemErrorSlot();
          if (!is_null($systemErrorSlot)) {
            $affectedPlayers[] = $player;
            $scribbles[] = $player->scoresheet()->addScribble($systemErrorSlot, SCRIBBLE_CIRCLE);
          }
        }
      }

      Notifications::resolveSabotage($affectedPlayers, $scribbles, $name);
    }

    Globals::setTriggeredSabotages([]);
  }

  public function isEndOfGameTriggered(): bool
  {
    $triggered = parent::isEndOfGameTriggered();
    if ($triggered) return true;

    // Any circled system error not crossed off?
    $slots = $this->getSectionSlots('errors');
    foreach ($slots as $slot) {
      if ($this->hasScribbledSlot($slot, SCRIBBLE_CIRCLE) && !$this->hasScribbledSlot($slot)) {
        return false;
      }
    }

    Notifications::endGameTriggered($this->player, 'launch');
    return true;
  }


  // WRITE NUMBER
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

  // QUARTER BONUSES
  public function getScribbleReactions($scribble): array
  {
    $slot = $scribble->getSlot();

    // Number => check quarters
    if (1 <= $slot && $slot <= 53) {
      foreach ($this->getQuarters() as $quarter) {
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
    return $this->hasScribbledSlots($slots) && in_array($slot, $slots);
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
          'name' => $quarter['name'],
        ]
      ];
    }

    return $actions;
  }

  public static function getQuarters()
  {
    return [
      // ASTRONAUT
      [
        'slots' => [1, 2, 3],
        'bonuses' => [
          54 => [ROCKET => 3],
          55 => [ACTIVATION => 1],
          56 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("1st Astronaut quarter bonus"),
      ],
      [
        'slots' => [4, 5, 6],
        'bonuses' => [
          57 => [ROCKET => 2],
          58 => [ROCKET => 2, 'check' => 152],
          59 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("2nd Astronaut quarter bonus"),
      ],
      // WATER
      [
        'slots' => [7, 8],
        'bonuses' => [
          60 => [ROCKET => 2],
          61 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("1st Water quarter bonus"),
      ],
      [
        'slots' => [9, 10, 11],
        'bonuses' => [
          62 => [ROCKET => 3],
          63 => [ROCKET => 3, 'check' => 153],
        ],
        'name' => clienttranslate("2nd Water quarter bonus"),
      ],
      // ROBOT
      [
        'slots' => [12, 13],
        'bonuses' => [
          64 => [ACTIVATION => 1],
          65 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("1st Robot quarter bonus"),
      ],
      [
        'slots' => [14, 15, 16],
        'bonuses' => [
          66 => [ROCKET => 3],
          67 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("2nd Robot quarter bonus"),
      ],
      [
        'slots' => [17, 18, 19, 20, 21],
        'bonuses' => [
          68 => [ROCKET => 3],
          69 => [ROCKET => 3, 'check' => 154],
          70 => [SABOTAGE => 1],
          71 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("3rd Robot quarter bonus"),
      ],
      // PLANNING
      [
        'slots' => [22, 23, 24, 25, 26, 27],
        'bonuses' => [
          72 => [ROCKET => 4],
          73 => [ROCKET => 4, 'check' => 155],
          74 => [NUMBER_X => 1],
          75 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("Planning quarter bonus"),
      ],
      // ENERGY
      [
        'slots' => [28, 29, 30, 31, 32],
        'bonuses' => [
          76 => [ROCKET => 4],
          77 => [ROCKET => 4, 'check' => 156],
          78 => [SABOTAGE => 1],
          79 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("1st Energy quarter bonus"),
      ],
      [
        'slots' => [33, 34],
        'bonuses' => [
          80 => [ROCKET => 3]
        ],
        'name' => clienttranslate("2nd Energy quarter bonus"),
      ],
      [
        'slots' => [35, 36, 37],
        'bonuses' => [
          81 => [ROCKET => 2],
          82 => [ACTIVATION => 1],
          83 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("3rd Energy quarter bonus"),
      ],
      // PLANT
      [
        'slots' => [38, 39],
        'bonuses' => [
          84 => [ROCKET => 2],
          85 => [ROCKET => 2, 'check' => 157],
        ],
        'name' => clienttranslate("1st Plant quarter bonus"),
      ],
      [
        'slots' => [40, 41],
        'bonuses' => [
          86 => [ROCKET => 2],
          87 => [ACTIVATION => 1],
        ],
        'name' => clienttranslate("2nd Plant quarter bonus"),
      ],
      [
        'slots' => [42, 43],
        'bonuses' => [
          88 => [ROCKET => 2],
          89 => [ROCKET => 2, 'check' => 158],
        ],
        'name' => clienttranslate("3rd Plant quarter bonus"),
      ],
      [
        'slots' => [44, 45],
        'bonuses' => [
          90 => [NUMBER_X => 1],
          91 => [SABOTAGE => 1],
        ],
        'name' => clienttranslate("4th Plant quarter bonus"),
      ],
      // JOKER
      [
        'slots' => [46, 47],
        'bonuses' => [
          92 => [ROCKET => 2],
          93 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("1st Joker quarter bonus"),
      ],
      [
        'slots' => [48, 49],
        'bonuses' => [
          94 => [ROCKET => 2],
          95 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("2nd Joker quarter bonus"),
      ],
      [
        'slots' => [50, 51],
        'bonuses' => [
          96 => [ROCKET => 2],
          97 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("3rd Joker quarter bonus"),
      ],
      [
        'slots' => [52, 53],
        'bonuses' => [
          98 => [ACTIVATION => 1],
          99 => [NUMBER_X => 1],
        ],
        'name' => clienttranslate("4th Joker quarter bonus"),
      ],
    ];
  }
}
