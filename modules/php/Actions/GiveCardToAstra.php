<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class GiveCardToAstra extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GIVE_CARD_TO_ASTRA;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return !is_null($this->stGiveCardToAstra());
  }

  public function argsGiveCardToAstra()
  {
    $player = $this->getPlayer();
    $astra = Players::getAstra();
    $mayUseSoloBonus = !is_null($astra->getNextAvailableSoloBonus());

    $combination = $player->getCombination();
    $stacks = array_values(array_diff([0, 1, 2], $combination['stacks'] ?? []));

    return [
      'mayUseSoloBonus' => $mayUseSoloBonus,
      'stacks' => $stacks,
    ];
  }

  public function stGiveCardToAstra()
  {
    $args = $this->getArgs();
    if (!$args['mayUseSoloBonus'] && count($args['stacks']) == 1) {
      return [$args['stacks'][0]];
    }
  }

  public function actGiveCardToAstra($stack)
  {
    $player = $this->getPlayer();
    $args = $this->getArgs();
    if (!in_array($stack, $args['stacks'])) {
      throw new \BgaUserException('You cannot select this stack. Should not happen.');
    }

    $card = ConstructionCards::getInLocation("stack-$stack")->first();
    if (is_null($card)) {
      throw new \BgaUserException('No card in this stack. Should not happen.');
    }

    $card->setLocation('astra');
    Notifications::giveCardToAstra($player, $card);

    // Any reactions ?
    $astra = Players::getAstra();
    $astra->onReceivingCard($card);
  }


  public function actUseSoloBonus()
  {
    $player = $this->getPlayer();
    $astra = Players::getAstra();
    $location = $astra->getNextAvailableSoloBonus();
    $scribble = $astra->addScribble($location);
    Notifications::useSoloBonus($player, $scribble);
  }
}
