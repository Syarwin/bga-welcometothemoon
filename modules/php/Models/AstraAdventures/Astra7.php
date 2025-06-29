<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra7 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 15;
    $this->levelMultiplier = 1;
    $this->nBonuses = 6;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [];
  }
}
