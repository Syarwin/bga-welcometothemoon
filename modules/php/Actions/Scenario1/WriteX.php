<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;

class WriteX extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_WRITE_X;
  }

  public function getDescription(): string
  {
    return clienttranslate("Write X");
  }

  // public function isDoable($player)
  // {
  //   if ($this->getCtxArg('type') == 'normal') {
  //     return true;
  //   }

  //   list($tiles, $canPlace) = $this->getPlayableTiles($player, true);
  //   return $canPlace;
  // }

  public function argsWriteX()
  {
    $player = $this->getPlayer();
    return [
      'slots' => $player->scoresheet()->getAvailableSlotsForNumber(NUMBER_X, JOKER)
    ];
  }

  public function actWriteX(string $slotId)
  {
    $number = NUMBER_X;
    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slotId, $number);
    $scribbles = [$scribble];

    // Scribble the bonus slot
    $source = $this->getCtxArg('source');
    $scribbles[] = $player->scoresheet()->addScribble($source['slot']);
    Notifications::writeNumber($player, $number, $scribbles, $source['name']);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble);
    if (!empty($reactions)) {
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'childs' => $reactions
      ]);
    }
  }
}
