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
    // if (empty($data['selectableStacks'])) {
    //   $data['zones'] = PermitRefusal::getAvailableZones($player);
    // }

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


  // // Do the action (logging the choice for rest of the turn)
  // $player->chooseCards($stack);
  //   /*
  //    *  Return the set of possible number to write given current selected combination
  //    */
  //   // public function getAvailableNumbers()
  //   // {
  //   //   return $this->getAvailableNumbersOfCombination($this->getCombination());
  //   // }

  //     /*
  //  * Return either the selected stacks (of construction cards) if any, or null
  //  */
  // public function getSelectedCards()
  // {
  //   $selectCardAction = Log::getLastAction('selectCard', $this->id);
  //   return is_null($selectCardAction) ? null : $selectCardAction['arg'];
  // }

  // /*
  //  * Allow to format the selected stacks (getter defined below)
  //  *   into a combination [number, action]
  //  */
  // public function getCombination()
  // {
  //   $selectedCards = $this->getSelectedCards();
  //   if (is_null($selectedCards)) {
  //     throw new \BgaVisibleSystemException(
  //       "Trying to fetch the combination of a player who haven't choose the construction cards yet"
  //     );
  //   }

  //   return ConstructionCards::getCombination($this->id, $selectedCards);
  // }

}
