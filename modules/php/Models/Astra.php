<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

/*
 * Astra=> all utility functions concerning Astra
 */

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;

const OPPONENTS = [
  1 => [
    "name" => 'Katherine',
    "mult" => [2, 3, 2, 3, 1, 4],
  ],
  2 => [
    "name" => 'Alexei',
    "mult" => [2, 3, 3, 2, 4, 3],
  ],
  3 => [
    "name" => 'Margaret',
    "mult" => [5, 4, 2, 2, 2, 3],
  ],
  4 => [
    "name" => 'Franklin',
    "mult" => [2, 6, 4, 3, 3, 2],
  ],
  5 => [
    "name" => 'Sergei',
    "mult" => [4, 4, 4, 4, 4, 3],
  ],
  6 => [
    "name" => 'Stephanie',
    "mult" => [6, 2, 4, 5, 4, 4],
  ],
  7 => [
    "name" => 'Thomas',
    "mult" => [5, 4, 3, 6, 5, 4],
  ],
  8 => [
    "name" => 'Peggy',
    "mult" => [5, 3, 6, 3, 6, 6],
  ],
];

include_once dirname(__FILE__) . "/../constants.inc.php";
const ACTIONS = [ROBOT, ENERGY, PLANT, WATER, ASTRONAUT, PLANNING];

class Astra
{
  protected string $name;
  protected int $level;
  protected array $multipliers;
  protected int $fixedScore;
  protected int $levelMultiplier;
  protected array $scribblesByLocation;
  protected int $nBonuses;

  public function __construct()
  {
    $this->level = Globals::getAstraLevel();
    $opponent = OPPONENTS[$this->level];
    $this->name = $opponent['name'];
    $this->multipliers = [];
    foreach (ACTIONS as $i => $action) {
      $this->multipliers[$action] = $opponent['mult'][$i];
    }

    foreach (Scribbles::getInLocation("astra-%") as $scribble) {
      $this->scribblesByLocation[$scribble->getLocation()][] = $scribble;
    }
  }

  public function setupScenario(): void {}

  /**
   * Scribbles and bonuses
   */
  public function addScribble($location, $type = SCRIBBLE): Scribble
  {
    $scribble = Scribbles::add(0, [
      'type' => $type,
      'location' => $location,
    ]);
    $this->scribblesByLocation[$scribble->getLocation()][] = $scribble;
    return $scribble;
  }

  public function hasScribbledLocation(string $location, ?int $type = null): bool
  {
    foreach (($this->scribblesByLocation[$location] ?? []) as $scribble) {
      if (is_null($type) || $scribble->getType() == $type) {
        return true;
      }
    }

    return false;
  }


  public function circleNextBonus(): ?Scribble
  {
    for ($i = 0; $i < $this->nBonuses; $i++) {
      $location = "astra-bonus-$i";
      if (!$this->hasScribbledLocation($location)) {
        return $this->addScribble($location, SCRIBBLE_CIRCLE);
      }
    }
    return null;
  }

  public function getNextAvailableSoloBonus(): ?string
  {
    for ($i = 0; $i < $this->nBonuses; $i++) {
      $location = "astra-bonus-$i";
      if ($this->hasScribbledLocation($location, SCRIBBLE_CIRCLE) && !$this->hasScribbledLocation($location, SCRIBBLE)) {
        return $location;
      }
    }
    return null;
  }

  /**
   * SOLO CARDS
   */
  public function onDrawingSoloCard(ConstructionCard $card): array
  {
    $player = Players::getActive();
    $stack = SOLO_CARDS_STACKS[$card->getId()];
    $firstDraw = Globals::getSoloDraw() == 0;

    // First draw => log message
    if ($firstDraw) {
      Notifications::pmidMessage($player, clienttranslate('Drawing solo card ${stack} for the first time'), [
        'stack' => $stack
      ]);
    } // Second draw => flip over corresponding plan cards
    else {
      $plan = PlanCards::getInLocation("stack-$stack")->first();
      $planId = $plan->getId();
      $validationScribble = Scribbles::getSingle("0-$planId", false);

      // Mark the plan as validated
      if (is_null($validationScribble)) {
        $scribble = Scribbles::add(0, [
          'location' => "plan-$planId",
          'type' => SCRIBBLE,
        ]);
        $msg = clienttranslate('Drawing solo card ${stack} for the second time: corresponding mission card can no longer be accomplished for maximum points');
        Notifications::addScribble($player, $scribble, $msg, [
          'stack' => $stack,
          'duration' => 1500,
          'plansUpdateNeeded' => true
        ]);
      } // Otherwise just notif
      else {
        Notifications::pmidMessage($player, clienttranslate('Drawing solo card ${stack} for the second time'), [
          'stack' => $stack
        ]);
      }
    }

    $childs = $this->getAstraEffect($stack, $firstDraw);
    $childs[] = [
      'action' => REPLACE_SOLO_CARD,
      'args' => ['cardId' => $card->getId()]
    ];
    return count($childs) == 1 ? $childs[0] : ['type' => NODE_SEQ, 'childs' => $childs];
  }

  public function getAstraEffect(string $stack, bool $isFirstDraw): array
  {
    return [];
  }

  public function isEndOfGameTriggered(): bool
  {
    return false;
  }

  /**
   * @return int
   */
  public function getCardsByActionMap(): array
  {
    $nCardsByAction = [];
    foreach (ACTIONS as $action) {
      $nCardsByAction[$action] = 0;
    }
    foreach (ConstructionCards::getInLocation("astra") as $card) {
      $nCardsByAction[$card->getAction()]++;
    }
    return $nCardsByAction;
  }

  /**
   * UI DATA
   */
  public function getUiData(): array
  {
    $data = [];
    $totalScore = 0;

    // Count number of cards of each action
    $nCardsByAction = $this->getCardsByActionMap();

    // Score for each icon category
    foreach (ACTIONS as $action) {
      $n = $nCardsByAction[$action];
      $categoryScore = $n * $this->multipliers[$action];
      $data[] = ['slot' => "astra-$action-count", 'v' => $n];
      $data[] = ['slot' => "astra-$action-score", 'v' => $categoryScore];
      $totalScore += $categoryScore;
    }

    // Fixed score
    $data[] = ['slot' => 'astra-fixed-score', 'v' => $this->fixedScore];
    $totalScore += $this->fixedScore;

    // Level score
    $levelScore = $this->level * $this->levelMultiplier;
    $data[] = ['slot' => 'astra-level-score', 'v' => $levelScore];
    $totalScore += $levelScore;

    // Total score
    $data[] = ['slot' => 'astra-total-score', 'v' => $totalScore, 'overview' => "total"];

    return $data;
  }

  public function getScore(): int
  {
    $data = $this->getUiData();
    foreach ($data as $entry) {
      if ($entry['slot'] == 'astra-total-score') {
        return $entry['v'];
      }
    }
    return 0;
  }

  // Listener (only useful for scenario 1 probably)
  public function onReceivingCard(ConstructionCard $card): void {}
}
