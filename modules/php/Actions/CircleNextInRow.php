<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

class CircleNextInRow extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    foreach ($this->getSlots($scoresheet) as $slot) {
      if (!$scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
        return true;
      }
    }
    return false;
  }

  public function getDescription(): string
  {
    $symbol = $this->getCtxArg('symbol');
    return [
      CIRCLE_SYMBOL_ASTRONAUT => clienttranslate('Cross off an Astronaut symbol'),
      CIRCLE_SYMBOL_PLANNING => clienttranslate('Cross off a Planning symbol'),
      CIRCLE_SYMBOL_WATER => clienttranslate('Circle a water symbol'),
      CIRCLE_SYMBOL_PLANT => clienttranslate('Circle a plant symbol'),
      CIRCLE_SYMBOL_RUBY => clienttranslate('Circle a ruby'),
      CIRCLE_SYMBOL_PEARL => clienttranslate('Circle a pearl'),
    ][$symbol];
  }

  public function stCircleNextInRow()
  {
    return [];
  }

  public function actCircleNextInRow()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbleType = $this->getCtxArg('scribbleType') ?? null;
    $amount = $this->getCtxArg('amount') ?? 1;
    $scribbles = [];
    for ($i = 0; $i < $amount; $i++) {
      foreach ($this->getSlots($scoresheet) as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot)) {
          $scribbles[] = $scoresheet->addScribble($slot, $scribbleType ?? SCRIBBLE_CIRCLE);
          $slotId = $jokers[$slot] ?? null;
          if (!is_null($slotId)) {
            $scribble = $scoresheet->addScribble($slotId, SCRIBBLE_CIRCLE);
            Notifications::circleJoker($player, $scribble);
          }
          break;
        }
      }
    }
    Notifications::addScribbles($player, $scribbles);
    $this->sendTextNotification($player, $scribbles);
  }

  private function getSlots(Scoresheet $scoresheet): array
  {
    $slots = $this->getCtxArg('slots');
    if (is_null($slots)) {
      $section = $this->getCtxArg('section');
      $slots = $scoresheet->getSectionSlots($section);
    }
    return $slots;
  }

  private function sendTextNotification(Player $player, array $scribbles)
  {
    $symbol = $this->getCtxArg('symbol');
    if (count($scribbles) === 1) {
      $msg = [
        CIRCLE_SYMBOL_ASTRONAUT => clienttranslate('${player_name} crosses off an Astronaut symbol'),
        CIRCLE_SYMBOL_PLANNING => clienttranslate('${player_name} crosses off a Planning symbol'),
        CIRCLE_SYMBOL_WATER => clienttranslate('${player_name} circles a water symbol'),
        CIRCLE_SYMBOL_PLANT => clienttranslate('${player_name} circles a plant symbol'),
        CIRCLE_SYMBOL_RUBY => clienttranslate('${player_name} circles a ruby'),
        CIRCLE_SYMBOL_PEARL => clienttranslate('${player_name} circles a pearl'),
      ][$symbol];
    } else {
      // If at any time we'll start to scribble more than 1 symbol for astronauts and/or planning, add them here
      $msg = [
        CIRCLE_SYMBOL_WATER => clienttranslate('${player_name} circles ${amount} water symbols'),
        CIRCLE_SYMBOL_PLANT => clienttranslate('${player_name} circles ${amount} plant symbols'),
        CIRCLE_SYMBOL_RUBY => clienttranslate('${player_name} circles ${amount} rubies'),
        CIRCLE_SYMBOL_PEARL => clienttranslate('${player_name} circles ${amount} pearls'),
      ][$symbol] ?? null;
    }
    if ($msg) {
      Notifications::pmidMessage($player, $msg, ['amount' => count($scribbles)]);
    }
  }
}
