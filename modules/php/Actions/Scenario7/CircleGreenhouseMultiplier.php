<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario7;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class CircleGreenhouseMultiplier extends Action
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
    return true;
  }

  public function getDescription(): string
  {
    return clienttranslate('Circle a x2 bonus');
  }

  public function stCircleGreenhouseMultiplier()
  {
    return [];
  }

  protected function getMsg(): array
  {
    return [
      159 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°1 (the topmost one)'),
      158 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°2 (the second from the top)'),
      157 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°3 (the third from the top)'),
      156 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°4 (the third from the bottom)'),
      155 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°5 (the second from the bottom)'),
      154 => clienttranslate('${player_name} have circled the x2 bonus of the greenhouse in the starship n°6 (the lowest one)'),
    ];
  }

  public function actCircleGreenhouseMultiplier()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $x2Slot = $this->getCtxArg('slot');
    if (!$scoresheet->hasScribbledSlot($x2Slot)) {
      $scoresheet->prepareForPhaseFive(['slot' => $x2Slot]);

      $scribble = $scoresheet->addScribble($x2Slot, SCRIBBLE_CIRCLE);
      $msg = $this->getMsg()[$x2Slot];
      Notifications::circleGreenhouseMultiplier($player, $scribble, $msg);

      // ASTRA BONUS
      if (Globals::isSolo()) {
        $bonusScribble = Players::getAstra()->circleNextBonus();
        Notifications::gainOneSoloBonus($player, $bonusScribble);
      }
    }
  }
}
