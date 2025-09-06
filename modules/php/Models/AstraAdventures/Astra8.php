<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;
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
    return 2;
  }

  public function setupScenario(): void
  {
    $pId = Players::getAll()->first();
    Scribbles::add($pId, [
      'type' => SCRIBBLE_INSIGNAS[2],
      'location' => "slot-221",
    ]);
    Scribbles::add($pId, [
      'type' => SCRIBBLE_INSIGNAS[1],
      'location' => "slot-222",
    ]);
  }

  public function playTurn(ConstructionCard $card): void
  {
    // var_dump($card->getNumber());
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
