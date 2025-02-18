<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Globals;

class ReplaceSoloCard extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isIrreversible(?Player $player = null): bool
  {
    return true;
  }

  public function stReplaceSoloCard()
  {
    return [];
  }

  public function actReplaceSoloCard()
  {
    // Discard existing card
    $card = ConstructionCards::getSingle($this->getCtxArg('cardId'));
    $stack = $card->getLocation();
    ConstructionCards::moveAllInLocation($stack, 'discard', 0);
    // Draw a new one
    $drawnCard = ConstructionCards::pickOneForLocation("deck", $stack, 0);
    $player = $this->getPlayer();
    $stack = SOLO_CARDS_STACKS[$card->getId()];
    Notifications::replaceSoloCard($player, $stack, $card, $drawnCard);

    if ($drawnCard->getAction() == SOLO) {
      $astra = Players::getAstra();
      $flow = $astra->onDrawingSoloCard($drawnCard);
      if (Globals::getMode() == \MODE_PRIVATE) {
        $this->ctx->getRoot()->pushChild(Engine::buildTree($flow));
      }
    }
  }
}
