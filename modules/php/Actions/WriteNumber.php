<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Managers\Meeples;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Tiles;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Helpers\FlowConvertor;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Managers\Susan;

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

  public function actWriteNumber(string $slotId, int $number)
  {
    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slotId, $number);
    Notifications::writeNumber($player, $number, [$scribble]);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble);
    if (!empty($reactions)) {
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'childs' => $reactions
      ]);
    }
  }
}
