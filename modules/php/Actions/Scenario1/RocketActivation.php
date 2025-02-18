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

  protected array $slots = [
    152,
    153,
    154,
    155,
    156,
    157,
    158,
  ];
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
