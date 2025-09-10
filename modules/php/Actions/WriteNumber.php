<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;

class WriteNumber extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_WRITE_NUMBER;
  }

  // public function isDoable($player)
  // {
  //   if ($this->getCtxArg('type') == 'normal') {
  //     return true;
  //   }

  //   list($tiles, $canPlace) = $this->getPlayableTiles($player, true);
  //   return $canPlace;
  // }

  /*
  * Given a number/action combination (as assoc array), compute the set of writtable numbers on the sheet
  */
  public static function getAvailableNumbersOfCombination($player, $combination, $usableJokers = -1)
  {
    // Unless the action is an astronaut agent, a combination is uniquely associated to a number
    $numbers = [$combination['number']];

    // For astronaut, we can do -2, -1, +1, +2 EXCEPT for first scenario
    if ($combination['action'] == ASTRONAUT && Globals::getScenario() != 1) {
      $modifiers = [-2, -1, 1, 2];
      foreach ($modifiers as $dx) {
        $n = $combination['number'] + $dx;
        if ($n < 0 || $n > 17) {
          continue;
        }

        array_push($numbers, $n);
      }
    }

    // Scenario 8 => can use stored astronaut bonuses
    if (Globals::getScenario() == 8) {
      if ($usableJokers === -1) {
        $scoresheet = $player->scoresheet();
        $slots = $scoresheet->getPlayerSectionSlots('astronautmarkers');
        $usableJokers = $scoresheet->countScribbledSlots($slots, SCRIBBLE_CIRCLE) - $scoresheet->countScribbledSlots($slots, SCRIBBLE);
      }

      $modifiers = [];
      $numbers = [];
      for ($dx = -2 * $usableJokers; $dx <= 2 * $usableJokers; $dx++) {
        $n = $combination['number'] + $dx;
        if ($n < 0 || $n > 17) {
          continue;
        }

        array_push($numbers, $n);
      }
    }

    // For each number, compute list of houses where we can write the number
    $result = [];
    foreach ($numbers as $number) {
      $slots = $player->scoresheet()->getAvailableSlotsForNumber($number, $combination['action']);
      if (!empty($slots)) {
        $result[$number] = $slots;
      }
    }
    return $result;
  }


  public function argsWriteNumber()
  {
    $player = $this->getPlayer();
    $combination = $player->getCombination();
    return [
      'numbers' => self::getAvailableNumbersOfCombination($player, $combination),
    ];
  }

  public function actWriteNumber(string $slot, int $number)
  {
    $args = $this->getArgs();
    $slots = $args['numbers'][$number] ?? [];
    if (!in_array($slot, $slots)) {
      throw new \BgaUserException('You cannot write this number here. Should not happen.');
    }
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    // SCENARIO 8 => have we used some astronaut jokers ?
    if (Globals::getScenario() == 8) {
      $combination = $player->getCombination();
      $scribbles = [];
      $circledJokers = array_filter($scoresheet->getPlayerSectionSlots('astronautmarkers'), fn($jokerSlot) => $scoresheet->hasScribbledSlot($jokerSlot, SCRIBBLE_CIRCLE));

      for ($jokers = 0; $jokers < 7; $jokers++) {
        $numbers = self::getAvailableNumbersOfCombination($player, $combination, $jokers);
        $slots = [];
        foreach ($numbers as $number => $numberSlots) {
          $slots = array_merge($slots, $numberSlots);
        }
        if (in_array($slot, $slots)) break;

        $jokerSlot = $scoresheet->getFirstUnscribbled($circledJokers, SCRIBBLE);
        if (is_null($jokerSlot)) {
          throw new \BgaUserException('Not enough astronaut joker to write on this planet. Should not happen.');
        }
        $scribbles[] = $scoresheet->addScribble($jokerSlot, SCRIBBLE);
      }
      if ($jokers > 0) {
        Notifications::useAstronautJoker($player, $scribbles);
      }
    }

    $scribble = $player->scoresheet()->addScribble($slot, $number);
    Stats::incNumberedSpacesNumber($player->getId(), 1);
    Stats::setEmptySlotsNumber($player->getId(), $player->scoresheet()->countAllUnscribbledSlots());
    Notifications::writeNumber($player, $number, [$scribble]);

    // Reaction to the scribble itself (filled up quarter bonuses, etc)
    $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actWriteNumber');

    // Action corresponding to the combination
    $combination = $player->getCombination();
    $action = $player->scoresheet()->getCombinationAtomicAction($combination, $slot);
    $this->incStat($combination['action'], $player->getId());

    // Insert reactions first, unless some specific cases:
    $actionBeforeReaction = false;
    // S4 : always cross off water/plant linked before extraction
    if (Globals::getScenario() == 4 && in_array($combination['action'], [WATER, PLANT]))  $actionBeforeReaction = true;
    // S5 : quarantine quarter after action
    if (Globals::getScenario() == 6 && in_array($combination['action'], [WATER, PLANT, ENERGY]))  $actionBeforeReaction = true;

    if ($actionBeforeReaction) {
      $this->insertAsChild($action);
      $this->insertAsChild($reactions);
    } else {
      $this->insertAsChild($reactions);
      $this->insertAsChild($action);
    }
  }

  private function incStat(string $action, int $pId): void
  {
    $methodUppercased = ucfirst($action);
    $methodName = "Bga\Games\WelcomeToTheMoon\Core\Stats::incUsed$methodUppercased";
    call_user_func($methodName, $pId, 1);
  }
}
