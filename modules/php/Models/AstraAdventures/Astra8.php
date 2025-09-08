<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;
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

  public function getName(): string
  {
    return clienttranslate('Astra');
  }

  public function scoresheet(): ?Scoresheet8
  {
    $player = Players::getSolo();
    // Even turn, play on "my" sheet
    if (Globals::getTurn() % 2 == 0) {
      return new Scoresheet8($player, $this, 2);
    } else {
      return new Scoresheet8($this, $player, 1);
    }
  }


  public function setupScenario(): void
  {
    $pId = Players::getSolo();
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
