<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;


class Scoresheet1 extends Scoresheet
{
  protected int $scenario = 1;



  protected array $increasingConstraints = [
    // ASTRONAUT
    [1, 2, 3],
    [4, 5, 6],
    // WATER
    [8, 9, 10, 11, 12],
    // ROBOT
    [13, 14, 15, 16, 17],
    [18, 19, 20, 21, 22],
    // PLANNING
    [23, 24, 25, 26, 27, 28],
    // ENERGY
    [29, 30, 31, 32, 33, 34, 35, 36, 37, 38],
    // PLANT
    [39, 40, 41, 42, 43, 44, 45, 46],
    // JOKER
    [47, 48, 49, 50, 51, 52, 53, 54]
  ];
  public function getAvailableSlotsForNumber(int $number, string $action)
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


  protected array $quarters = [
    // ASTRONAUT
    [
      'slots' => [1, 2, 3],
      'bonus' => []
    ],
    [
      'slots' => [4, 5, 6],
      'bonus' => []
    ],
    // WATER
    [
      'slots' => [8, 9],
      'bonus' => []
    ],
    [
      'slots' => [10, 11, 12],
      'bonus' => []
    ],
    // ROBOT
    [
      'slots' => [13, 14],
      'bonus' => []
    ],
    [
      'slots' => [15, 16, 17],
      'bonus' => []
    ],
    [
      'slots' => [18, 19, 20, 21, 22],
      'bonus' => []
    ],
    // PLANNING
    [
      'slots' => [23, 24, 25, 26, 27, 28],
      'bonus' => []
    ],
    // ENERGY
    [
      'slots' => [29, 30, 31, 32, 33],
      'bonus' => []
    ],
    [
      'slots' => [34, 35],
      'bonus' => []
    ],
    [
      'slots' => [36, 37, 38],
      'bonus' => []
    ],
    // PLANT
    [
      'slots' => [39, 40],
      'bonus' => []
    ],
    [
      'slots' => [41, 42],
      'bonus' => []
    ],
    [
      'slots' => [43, 44],
      'bonus' => []
    ],
    [
      'slots' => [45, 46],
      'bonus' => []
    ],
    // JOKER
    [
      'slots' => [47, 48],
      'bonus' => []
    ],
    [
      'slots' => [49, 50],
      'bonus' => []
    ],
    [
      'slots' => [51, 52],
      'bonus' => []
    ],
    [
      'slots' => [53, 54],
      'bonus' => []
    ],
  ];


  /////////////////////////////
  //  ____        _        
  // |  _ \  __ _| |_ __ _ 
  // | | | |/ _` | __/ _` |
  // | |_| | (_| | || (_| |
  // |____/ \__,_|\__\__,_|
  //////////////////////////////
  protected array $datas = [
    "id"       => "scenario-1",
    "name"     => "The Launch",
    "jpgUrl"   => "./scenarios/scenario-1.jpg",
    "sections" => [
      [
        "id"       => "numbers",
        "name"     => "Numbers",
        "modes"    => [
          "show",
          "add"
        ],
        "elts"     => [
          [
            "id" => 1,
            "x"  => 372,
            "y"  => 137
          ],
          [
            "id" => 2,
            "x"  => 422,
            "y"  => 137
          ],
          [
            "id" => 3,
            "x"  => 475,
            "y"  => 137
          ],
          [
            "id" => 4,
            "x"  => 372,
            "y"  => 241
          ],
          [
            "id" => 5,
            "x"  => 425,
            "y"  => 242
          ],
          [
            "id" => 6,
            "x"  => 477,
            "y"  => 241
          ],
          [
            "id" => 8,
            "x"  => 305,
            "y"  => 344
          ],
          [
            "id" => 9,
            "x"  => 357,
            "y"  => 345
          ],
          [
            "id" => 10,
            "x"  => 426,
            "y"  => 346
          ],
          [
            "id" => 11,
            "x"  => 481,
            "y"  => 344
          ],
          [
            "id" => 12,
            "x"  => 540,
            "y"  => 341
          ],
          [
            "id" => 13,
            "x"  => 303,
            "y"  => 449
          ],
          [
            "id" => 14,
            "x"  => 354,
            "y"  => 450
          ],
          [
            "id" => 15,
            "x"  => 432,
            "y"  => 450
          ],
          [
            "id" => 16,
            "x"  => 483,
            "y"  => 449
          ],
          [
            "id" => 17,
            "x"  => 533,
            "y"  => 449
          ],
          [
            "id" => 18,
            "x"  => 321,
            "y"  => 551
          ],
          [
            "id" => 19,
            "x"  => 372,
            "y"  => 552
          ],
          [
            "id" => 20,
            "x"  => 422,
            "y"  => 553
          ],
          [
            "id" => 21,
            "x"  => 471,
            "y"  => 553
          ],
          [
            "id" => 22,
            "x"  => 522,
            "y"  => 552
          ],
          [
            "id" => 23,
            "x"  => 293,
            "y"  => 654
          ],
          [
            "id" => 24,
            "x"  => 347,
            "y"  => 657
          ],
          [
            "id" => 25,
            "x"  => 397,
            "y"  => 660
          ],
          [
            "id" => 26,
            "x"  => 448,
            "y"  => 658
          ],
          [
            "id" => 27,
            "x"  => 500,
            "y"  => 656
          ],
          [
            "id" => 28,
            "x"  => 551,
            "y"  => 653
          ],
          [
            "id" => 29,
            "x"  => 174,
            "y"  => 756
          ],
          [
            "id" => 30,
            "x"  => 224,
            "y"  => 760
          ],
          [
            "id" => 31,
            "x"  => 275,
            "y"  => 762
          ],
          [
            "id" => 32,
            "x"  => 327,
            "y"  => 764
          ],
          [
            "id" => 33,
            "x"  => 378,
            "y"  => 764
          ],
          [
            "id" => 34,
            "x"  => 452,
            "y"  => 763
          ],
          [
            "id" => 35,
            "x"  => 511,
            "y"  => 763
          ],
          [
            "id" => 36,
            "x"  => 584,
            "y"  => 762
          ],
          [
            "id" => 37,
            "x"  => 637,
            "y"  => 761
          ],
          [
            "id" => 38,
            "x"  => 691,
            "y"  => 757
          ],
          [
            "id" => 39,
            "x"  => 212,
            "y"  => 865
          ],
          [
            "id" => 40,
            "x"  => 262,
            "y"  => 869
          ],
          [
            "id" => 41,
            "x"  => 337,
            "y"  => 871
          ],
          [
            "id" => 42,
            "x"  => 386,
            "y"  => 871
          ],
          [
            "id" => 43,
            "x"  => 462,
            "y"  => 871
          ],
          [
            "id" => 44,
            "x"  => 511,
            "y"  => 871
          ],
          [
            "id" => 45,
            "x"  => 581,
            "y"  => 867
          ],
          [
            "id" => 46,
            "x"  => 632,
            "y"  => 864
          ],
          [
            "id" => 47,
            "x"  => 211,
            "y"  => 963
          ],
          [
            "id" => 48,
            "x"  => 263,
            "y"  => 968
          ],
          [
            "id" => 49,
            "x"  => 336,
            "y"  => 970
          ],
          [
            "id" => 50,
            "x"  => 388,
            "y"  => 971
          ],
          [
            "id" => 51,
            "x"  => 459,
            "y"  => 970
          ],
          [
            "id" => 52,
            "x"  => 511,
            "y"  => 970
          ],
          [
            "id" => 53,
            "x"  => 581,
            "y"  => 966
          ],
          [
            "id" => 54,
            "x"  => 632,
            "y"  => 963
          ]
        ],
        "eltClass" => "slot-number"
      ]
    ]
  ];
}
