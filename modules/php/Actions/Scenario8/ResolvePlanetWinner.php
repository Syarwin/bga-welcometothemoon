<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class ResolvePlanetWinner extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function getDescription(): string
  {
    return '';
  }

  public function stResolvePlanetWinner()
  {
    return [];
  }

  public function actResolvePlanetWinner()
  {
    $player = $this->getPlayer();
    $planetId = $this->getCtxArg('planetId');
    $scoresheet = $player->scoresheet();
    $endOfGame = ($this->getCtxArg('endOfGame') ?? false);
    $scoresheet->resolvePlanetWinnerIfNeeded($player, $planetId, $endOfGame);
  }
}
