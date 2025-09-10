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
    $scribbles = $scoresheet->resolvePlanetWinnerIfNeeded($player, $planetId);

    // SOLO EFFECT
    if (Globals::isSolo()) {
      // Have we won this planet ?
      $types = array_map(fn($scribble) => $scribble->getType(), $scribbles);
      if (in_array(SCRIBBLE_INSIGNA_SQUARE, $types)) {
        $scoresheet1 = Players::getSolo()->scoresheetForScore();
        $scoresheet2 = Players::getAstra()->scoresheetForScore();
        $controlledPlanets = $scoresheet->getControlledPlanetsAmount($scoresheet1, SCRIBBLE_INSIGNA_SQUARE) + $scoresheet->getControlledPlanetsAmount($scoresheet2, SCRIBBLE_INSIGNA_SQUARE);
        if ($controlledPlanets % 2 == 0) {
          $bonusScribble = Players::getAstra()->circleNextBonus();
          Notifications::gainOneSoloBonus($player, $bonusScribble);
        }
      }
    }
  }
}
