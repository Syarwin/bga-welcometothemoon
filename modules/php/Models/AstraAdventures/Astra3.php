<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra3 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 10;
    $this->levelMultiplier = 3;
    $this->nBonuses = 8;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if (!$isFirstDraw || $stack == 'C') return [];

    return [
      [
        'action' => CROSS_OFF_FILLED_QUARTER_BONUS,
      ]
    ];
  }
}
