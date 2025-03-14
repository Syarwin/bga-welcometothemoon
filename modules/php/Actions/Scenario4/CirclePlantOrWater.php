<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario4;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CirclePlantOrWater extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S4_CIRCLE_PLANT_OR_WATER;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function getSlots(Player $player): array
  {
    return $this->getCtxArg('slots') ?? [];
  }

  public function getArgs(): array
  {
    return [...parent::getArgs(), 'descSuffix' => $this->getCtxArg('type')];
  }

  public function getDescription(): string
  {
    return clienttranslate("Circle a resource above or below the current slot");
  }

  public function stCirclePlantOrWater()
  {
    $pref = Players::getCurrent()->getPref(OPTION_CIRCLE_LINKED_SLOT);
    $slots = $this->getCtxArg('slots');
    $singleChoice = count($slots) === 1;
    return $singleChoice && $pref === OPTION_CIRCLE_AUTOMATIC ? ['slot' => $slots[0]] : null;
  }

  public function actCirclePlantOrWater(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    $type = $this->getCtxArg('descSuffix') === WATER ? CIRCLE_TYPE_WATER_S4 : CIRCLE_TYPE_PLANT_S4;
    Notifications::circleSingleLinked($player, $scribble, $type);
  }
}
