<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra6 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 5;
    $this->levelMultiplier = 4;
    $this->nBonuses = 9;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [];
  }
}
