<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

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
    $viruses = [];
    $player = Players::getActive();
    $scoresheet = $player->scoresheet();

    if ($isFirstDraw) {
      foreach ([VIRUS_GREEN, VIRUS_BLUE] as $potentialVirus) {
        [, $virusSlot] = Scoresheet6::getViruses()[$potentialVirus];
        if (!$scoresheet->hasScribbledSlot($virusSlot)) {
          $viruses[] = $potentialVirus;
        }
      }
    } else {
      $viruses[] = [
        'A' => VIRUS_RED,
        'B' => VIRUS_PURPLE,
        'C' => VIRUS_YELLOW,
      ][$stack];
    }

    $states = [
      [
        'action' => S6_PROPAGATE,
        'args' => [
          'isAstra' => true,
        ]
      ]
    ];

    if (empty($viruses)) {
      $states = [['action' => S6_CROSS_OFF_PROPAGATION_SYMBOL], ...$states];
    } else if (count($viruses) === 1) {
      Scoresheet6::activateViruses($viruses, [$player->getId() => 0]);
    } else {
      $states = [['action' => S6_CROSS_OFF_VIRUS], ...$states];
    }
    return $states;
  }
}
