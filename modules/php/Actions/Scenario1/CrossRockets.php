<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CrossRockets extends \Bga\Games\WelcomeToTheMoon\Models\Action
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
    // Conditional bonus ?
    $slotToCheck = $this->getCtxArg('check');
    if (!is_null($slotToCheck)) {
      if (!$player->scoresheet()->hasScribbledSlot($slotToCheck)) {
        return false;
      }
    }

    return true;
  }


  public function getDescription(): string|array
  {
    $doable = $this->isDoable($this->getPlayer());
    return [
      'log' => $doable ? clienttranslate('Cross ${n} rockets') : clienttranslate('Cross ${n} rockets (not activated)'),
      'args' => [
        'n' => $this->getCtxArg('n'),
      ]
    ];
  }

  public function stCrossRockets()
  {
    return [];
  }


  protected array $rows = [
    139 => [100, 101, 102, 103, 104],
    140 => [105, 106, 107, 108],
    141 => [109, 110, 111],
    142 => [112, 113, 114],
    143 => [115, 116, 117],
    144 => [118, 119, 120],
    145 => [121, 122, 123],
    146 => [124, 125, 126],
    147 => [127, 128],
    148 => [129, 130],

    200 => SYSTEM_ERROR,
    201 => [131, 132, 133, 134, 135, 136, 137, 138]
  ];


  public function actCrossRockets()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $n = $this->getCtxArg("n");

    $scribbles = [];
    $m = 0;
    foreach ($this->rows as $scoreslot => $slots) {
      // System errors row
      if ($slots == SYSTEM_ERROR) {
        die("TODO: cross rocket system errors");
      }
      // Other rows
      else {
        foreach ($slots as $i => $slot) {
          if ($scoresheet->hasScribbledSlot($slot)) continue;

          // Empty rocket found => scribble it
          $scribbles[] = $scoresheet->addScribble($slot);
          $m++;
          // End of row slot
          if ($i == count($slots) - 1) {
            $scribbles[] = $scoresheet->addScribble($scoreslot);
          }

          if ($m == $n) {
            break;
          }
        }
      }

      if ($m == $n) {
        break;
      }
    }


    // Scribble the bonus slot
    $source = $this->getCtxArg('source');
    $scribbles[] = $player->scoresheet()->addScribble($source['slot']);

    Notifications::crossRockets($player, $n, $scribbles, $source['name']);
  }
}
