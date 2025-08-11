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
  public static function getAvailableNumbersOfCombination($player, $combination)
  {
    // Unless the action is temporary agent, a combination is uniquely associated to a number
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
