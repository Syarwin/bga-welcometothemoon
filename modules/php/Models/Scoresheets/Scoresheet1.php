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

  public function getScribbleReactions($scribble): array
  {
    $slot = $scribble->getSlot();

    // Number => check quarters
    if (1 <= $slot && $slot <= 53) {
      foreach ($this->quarters as $quarter) {
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
    $inQuarter = false;
    foreach ($slots as $slot2) {
      if (!$this->hasScribbledSlot($slot2)) {
        return false;
      }

      if ($slot2 == $slot) $inQuarter = true;
    }

    return $inQuarter;
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
        ]
      ];
    }

    return $actions;
  }

  protected array $quarters = [
    // ASTRONAUT
    [
      'slots' => [1, 2, 3],
      'bonuses' => [
        54 => [ROCKET => 3],
        55 => [ACTIVATION => 1],
        56 => [NUMBER_X => 1],
      ],
    ],
    [
      'slots' => [4, 5, 6],
      'bonuses' => [
        57 => [ROCKET => 2],
        58 => [ROCKET => 2, 'check' => 152],
        59 => [SABOTAGE => 1],
      ],
    ],
    // WATER
    [
      'slots' => [7, 8],
      'bonuses' => [
        60 => [ROCKET => 2],
        61 => [SABOTAGE => 1],
      ],
    ],
    [
      'slots' => [9, 10, 11],
      'bonuses' => [
        62 => [ROCKET => 3],
        63 => [ROCKET => 3, 'check' => 153],
      ],
    ],
    // ROBOT
    [
      'slots' => [12, 13],
      'bonuses' => [
        64 => [ACTIVATION => 1],
        65 => [NUMBER_X => 1],
      ],
    ],
    [
      'slots' => [14, 15, 16],
      'bonuses' => [
        66 => [ROCKET => 3],
        67 => [NUMBER_X => 1],
      ],
    ],
    [
      'slots' => [17, 18, 19, 20, 21],
      'bonuses' => [
        69 => [ROCKET => 3],
        69 => [ROCKET => 3, 'check' => 154],
        70 => [SABOTAGE => 1],
        71 => [SABOTAGE => 1],
      ],
    ],
    // PLANNING
    [
      'slots' => [22, 23, 24, 25, 26, 27],
      'bonuses' => [
        72 => [ROCKET => 4],
        73 => [ROCKET => 4, 'check' => 155],
        74 => [NUMBER_X => 1],
        75 => [SABOTAGE => 1],
      ]
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
    ],
    [
      'slots' => [33, 34],
      'bonuses' => [
        80 => [ROCKET => 3]
      ]
    ],
    [
      'slots' => [35, 36, 37],
      'bonuses' => [
        81 => [ROCKET => 2],
        82 => [ACTIVATION => 1],
        83 => [NUMBER_X => 1],
      ]
    ],
    // PLANT
    [
      'slots' => [38, 39],
      'bonuses' => [
        84 => [ROCKET => 2],
        85 => [ROCKET => 2, 'check' => 157],
      ]
    ],
    [
      'slots' => [40, 41],
      'bonuses' => [
        86 => [ROCKET => 2],
        87 => [ACTIVATION => 1],
      ]
    ],
    [
      'slots' => [42, 43],
      'bonuses' => [
        88 => [ROCKET => 2],
        89 => [ROCKET => 2, 'check' => 158],
      ]
    ],
    [
      'slots' => [44, 45],
      'bonuses' => [
        90 => [NUMBER_X => 1],
        91 => [SABOTAGE => 1],
      ]
    ],
    // JOKER
    [
      'slots' => [46, 47],
      'bonuses' => [
        92 => [ROCKET => 2],
        93 => [NUMBER_X => 1],
      ]
    ],
    [
      'slots' => [48, 49],
      'bonuses' => [
        94 => [ROCKET => 2],
        95 => [NUMBER_X => 1],
      ]
    ],
    [
      'slots' => [50, 51],
      'bonuses' => [
        96 => [ROCKET => 2],
        97 => [NUMBER_X => 1],
      ]
    ],
    [
      'slots' => [52, 53],
      'bonuses' => [
        98 => [ROCKET => 2],
        99 => [NUMBER_X => 1],
      ]
    ],
  ];
}
