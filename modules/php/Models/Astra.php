<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

/*
 * Astra=> all utility functions concerning Astra
 */

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;

const OPPONENTS =  [
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
  public function onDrawingSoloCard(ConstructionCard $card)
  {
    $stack = SOLO_CARDS_STACKS[$card->getId()];

    // First draw => log message
    if (Globals::getSoloDraw() == 2) {
      Notifications::midMessage(clienttranslate('Drawing solo card ${stack} for the first time'), [
        'stack' => $stack
      ]);
    }
    // Second draw => flip over corresponding plan cards
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
        Notifications::addScribble(null, $scribble, $msg, [
          'stack' => $stack,
          'duration' => 1500,
          'plansUpdateNeeded' => true
        ]);
      }
      // Otherwise just notif
      else {
        Notifications::midMessage(clienttranslate('Drawing solo card ${stack} for the second time'), [
          'stack' => $stack
        ]);
      }
    }

    return [];
  }

  /**
   * UI DATA
   */
  public function getUiData()
  {
    $data = [];
    $totalScore = 0;

    // Count number of cards of each action
    $nCardsByAction = [];
    foreach (ACTIONS as $action) {
      $nCardsByAction[$action] = 0;
    }
    foreach (ConstructionCards::getInLocation("astra") as $card) {
      $nCardsByAction[$card->getAction()]++;
    }

    // Score for each icon category
    foreach (ACTIONS as $action) {
      $n = $nCardsByAction[$action];
      $categoryScore = $n * $this->multipliers[$action];
      $data["astra-$action-count"] = $n;
      $data["astra-$action-score"] = $categoryScore;
      $totalScore += $categoryScore;
    }

    // Fixed score
    $data['astra-fixed-score'] = $this->fixedScore;
    $totalScore += $this->fixedScore;

    // Level score
    $levelScore = $this->level * $this->levelMultiplier;
    $data['astra-level-score'] = $levelScore;
    $totalScore += $levelScore;

    // Total score
    $data['astra-total-score'] = $totalScore;

    return $data;
  }

  // Listener (only useful for scenario 1 probably)
  public function onReceivingCard(ConstructionCard $card): void {}
}
