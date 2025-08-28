<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

class Astra8 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 20;
    $this->levelMultiplier = 3;
    $this->nBonuses = 6;
  }

  public function getNo(): int
  {
    return 0;
  }

  public function setupScenario(): void
  {
    $pId = Players::getAll()->first();
    Scribbles::add($pId, [
      'type' => SCRIBBLE_INSIGNAS[0],
      'location' => "slot-221",
    ]);
    Scribbles::add($pId, [
      'type' => SCRIBBLE_INSIGNAS[1],
      'location' => "slot-222",
    ]);
  }

  public function addScribble($location, $type = SCRIBBLE): Scribble
  {
    $currentPlayer = Globals::getTurn() % 2 == 0 ? Players::getAll()->first() : $this;
    $currentInsigna = SCRIBBLE_INSIGNAS[$currentPlayer->getNo()];
    if (!in_array($type, [SCRIBBLE, SCRIBBLE_CIRCLE])) {
      $type = $currentInsigna;
    }

    $scribble = Scribbles::add(0, [
      'type' => $type,
      'location' => "slot-$location",
    ]);
    return $scribble;
  }


  public function getUiData(): array
  {
    return [];
  }


  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [];
  }
}
