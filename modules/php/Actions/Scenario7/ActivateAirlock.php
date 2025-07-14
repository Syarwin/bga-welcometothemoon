<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario7;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet7;

class ActivateAirlock extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S7_ACTIVATE_AIRLOCK;
  }

  public function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $blockInfos = Scoresheet7::getBlockInfos();
    $blockIds = Scoresheet7::getConnectedBlocks($scoresheet);
    $slots = [];
    foreach ($blockIds as $blockId) {
      foreach ($blockInfos[$blockId]['links'] as $slot => $linkedBlockId) {
        if (!$scoresheet->hasScribbledSlot($slot)) {
          $slots[] = $slot;
        }
      }
    }

    return $slots;
  }

  public function getDescription(): string
  {
    return clienttranslate("Activate an airlock");
  }

  public function actActivateAirlock(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles = [];
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_LINE);

    Notifications::activateAirlock($player, $scribbles, $this->getCtxArg('bonus') ?? false);
  }
}
