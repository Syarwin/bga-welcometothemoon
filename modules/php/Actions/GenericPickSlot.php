<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Models\Player;

class GenericPickSlot extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function isDoable(Player $player): bool
  {
    return !empty($this->getFreeSlots($player));
  }

  protected array $slots = [];
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

  public function getArgs()
  {
    if (is_null($this->args)) {
      $player = $this->getPlayer();
      $slots = $this->getFreeSlots($player);
      $this->args = [
        'slots' => $slots,
        'action' => 'act' . $this->getClassName(),
      ];
    }
    return $this->args;
  }

  public function sanityCheck(int $slot): void
  {
    $args = $this->getArgs();
    if (!in_array($slot, $args['slots'])) {
      throw new \BgaUserException('You cannot activate this rocket bonus here. Should not happen.');
    }
  }
}
