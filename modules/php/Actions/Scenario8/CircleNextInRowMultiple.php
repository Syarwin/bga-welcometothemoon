<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleNextInRowMultiple extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    $planetSlots = $this->getCtxArg('planet')['slots'];
    return $player->scoresheet()->countScribbledSlots($planetSlots) < count($planetSlots);
  }

  public function getDescription(): string
  {
    return '';
  }

  public function stCircleNextInRowMultiple()
  {
    return [];
  }

  public function actCircleNextInRowMultiple()
  {
    foreach ([$this->getCtxArg('planet'), $this->getCtxArg('scoring')] as $childArgs) {
      $this->insertAsChild([
        'action' => CIRCLE_NEXT_IN_ROW,
        'args' => $childArgs
      ]);
    }
  }
}
