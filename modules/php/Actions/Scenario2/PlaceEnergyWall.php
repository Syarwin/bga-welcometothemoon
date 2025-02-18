<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class PlaceEnergyWall extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_PLACE_ENERGY_WALL;
  }

  protected function getSlots(Player $player): array
  {
    return $player->scoresheet()->getSectionSlots('walls');
  }

  public function actPlaceEnergyWall(int $slot)
  {
    $this->sanityCheck($slot);

    // Draw the line
    $scribbles = [];
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_LINE);

    // Scribble the energy slots
    $bonusSlot = $this->getCtxArg('slot');
    $scribbles[] = $scoresheet->addScribble($bonusSlot);
    // DO WE WANT TO SCRIBBLE THE TWO ENERGY CIRCLES ? UNCOMMENT IF YES
    // foreach (CircleEnergy::$slots[$bonusSlot] as $slot2) {
    //   $scribbles[] = $scoresheet->addScribble($slot2);
    // }

    Notifications::placeEnergyWall($player, $scribbles);
  }
}
