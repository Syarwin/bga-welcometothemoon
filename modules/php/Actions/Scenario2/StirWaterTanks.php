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
    $args = $this->getCtxArgs();
    $slot = $args['slot'];
    $allSlots = $args['waterTanksSlots'];
    return [
      'slot' => $allSlots[$slot] ?? null,
    ];
  }

  public function actStirWaterTanks()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $args = $this->getArgs();
    $slot = $args['slot'];
    $values = $args['waterTanksValues'] ?? null;

    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::stirWaterTanks($player, $scribble, $values[$slot] ?? null);
  }
}
