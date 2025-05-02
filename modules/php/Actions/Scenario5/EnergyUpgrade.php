<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario5;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class EnergyUpgrade extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S5_ENERGY_UPGRADE;
  }

  protected array $sections = [
    'plan' => [
      170 => [138, 139],
      171 => [140, 141],
      172 => [142, 143],
      173 => [144, 145],
    ],
    WATER => [
      178 => [146, 147, 148],
      179 => [149, 150, 151],
      180 => [152, 153, 154],
      181 => [155, 156, 157],
    ],
    ASTRONAUT => [
      174 => [158, 159],
      175 => [160, 161],
      176 => [162, 163],
      177 => [164, 165],
    ],
    'dome' => [
      166 => [166],
      167 => [167],
      168 => [168],
      169 => [169],
    ],
  ];

  public function getFreeSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $freeSlots = [];
    foreach ($this->sections as $section) {
      foreach ($section as $selectorSlot => $slots) {
        if (!$scoresheet->hasScribbledSlot($slots[0])) {
          $freeSlots[] = $selectorSlot;
          break;
        }
      }
    }

    return $freeSlots;
  }

  public function actEnergyUpgrade(int $slot)
  {
    $this->sanityCheck($slot);

    // Find the corresponding section and slots
    $slots = [];
    foreach ($this->sections as $sectionType => $section) {
      if (array_key_exists($slot, $section)) {
        $slots = $section[$slot];
        break;
      }
    }

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles = [];
    foreach ($slots as $slot) {
      $scribbles[] = $scoresheet->addScribble($slot);
    }

    Notifications::s5EnergyUpgrade($player, $sectionType, $scribbles);
  }
}
