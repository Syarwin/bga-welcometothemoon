<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class WriteX extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_WRITE_X;
  }

  public function getDescription(): string
  {
    return clienttranslate("Write X");
  }

  public function isOptional(): bool
  {
    return $this->getPlayer()->scoresheet()->isWriteXOptional();
  }

  protected function getSlots(Player $player): array
  {
    return $player->scoresheet()->getAvailableSlotsForNumber(NUMBER_X, JOKER);
  }

  public function actWriteX(string $slotId)
  {
    $this->sanityCheck($slotId);

    $number = NUMBER_X;
    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slotId, $number);
    $scribbles = [$scribble];
    // Scribble the bonus slot
    $source = $this->getCtxArg('source');
    if (isset($source['slot'])) {
      $scribbles[] = $player->scoresheet()->addScribble($source['slot']);
    }
    Notifications::writeNumber($player, $number, $scribbles, $source['name'] ?? null);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actWriteX');
    if (!empty($reactions)) {
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'childs' => $reactions
      ]);
    }
  }
}
