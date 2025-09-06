<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;

class AstraTurn extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_S8_ASTRA_TURN;
  }

  /*
  * Given a number/action combination (as assoc array), compute the set of writtable numbers on the sheet
  */
  public static function getAvailableNumbersOfCombination($player, $combination)
  {
    $numbers = [$combination['number']];
    $result = [];
    foreach ($numbers as $number) {
      $slots = $player->scoresheet()->getAvailableSlotsForNumber($number, $combination['action']);
      if (!empty($slots)) {
        $result[$number] = $slots;
      }
    }
    return $result;
  }


  public function argsAstraTurn()
  {
    $player = Players::getSolo();
    $card = ConstructionCards::getInLocation('stack-0')->first();
    $combination = ['number' => $card->getNumber(), 'action' => '']; // Give a fake action to make sure it's not using astronaut
    return [
      'numbers' => self::getAvailableNumbersOfCombination($player, $combination),
    ];
  }

  public function actAstraTurn(string $slot, int $number)
  {
    $args = $this->getArgs();
    $slots = $args['numbers'][$number] ?? [];
    if (!in_array($slot, $slots)) {
      throw new \BgaUserException('You cannot write this number here. Should not happen.');
    }

    $player = Players::getSolo();
    $scribble = $player->scoresheet()->addScribble($slot, SCRIBBLE_INSIGNA_TRIANGLE);
    Notifications::writeNumberS8AstraTurn($player, $number, [$scribble]);

    // Reaction to the scribble itself (filled up quarter bonuses, etc)
    $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actWriteNumber');

    // Action corresponding to the combination
    $card = ConstructionCards::getInLocation('stack-0')->first();
    $combination = ['number' => $card->getNumber(), 'action' => $card->getAction()];
    if ($combination['action'] == ASTRONAUT) $combination['action'] = PLANNING;
    if ($combination['action'] == ENERGY) $combination['action'] = ROBOT;

    $action = $player->scoresheet()->getCombinationAtomicAction($combination, $slot);

    $this->insertAsChild($reactions);
    $this->insertAsChild($action);
  }
}
