<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleSingleLinked extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_CIRCLE_SINGLE_LINKED;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    return !is_null($this->getArgs()['slot']);
  }

  public function getDescription(): string
  {
    return clienttranslate("Circle a connected slot");
  }

  public function stCircleSingleLinked()
  {
    $pref = Players::getCurrent()->getPref(OPTION_CIRCLE_LINKED_SLOT);
    return $pref === OPTION_CIRCLE_AUTOMATIC ? [] : null;
  }

  public function argsCircleSingleLinked()
  {
    return $this->getCtxArgs();
  }

  public function actCircleSingleLinked()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $args = $this->getArgs();
    $slot = $args['slot'];
    $values = $args['values'] ?? null;
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    Notifications::circleSingleLinked($player, $scribble, $values[$slot] ?? null, $args['type']);
  }
}
