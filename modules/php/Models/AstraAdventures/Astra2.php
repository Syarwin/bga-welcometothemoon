<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Models\Astra;

class Astra2 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 5;
    $this->levelMultiplier = 1;
    $this->nBonuses = 10;
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    if ($stack == 'C') return [];

    return [
      [
        'action' => CROSS_OFF_MULTIPLIER,
      ]
    ];
  }

  public function getUiData(): array
  {
    $data = parent::getUiData();
    $data[] = ['overview' => 'most-sections', 'v' => 0, 'details' => intdiv($this->getCardsByActionMap()[ENERGY], 2)];
    return $data;
  }
}
