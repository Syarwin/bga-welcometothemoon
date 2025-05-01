<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra8 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 20;
    $this->levelMultiplier = 3;
    $this->nBonuses = 6;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [];
  }
}
