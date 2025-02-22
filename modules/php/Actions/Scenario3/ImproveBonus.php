<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario3;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class ImproveBonus extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_IMPROVE_BONUS;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    return array_map(function ($dataBlock) use ($scoresheet) {
      return $scoresheet->getFirstUnscribbled(array_keys($dataBlock['slots']));
    }, self::getData());
  }

  public function actImproveBonus(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scribble = $player->scoresheet()->addScribble($slot);
    $dataBlock = current(array_filter(self::getData(), function ($dataBlock) use ($slot) {
      return in_array($slot, array_keys($dataBlock['slots']));
    }));
    Notifications::improveBonus($player, $scribble, $dataBlock['name'], $dataBlock['slots'][$slot]);
  }

  private static function getData(): array
  {
    return [
      [
        'name' => clienttranslate('greenhouses'),
        'slots' => [101 => 0, 102 => 1, 103 => 2, 104 => 3],
      ],
      [
        'name' => clienttranslate('water'),
        'slots' => [105 => 0, 106 => 2, 107 => 4, 108 => 6],
      ],
      [
        'name' => clienttranslate('antennas'),
        'slots' => [109 => 0, 110 => 1, 111 => 2, 112 => 4],
      ]
    ];
  }
}
