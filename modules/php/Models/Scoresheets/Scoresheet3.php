<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario3\BuildRobotTunnel;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Quarter;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../constants.inc.php";
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

  public static function getQuarters()
  {
    return array_map(function ($data) {
      return new Quarter($data);
    }, [
      [0, clienttranslate('top-left'), [3, 4, 5, 9, 10, 11, 15, 16], [[63], [64], [65], [66, 67]]],
      [1, clienttranslate('top-right'), [19, 20, 21, 25, 26, 27, 31, 32], [[68], [69], [70], [71, 72]]],
      [2, clienttranslate('bottom-left'), [1, 2, 6, 7, 8, 12, 13, 14], [[73], [74], [75], [76, 77]]],
      [3, clienttranslate('bottom-right'), [17, 18, 22, 23, 24, 28, 29, 30], [[78], [79], [80], [81, 82]]],
    ]);
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    switch ($combination['action']) {
      case WATER:
        return ['action' => STIR_WATER_TANKS, 'args' => ['slot' => $slot, 'waterTanksSlots' => $this->waterTanksAtSlots]];
      case PLANT:
        $quarterId = current(array_filter(self::getQuarters(), function ($quarter) use ($slot) {
          return $quarter->hasSlot($slot);
        }))->getId();
        return ['action' => CIRCLE_GREENHOUSE, 'args' => ['quarterId' => $quarterId]];
      case ENERGY:
        return ['action' => IMPROVE_BONUS];
      case PLANNING:
        return [
          'action' => WRITE_X,
          'args' => [
            'source' => [
              'name' => clienttranslate('Planning action'),
              'slot' => $this->getFirstUnscribbled($this->getSectionSlots('planningmarkers'))
            ],
          ]
        ];
      case ROBOT:
        return ['action' => BUILD_ROBOT_TUNNEL];
    }
    return null;
  }

  public function getScribbleReactions(Scribble $scribble): array
  {
    if (!in_array($scribble->getSlot(), $this->getSectionSlots('numbers'))) return [];

    // Check antennas
    $scribbles = BuildRobotTunnel::scribblesConnectedAntennas($this);
    if (!empty($scribbles)) {
      Notifications::circleAntennas($this->player, $scribbles);
    }

    return [];
  }
}
