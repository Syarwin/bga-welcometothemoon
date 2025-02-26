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
  public function getPlayableCombinations($player, $canUseJoker = false)
  {
    $combinations = ConstructionCards::getPossibleCombinations($canUseJoker);
    $result = [];
    foreach ($combinations as $combination) {
      if (!empty(WriteNumber::getAvailableNumbersOfCombination($player, $combination))) {
        array_push($result, $combination);
      }
    }
    return $result;
  }


  public function argsChooseCards()
  {
    $player = $this->getPlayer();
    $data = [];
    $data['combinations'] = $this->getPlayableCombinations($player);
    if (empty($data['combinations'])) {
      $data['systemError'] = $player->scoresheet()->getNextFreeSystemErrorSlot();
      $data['descSuffix'] = Globals::getScenario() == 1 ? 'impossible1' : 'impossible';
    }

    // Joker action
    if ($player->scoresheet()->canUseJoker()) {
      $data['jokerCombinations'] = $this->getPlayableCombinations($player, true);
    }

    return $data;
  }

  public function actChooseCards(array $combination, bool $useJoker = false)
  {
    $player = $this->getPlayer();
    $args = $this->getArgs();
    if ($useJoker && !isset($args['jokerCombinations'])) {
      throw new \BgaUserException('You dont have any joker to use. Should not happen.');
    }

    $combinations = $useJoker ? $args['jokerCombinations'] : $args['combinations'];
    if (!in_array($combination, $combinations)) {
      throw new \BgaUserException('You cannot select this combination. Should not happen.');
    }

    if ($useJoker) {
      $scoresheet = $player->scoresheet();
      $scribble = $scoresheet->addScribble($scoresheet->getFirstUnscribbledJoker());
      Notifications::addScribble($player, $scribble);
    }

    PGlobals::setCombination($player->getId(), $combination);
    Notifications::chooseCards($player, $player->getCombination(), $useJoker);

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
    PGlobals::setCombination($player->getId(), []);
    $scribbleType = Globals::getScenario() == 1 ? SCRIBBLE_CIRCLE : SCRIBBLE;
    $scribble = $player->scoresheet()->addScribble($args['systemError'], $scribbleType);
    Notifications::systemError($player, $scribble);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actSystemError');
    if (!empty($reactions)) {
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'childs' => $reactions
      ]);
    }
  }
}
