<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleSymbol extends Action
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
    return !is_null($this->getLinkedSlot($player));
  }

  private array $waterTanksAtSlots = [
    1 => 136,
    7 => 137,
    13 => 138,
    20 => 139,
    23 => 140,
    34 => 141,
    37 => 142,
    43 => 143,
    47 => 144,
    53 => 145,
  ];

  private array $plantsByQuarter = [
    [120],
    [121],
    [122, 123],
    [124],
    [125, 126],
    [127, 128],
    [129, 130],
    [131],
    [132, 133],
    [134],
    [135]
  ];

  // End lines : scoremarker slot / bonus slot / type of bonus
  private array $linkedBonus = [
    169 => [194, 205, ROBOT],
    171 => [195, 206, ROBOT],
    174 => [196, 207, ROBOT],
    177 => [197, 208, ROBOT],
    180 => [198, 209, ROBOT],
    183 => [199, 210, ROBOT],

    185 => [200, 211, ENERGY],
    187 => [201, 212, ENERGY],
    189 => [202, 213, ENERGY],
    191 => [203, 214, ENERGY],
    193 => [204, 215, ENERGY],
    195 => [205, 216, ENERGY]
  ];


  private function getLinkedSlot(Player $player): ?int
  {
    $slot = $this->getCtxArg('slot');
    $quarter = intdiv($slot - 1, 5);
    $scoresheet = $player->scoresheet();

    // Water
    if ($this->getCtxArg('type') == WATER) {
      return $this->waterTanksAtSlots[$slot] ?? null;
    }
    // Plant
    else {
      foreach ($this->plantsByQuarter[$quarter] as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
          return $slot;
        }
      }
    }
    return null;
  }

  public function getDescription(): string
  {
    $symbol = $this->getCtxArg('type');
    return [
      WATER => clienttranslate('Circle a water symbol'),
      PLANT => clienttranslate('Circle a plant symbol'),
    ][$symbol];
  }

  public function stCircleSymbol()
  {
    return [];
  }

  public function actCircleSymbol()
  {
    $player = $this->getPlayer();
    $type = $this->getCtxArg('type');
    $scoresheet = $player->scoresheet();
    $scribbles = [];

    // Scribble in the quarter
    $slot = $this->getLinkedSlot($player);
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
    $scribbles[] = $scribble;

    // Scribble on the bottom of the scoresheet
    $scoreSlot = $scoresheet->getFirstUnscribbled($scoresheet->getSectionSlots($type == WATER ? 'watermarkers' : 'plantmarkers'));
    $scribble = $scoresheet->addScribble($scoreSlot, SCRIBBLE);
    $scribbles[] = $scribble;

    // Any linked end-of-line bonus?
    $bonus = $this->linkedBonus[$scoreSlot] ?? null;
    if (!is_null($bonus)) {
      [$markerSlot, $bonusSlot, $bonusType] = $bonus;
      // Mark the score
      $scribble = $scoresheet->addScribble($markerSlot, SCRIBBLE);
      $scribbles[] = $scribble;
      // Add the action
      die("TODO");
    }

    // TODO : linked virus/weird propagation

    Notifications::addScribbles($player, $scribbles, "TODO");
  }
}
