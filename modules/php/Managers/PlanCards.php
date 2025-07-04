<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlanCards extends CachedPieces
{
  protected static string $table = 'plan_cards';
  protected static string $prefix = 'card_';
  protected static array $customFields = [];
  protected static ?Collection $datas = null;
  protected static bool $autoremovePrefix = false;
  protected static bool $autoIncrement = false;

  protected static function cast($row): PlanCard
  {
    $className = "Bga\\Games\\WelcomeToTheMoon\\Models\\PlanCards\\PlanCard" . $row['card_id'];
    return new $className($row);
  }


  public static function getCurrent()
  {
    return self::getInLocation('stack-%');
  }

  public static function getUiData()
  {
    return self::getCurrent()->ui();
  }

  protected static $plansByScenario = [
    1 => [
      'A' => [64, 65],
      'B' => [66, 67],
      'C' => [68, 69],
    ],
    2 => [
      'A' => [70, 71],
      'B' => [72, 73],
      'C' => [74, 75],
    ],
    3 => [
      'A' => [76, 77],
      'B' => [78, 79],
      'C' => [80, 81],
    ],
    4 => [
      'A' => [82, 83],
      'B' => [84, 85],
      'C' => [86, 87],
    ],
    5 => [
      'A' => [88, 89],
      'B' => [90, 91],
      'C' => [92, 93],
    ],
    6 => [
      'A' => [94, 95],
      'B' => [96, 97],
      'C' => [98, 99],
    ],
    7 => [
      'A' => [100, 101],
      'B' => [102, 103],
      'C' => [104, 105],
    ],
    8 => [
      'A' => [106, 107],
      'B' => [108, 109],
      'C' => [110, 111],
    ],
  ];


  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////


  /* Setup plan cards for a scenario */
  public static function setupScenario(int $scenario): void
  {
    self::DB()->delete()->run();

    // Keep only relevant cards
    $ids = [];
    foreach (self::$plansByScenario[$scenario] as $stack => $cardIds) {
      $i = bga_rand(0, count($cardIds) - 1);
      $ids[] = [
        'id' => $cardIds[$i],
        'location' => "stack-$stack",
        'nbr' => 1
      ];
    }

    $cards = self::create($ids);
    // TODO : notify
  }


  public static function getAccomplishablePlans(Player $player)
  {
    return self::getCurrent()->filter(fn($plan) => !$plan->isValidated($player) && $plan->canAccomplish($player));
  }
}
