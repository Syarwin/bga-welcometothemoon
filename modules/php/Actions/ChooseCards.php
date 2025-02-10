<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;

class ChooseCards extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_CHOOSE_CARDS;
  }


  /*
   * Return the stack combinations that leads to at least one writtable number
   */
  public function getAvailableStacks($player)
  {
    $combinations = ConstructionCards::getPossibleCombinations();
    $result = [];
    foreach ($combinations as $combination) {
      if (!empty(WriteNumber::getAvailableNumbersOfCombination($player, $combination))) {
        array_push($result, $combination['stacks']);
      }
    }
    return $result;
  }


  public function argsChooseCards()
  {
    $player = $this->getPlayer();
    if ($player->isZombie()) {
      return [];
    }

    $data = [];
    $data['stacks'] = $this->getAvailableStacks($player);
    if (empty($data['stacks'])) {
      $data['systemError'] = $player->scoresheet()->getNextFreeSystemErrorSlot();
      $data['descSuffix'] = Globals::getScenario() == 1 ? 'impossible1' : 'impossible';
    }

    return $data;
  }

  public function actChooseCards($stack)
  {
    $player = $this->getPlayer();
    $args = $this->getArgs();
    if (!in_array($stack, $args['stacks'])) {
      throw new \BgaUserException('You cannot select this stack. Should not happen.');
    }

    PGlobals::setStack($player->getId(), [$stack]);
    Notifications::chooseCards($player, $player->getCombination());

    $this->insertAsChild([
      'action' => WRITE_NUMBER,
    ]);
  }


  public function actSystemError()
  {
    $args = $this->getArgs();
    if (!array_key_exists('systemError', $args)) {
      throw new \BgaUserException('You have a valid number to place, you cannot refuse to place it. Should not happen.');
    }

    $player = $this->getPlayer();
    $scribbleType = Globals::getScenario() == 1 ? SCRIBBLE_CIRCLE : SCRIBBLE;
    $scribble = $player->scoresheet()->addScribble($args['systemError'], $scribbleType);
    Notifications::systemError($player, $scribble);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble);
    if (!empty($reactions)) {
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'childs' => $reactions
      ]);
    }
  }
}
