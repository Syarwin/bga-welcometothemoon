<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class RocketActivation extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_ROCKET_ACTIVATION;
  }

  public function isDoable(Player $player): bool
  {
    return !empty($this->getFreeSlots($player));
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
  public function getFreeSlots(Player $player)
  {
    $scoresheet = $player->scoresheet();
    $slots = [];
    foreach ($this->slots as $slotId) {
      if (!$scoresheet->hasScribbledSlot($slotId)) {
        $slots[] = $slotId;
      }
    }
    return $slots;
  }

  public function argsRocketActivation()
  {
    $player = $this->getPlayer();
    $slots = $this->getFreeSlots($player);

    return [
      'slots' => $slots,
    ];
  }


  public function actRocketActivation(int $slot)
  {
    $args = $this->getArgs();
    if (!in_array($slot, $args['slots'])) {
      throw new \BgaUserException('You cannot activate this rocket bonus here. Should not happen.');
    }

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_ARROW);

    // Scribble the bonus slot
    $source = $this->getCtxArg('source');
    $scribbles[] = $player->scoresheet()->addScribble($source['slot']);

    Notifications::activateRocket($player, $scribbles, $source['name']);
  }
}
