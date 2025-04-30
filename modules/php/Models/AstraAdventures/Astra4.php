<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra4 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 5;
    $this->levelMultiplier = 2;
    $this->nBonuses = 8;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [
      [
        'action' => S4_CROSS_OFF_FACTORY_BONUS,
      ]
    ];
  }
}
