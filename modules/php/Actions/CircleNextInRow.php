<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

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
    foreach ($this->getCtxArg('slots') as $slot) {
      if (!$scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
        return true;
      }
    }
    return false;
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
    return $this->isAstronautAction() ? clienttranslate('Cross off an Astronaut symbol') : clienttranslate('Cross off a Planning symbol');
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
    foreach ($args['slots'] as $slot) {
      if (!$scoresheet->hasScribbledSlot($slot)) {
        $scribble = $scoresheet->addScribble($slot);
        $this->isAstronautAction() ?
          Notifications::scribbleAstronaut($player, $scribble) :
          Notifications::scribblePlanning($player, $scribble);
        $jokers = $args['jokers'] ?? [];
        if (in_array($slot, array_keys($jokers))) {
          $scribble = $scoresheet->addScribble($jokers[$slot], SCRIBBLE_CIRCLE);
          Notifications::circleJoker($player, $scribble);
        }
        break;
      }
    }
  }
}
