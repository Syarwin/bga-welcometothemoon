<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class ImproveBonus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_IMPROVE_BONUS;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $slots = [];
    foreach ($this->getCtxArg('data') as $dataBlock) {
      $slot = $scoresheet->getFirstUnscribbled(array_keys($dataBlock['slots']));
      if (!is_null($slot)) {
        $slots[] = $slot;
      }
    }
    return $slots;
  }

  public function actImproveBonus(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slot);
    $dataBlock = current(array_filter($this->getCtxArg('data'), function ($dataBlock) use ($slot) {
      return in_array($slot, array_keys($dataBlock['slots']));
    }));
    Notifications::improveBonus($player, $scribble, $dataBlock['name'], $dataBlock['slots'][$slot]);
  }
}
