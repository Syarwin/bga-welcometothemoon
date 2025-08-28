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
      CIRCLE_SYMBOL_REACTOR => clienttranslate('Circle a reactor'),
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
    $args = $this->getCtxArgs();
    $scribbleType = $args['scribbleType'] ?? null;
    $amount = $args['amount'] ?? 1;
    $scribbles = [];
    for ($i = 0; $i < $amount; $i++) {
      foreach ($this->getSlots($scoresheet) as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot)) {
          $scribble = $scoresheet->addScribble($slot, $scribbleType ?? SCRIBBLE_CIRCLE);
          $scribbles[] = $scribble;
          $jokerSlotId = $args['jokers'][$slot] ?? null;
          if (!is_null($jokerSlotId)) {
            $scribbles[] = $scoresheet->addScribble($jokerSlotId, SCRIBBLE_CIRCLE);
            Notifications::pmidMessage($player, clienttranslate('${player_name} circles a Wild Action symbol'));
          }
          $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actCircleNextInRow');
          $this->insertAsChild($reactions);
          break;
        }
      }
    }
    Notifications::addScribbles($player, $scribbles);
    $this->sendTextNotification($player, $scribbles);
  }

  // TODO: Revert the commit this was added after all tables will be started after 6/03/2025
  public function actCircleOther()
  {
    $this->actCircleNextInRow();
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
        CIRCLE_SYMBOL_REACTOR => clienttranslate('${player_name} circles a reactor symbol'),

        CROSS_SYMBOL_WATER => clienttranslate('${player_name} crosses off a water symbol'),
        CROSS_SYMBOL_PLANT_ON_PLANET_AND_SHEET => clienttranslate('${player_name} crosses off a plant symbol on the same planet and a plant symbol on his scoring sheet'),
        CROSS_SYMBOL_WATER_ON_PLANET_AND_SHEET => clienttranslate('${player_name} crosses off a water symbol on the same planet and a water symbol on his scoring sheet'),
        CROSS_SYMBOL_PLANT_ON_SHEET => clienttranslate('${player_name} crosses off a plant symbol on his scoring sheet'),
        CROSS_SYMBOL_WATER_ON_SHEET => clienttranslate('${player_name} crosses off a water symbol on his scoring sheet'),
        CIRCLE_INSIGNIA_ON_ASTEROID => clienttranslate('${player_name} draws his insignia on the first available asteroid'),
      ][$symbol] ?? '';

      // Scenario 8 => astronaut symbol are circled first, not crossed
      // TODO: Rename existing CIRCLE_SYMBOL_ASTRONAUT to CROSS_SYMBOL_ASTRONAUT and use new CIRCLE_SYMBOL_ASTRONAUT const in S8 only
      if ($symbol == CIRCLE_SYMBOL_ASTRONAUT && $this->getCtxArg('scribbleType') == SCRIBBLE_CIRCLE) {
        $msg = clienttranslate('${player_name} circles an Astronaut symbol');
      }
    } else {
      // If at any time we start to scribble more than 1 symbol for astronauts and/or planning, add them here
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
