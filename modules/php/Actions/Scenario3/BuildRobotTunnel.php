<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario3;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

const DIRECTIONS = [
  [-1, 0], // N
  [0, 1], // E
  [1, 0], // S
  [0, -1], // W
];

class BuildRobotTunnel extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_BUILD_ROBOT_TUNNEL;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();

    // BFS
    $visited = [];
    $slots = [];
    $stack = [[10, 0]];
    while (!empty($stack)) {
      $pos = array_pop($stack);

      // Skip the node if marked, otherwise mark it
      if ($visited[$pos[0]][$pos[1]] ?? false) continue;
      $visited[$pos[0]][$pos[1]] = true;

      // For each direction
      foreach (DIRECTIONS as $dir) {
        $newRow = $pos[0] + $dir[0];
        $newCol = $pos[1] + $dir[1];
        if ($newCol < 0 || $newCol > 10 || $newRow < 0 || $newRow > 10) continue;

        // If the tunnel is build, add the node in the stack
        $tunnelSlot = static::$grid[$newRow][$newCol];
        if ($tunnelSlot == -1 || $scoresheet->hasScribbledSlot($tunnelSlot)) {
          $stack[] = [$newRow + $dir[0], $newCol + $dir[1]];
        }
        // Otherwise, we can build this tunnel
        else {
          $slots[] = $tunnelSlot;
        }
      }
    }

    return $slots;
  }

  public function actBuildRobotTunnel(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles = [];
    // Scribble the tunnel
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_LINE);
    // Explore the graph to find new antenna
    $antennasScribbles = static::scribblesConnectedAntennas($scoresheet);
    $circledAntennas = count($antennasScribbles);
    $scribbles = array_merge($scribbles, $antennasScribbles);
    Notifications::buildRobotTunnel($player, $scribbles, $circledAntennas);
  }

  protected static $grid = [
    [5, 170, 11, 176, 16, 182, 21, 188, 27, 194, TOP_RIGHT_CORNER_SLOT],
    [140, 0, 145, 0, 150, 0, 155, 0, 160, 0, 165],
    [4, 169, 10, 175, 15, 181, 20, 187, 26, 193, 32],
    [139, 0, 144, 0, 149, 0, 154, 0, 159, 0, 164],
    [3, 168, 9, 174, -1, 180, 19, 186, 25, 192, 31],
    [138, 0, 143, 0, 148, 0, 153, 0, 158, 0, 163],
    [2, 167, 8, 173, 14, 179, -1, 185, 24, 191, 30],
    [137, 0, 142, 0, 147, 0, 152, 0, 157, 0, 162],
    [1, 166, 7, 172, 13, 178, 18, 184, 23, 190, 29],
    [-1, 0, 141, 0, 146, 0, 151, 0, 156, 0, 161],
    [-1, -1, 6, 171, 12, 177, 17, 183, 22, 189, 28],
  ];

  protected static $slotToAntennasMap = [
    1 => [129],
    3 => [125],
    6 => [132],
    8 => [127],
    11 => [121],
    15 => [123],
    18 => [130],
    19 => [126],
    21 => [122],
    22 => [133],
    24 => [128],
    26 => [124],
    29 => [131],
    TOP_RIGHT_CORNER_SLOT => [134, 135, 136]
  ];

  public static function scribblesConnectedAntennas(Scoresheet $scoresheet)
  {
    $scribbles = [];

    // BFS
    $visited = [];
    $slots = [];
    $stack = [[10, 0]];
    while (!empty($stack)) {
      $pos = array_pop($stack);

      // Skip the node if marked, otherwise mark it
      if ($visited[$pos[0]][$pos[1]] ?? false) continue;
      $visited[$pos[0]][$pos[1]] = true;

      // Any Antenna to scribble here ? (only if the slot is scribbled!)
      $slot = static::$grid[$pos[0]][$pos[1]];
      if (in_array($slot, [-1, TOP_RIGHT_CORNER_SLOT]) || $scoresheet->hasScribbledSlot($slot)) {
        $antennasSlots = static::$slotToAntennasMap[$slot] ?? [];
        foreach ($antennasSlots as $antennasSlot) {
          if (!$scoresheet->hasScribbledSlot($antennasSlot)) {
            $scribbles[] = $scoresheet->addScribble($antennasSlot, SCRIBBLE_CIRCLE);
          }
        }
      }

      // For each direction
      foreach (DIRECTIONS as $dir) {
        $newRow = $pos[0] + $dir[0];
        $newCol = $pos[1] + $dir[1];
        if ($newCol < 0 || $newCol > 10 || $newRow < 0 || $newRow > 10) continue;

        // If the tunnel is build, add the node in the stack
        $tunnelSlot = static::$grid[$newRow][$newCol];
        if ($tunnelSlot == -1 || $scoresheet->hasScribbledSlot($tunnelSlot)) {
          $stack[] = [$newRow + $dir[0], $newCol + $dir[1]];
        }
      }
    }

    return $scribbles;
  }
}
