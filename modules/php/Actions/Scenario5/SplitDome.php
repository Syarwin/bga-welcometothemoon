<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario5;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class SplitDome extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S5_SPLIT_DOME;
  }

  protected array $slots = [
    114,
    115,
    116,
    117,
    118,
    119,
    120,
    121
  ];

  public function actSplitDome(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_LINE_ORTHOGONAL);

    Notifications::splitDome($player, $scribble);
  }
}
