<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class StirWaterTanks extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_STIR_WATER_TANKS;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    return !is_null($this->getArgs()['slot']);
  }

  public function stStirWaterTanks()
  {
    $pref = Players::getCurrent()->getPref(OPTION_WATER_ACTION);
    return $pref === OPTION_WATER_AUTOMATIC ? [] : null;
  }

  public function argsStirWaterTanks()
  {
    $slot = $this->getCtxArgs()['slot'];
    return [
      'slot' => self::$waterTanksAtSlots[$slot] ?? null,
    ];
  }

  public function actStirWaterTanks()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $slot = $this->getArgs()['slot'];

    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::stirWaterTanks($player, $scribble, self::$waterTanksValues[$slot]);
  }

  private static array $waterTanksAtSlots = [
    2 => 62,
    5 => 63,
    7 => 64,
    11 => 65,
    14 => 66,
    18 => 67,
    19 => 68,
    23 => 69,
    26 => 70,
    30 => 71,
    32 => 72,
    35 => 73,
  ];

  public static array $waterTanksValues = [
    62 => 4,
    63 => 4,
    64 => 5,
    65 => 6,
    66 => 7,
    67 => 8,
    68 => 8,
    69 => 7,
    70 => 6,
    71 => 5,
    72 => 4,
    73 => 4,
  ];
}
