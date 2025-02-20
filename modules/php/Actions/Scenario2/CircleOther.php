<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleOther extends \Bga\Games\WelcomeToTheMoon\Models\Action
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
    foreach ($this->getSlots() as $slot) {
      if (!$scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
        return true;
      }
    }
    return false;
  }

  private function getSlots()
  {
    return $this->isAstronautAction() ? self::$astronauts : self::$planning;
  }

  private function isAstronautAction()
  {
    $action = $this->getCtxArg('actionType');
    if ($action !== ASTRONAUT && $action !== PLANNING) {
      throw new \InvalidArgumentException("Expected action to be either 'astronaut' or 'planning', got {$action}");
    }
    return $action === ASTRONAUT;
  }

  public function getDescription(): string
  {
    return $this->isAstronautAction() ? clienttranslate('Circling an astronaut symbol') : clienttranslate('Circling a planning symbol');
  }

  public function stCircleOther()
  {
    return [];
  }

  public function actCircleOther()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    foreach ($this->getSlots() as $slot) {
      if (!$scoresheet->hasScribbledSlot($slot)) {
        $scribble = $scoresheet->addScribble($slot);
        Notifications::scribbleAstronaut($player, $scribble);
        if (in_array($slot, array_keys(self::$jokers))) {
          $scribble = $scoresheet->addScribble(self::$jokers[$slot], SCRIBBLE_CIRCLE);
          Notifications::circleJoker($player, $scribble);
        }
        break;
      }
    }
  }

  private static array $astronauts = [151, 152, 154, 155, 157, 158];
  private static array $planning = [160, 161, 163, 164, 166, 167];
  private static array $jokers = [
    152 => 153,
    155 => 156,
    158 => 159,
    161 => 162,
    164 => 165,
    167 => 168,
  ];
}
