<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;


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

  public static function getUiData()
  {
    return self::getInLocation('stack-%')->ui();
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
}
