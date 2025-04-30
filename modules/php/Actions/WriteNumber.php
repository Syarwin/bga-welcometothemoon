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
    $this->insertAsChild($reactions);

    // Action corresponding to the combination
    $action = $player->scoresheet()->getCombinationAtomicAction($player->getCombination(), $slot);
    $this->incStat($player->getCombination()['action'], $player->getId());
    if (!is_null($action)) {
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
