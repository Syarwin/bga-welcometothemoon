<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra5 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 10;
    $this->levelMultiplier = 2;
    $this->nBonuses = 8;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    return [
      [
        'action' => S5_CROSS_OFF_SKYSCRAPER_BONUS,
      ]
    ];
  }
}
