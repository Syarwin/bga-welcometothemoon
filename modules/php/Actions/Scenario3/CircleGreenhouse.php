<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario3;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Quarter;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet3;

class CircleGreenhouse extends \Bga\Games\WelcomeToTheMoon\Models\Action
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
    return $this->getNextUnscribbledSlot($player) !== null;
  }

  public function getDescription(): string
  {
    return clienttranslate('Circle a Greenhouse');
  }

  protected function getNextUnscribbledSlot(Player $player, Quarter|null $quarter = null): int|null
  {
    if (is_null($quarter)) {
      $quarter = Scoresheet3::getQuarters()[$this->getCtxArgs()['quarterId']];
    }
    return $player->scoresheet()->getFirstUnscribbled(array_merge(...$quarter->getPlantsSlots()), SCRIBBLE_CIRCLE);
  }

  public function stCircleGreenhouse()
  {
    return [];
  }

  public function actCircleGreenhouse(): void
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    /** @var Quarter $quarter */
    $quarter = Scoresheet3::getQuarters()[$this->getCtxArgs()['quarterId']];
    $nextUnscribbledSlot = $this->getNextUnscribbledSlot($player, $quarter);
    // There might be 1 or 2 plants in a Greenhouse
    $greenhousePlantSlots = current(array_filter($quarter->getPlantsSlots(), function ($greenhouseBlock) use ($nextUnscribbledSlot) {
      return in_array($nextUnscribbledSlot, $greenhouseBlock);
    }));
    $scribbles = [];
    foreach ($greenhousePlantSlots as $slot) {
      $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    }
    Notifications::circleGreenhouse($player, $scribbles, $quarter->getName());
  }
}
