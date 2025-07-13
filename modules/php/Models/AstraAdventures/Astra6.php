<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
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
    $virus = null;
    $player = Players::getActive();
    $scoresheet = $player->scoresheet();

    if ($isFirstDraw) {
      foreach ([VIRUS_GREEN, VIRUS_BLUE] as $potentialVirus) {
        [, $virusSlot] = Scoresheet6::getViruses()[$potentialVirus];
        if (!$scoresheet->hasScribbledSlot($virusSlot)) {
          $virus = $potentialVirus;
          break;
        }
      }
    } else {
      $virus = [
        'A' => VIRUS_RED,
        'B' => VIRUS_PURPLE,
        'C' => VIRUS_YELLOW,
      ][$stack];
    }

    Globals::setPropagations([$player->getId() => 1]);

    if (!is_null($virus)) {
      $this->activateVirus($virus, $scoresheet);
      return [
        [
          'action' => S6_PROPAGATE,
        ]
      ];
    } else {
      return [
        [
          'action' => S6_CROSS_OFF_PROPAGATION_SYMBOL,
        ],
        [
          'action' => S6_PROPAGATE,
        ]
      ];
    }
  }

  private function activateVirus(int $virus, Scoresheet $scoresheet): void
  {
    [$linkedVirusSlot, $virusSlot] = Scoresheet6::getViruses()[$virus];

    $scribbles = [$scoresheet->addScribble($virusSlot, SCRIBBLE_CIRCLE)];
    if (!$scoresheet->hasScribbledSlot($linkedVirusSlot)) {
      $scribbles[] = $scoresheet->addScribble($linkedVirusSlot);
    }
    Notifications::activateVirus($virus, $scribbles);
  }
}
