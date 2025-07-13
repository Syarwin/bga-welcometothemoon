<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class CrossOffPropagationSymbol extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S6_CROSS_OFF_PROPAGATION_SYMBOL;
  }

  protected array $slots = [217, 218, 220, 221];

  public function actCrossOffPropagationSymbol(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot);

    Notifications::crossOffPropagationSymbolAstra($player, $scribble);
  }
}
