<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario2;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;

class PlaceEnergyWall extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_PLACE_ENERGY_WALL;
  }


  // // Each entry is BOTTOM SLOT => [FIRST TOP SLOT, SECOND TOP SLOT] of an individual column
  // protected array $slots = [
  //   135 => [133, 134],
  //   138 => [136, 137],
  //   141 => [139, 140],
  //   144 => [142, 143],
  //   147 => [145, 146],
  //   150 => [148, 149],
  // ];
  // // Find the next empty slot and whether it implies to build a wall or not 
  // public function getNextSlot(Player $player): ?array
  // {
  //   $scoresheet = $player->scoresheet();
  //   $slots = [];
  //   foreach ($this->slots as $bonusSlot => $slots) {
  //     foreach ($slots as $i => $slotId) {
  //       if (!$scoresheet->hasScribbledSlot($slotId) && $slotId != 133) { // SLOT 133 is pre-scribbled
  //         return [$slotId, $i > 0];
  //       }
  //     }
  //   }

  //   return null;
  // }

  public function argsPlaceEnergyWall()
  {
    $player = $this->getPlayer();

    return [
      'slots' => $player->scoresheet()->getSectionFreeSlots('walls'),
    ];
  }

  public function actPlaceEnergyWall(int $slot)
  {
    $args = $this->getArgs();
    if (!in_array($slot, $args['slots'])) {
      throw new \BgaUserException('You cannot place an energy wall here. Should not happen.');
    }

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
