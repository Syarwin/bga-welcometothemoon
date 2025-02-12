<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\PGlobals;

class GiveCardToAstra extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GIVE_CARD_TO_ASTRA;
  }



  public function argsGiveCardToAstra()
  {
    $player = $this->getPlayer();
    $mayUseSoloBonus = true; // TODO

    $stacks = array_values(array_diff([1, 2, 3], [])); // TODO

    return [
      'mayUseSoloBonus' => $mayUseSoloBonus,
      'stacks' => $stacks
    ];
  }

  public function actGiveCardToAstra($cardId)
  {
    $player = $this->getPlayer();
    $args = $this->getArgs();
    // if (!in_array($stack, $args['stacks'])) {
    //   throw new \BgaUserException('You cannot select this stack. Should not happen.');
    // }

    // PGlobals::setStack($player->getId(), [$stack]);
    // Notifications::chooseCards($player, $player->getCombination());

    // $this->insertAsChild([
    //   'action' => WRITE_NUMBER,
    // ]);
  }


  public function actUseSoloBonus() {}
}
