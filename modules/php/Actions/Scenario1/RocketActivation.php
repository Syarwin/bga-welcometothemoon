<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class RocketActivation extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_ROCKET_ACTIVATION;
  }

  public function getDescription(): string
  {
    return clienttranslate('Activate an Inactive Rocket bonus');
  }

  protected function getSlots(Player $player): array
  {
    $slotsAndQuarter = [
      152 => [4, 5, 6],
      153 => [9, 10, 11],
      154 => [17, 18, 19, 20, 21],
      155 => [22, 23, 24, 25, 26, 27],
      156 => [28, 29, 30, 31, 32],
      157 => [38, 39],
      158 => [42, 43],
    ];
    $slots = [];
    // Filter out slot from a filled out quarter
    foreach ($slotsAndQuarter as $slot => $quarterSlots) {
      if (!$player->scoresheet()->hasScribbledSlots($quarterSlots)) {
        $slots[] = $slot;
      }
    }
    return $slots;
  }

  public function actRocketActivation(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_ARROW);

    // Scribble the bonus slot
    $source = $this->getCtxArg('source');
    $scribbles[] = $player->scoresheet()->addScribble($source['slot']);

    Notifications::activateRocket($player, $scribbles, $source['name']);
  }
}
