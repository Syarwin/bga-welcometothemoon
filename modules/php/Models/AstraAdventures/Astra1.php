<?php

namespace Bga\Games\WelcomeToTheMoon\Models\AstraAdventures;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;

class Astra1 extends Astra
{
  public function __construct()
  {
    parent::__construct();
    $this->fixedScore = 0;
    $this->levelMultiplier = 0;
    $this->nBonuses = 9;
  }

  public function setupScenario(): void
  {
    $scribbles = [];
    $levelToRocketsMap = [
      1 => 14,
      2 => 11,
      3 => 9,
      4 => 7,
      5 => 5,
      6 => 3,
      7 => 1,
      8 => -1
    ];
    for ($i = 0; $i <= $levelToRocketsMap[$this->level]; $i++) {
      $scribbles[] = $this->addScribble("astra-rocket-slot-$i");
    }
  }

  public function getUiData(): array
  {
    $data = [];
    $totalScore = 10;

    // Rocket score
    $slotToScoreMap = [
      15 => 20,
      20 => 30,
      25 => 40,
      30 => 50,
      35 => 60,
      40 => 70,
      45 => 80,
      50 => 90,
      55 => 100,
      60 => 110,
      65 => 120,
      70 => 130,
      75 => 140,
      80 => 150,
    ];
    $nRockets = 0;
    for ($i = 0; $i <= 80; $i++) {
      $location = "astra-rocket-slot-$i";
      if (!$this->hasScribbledLocation($location)) {
        break;
      }
      $nRockets++;
    }

    foreach ($slotToScoreMap as $minRockets => $score) {
      if ($nRockets >= $minRockets) {
        $totalScore = $score;
      }
    }

    // Total score
    $data[] = ['overview' => 'rockets', 'v' => $nRockets, 'max' => 81];
    $data[] = ['slot' => 'astra-total-score', 'v' => $totalScore, 'overview' => "total"];

    return $data;
  }

  public function onReceivingCard(ConstructionCard $card): void
  {
    $nRocketsToCross = $this->multipliers[$card->getAction()];

    for ($i = 0; $i <= 80; $i++) {
      $location = "astra-rocket-slot-$i";

      if (!$this->hasScribbledLocation($location)) {
        $scribbles[] = $this->addScribble($location);
        if (count($scribbles) >= $nRocketsToCross) {
          break;
        }
      }
    }

    $player = Players::getCurrent();
    Notifications::astraCrossRockets($player, $nRocketsToCross, $scribbles);
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    return [
      [
        'action' => CROSS_OFF_SABOTAGE
      ]
    ];
  }

  public function isEndOfGameTriggered(): bool
  {
    // Any rocket not crossed off?
    // -> 80 is the slot id of the last rocket
    if (!$this->hasScribbledLocation("astra-rocket-slot-80")) return false;

    Notifications::endGameTriggered(null, 'astraLaunch');
    return true;
  }
}
