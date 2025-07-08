<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario5;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class BuildDome extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S5_BUILD_DOME;
  }

  protected function getSlots(Player $player): array
  {
    $sections = $player->scoresheet()->getUnbuiltDomeSections();
    if (empty($sections)) {
      return [];
    }

    // Keep only first and last unbuilt sections
    $sections = [$sections[0], $sections[count($sections) - 1]];
    // Check parity
    $slots = [];
    foreach ($sections as [$slot, $parity]) {
      if ($parity == $this->getCtxArg('parity')) {
        $slots[] = $slot;
      }
    }

    return $slots;
  }

  public function actBuildDome(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_LINE);

    Notifications::buildDome($player, $scribble);
  }
}
