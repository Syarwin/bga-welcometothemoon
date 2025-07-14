<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario5;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class FilledSkyscraper extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function getDescription(): string|array
  {
    return clienttranslate("Get filled skyscraper points");
  }

  public function stFilledSkyscraper()
  {
    return [];
  }


  protected array $bonuses = [
    1 => [92, 93],
    9 => [90, 91],
    10 => [96, 97],
    19 => [94, 95],
    20 => [100, 101],
    29 => [98, 99],
    30 => [104, 105],
    38 => [102, 103],
  ];

  protected function getMsgs(): array
  {
    return [
      1 => [
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°1 first and gets 20 points'),
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°1 and gets 10 points'),
      ],
      9 => [
        clienttranslate('${player_name} have reached the highest level of skyscraper n°1 first and gets 6 points'),
        clienttranslate('${player_name} have reached the highest level of skyscraper n°1 and gets 3 points'),
      ],
      10 => [
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°2 first and gets 10 points'),
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°2 and gets 5 points'),
      ],
      19 => [
        clienttranslate('${player_name} have reached the highest level of skyscraper n°2 first and gets 20 points'),
        clienttranslate('${player_name} have reached the highest level of skyscraper n°2 and gets 10 points'),
      ],
      20 => [
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°3 first and gets 6 points'),
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°3 and gets 3 points'),
      ],
      29 => [
        clienttranslate('${player_name} have reached the highest level of skyscraper n°3 first and gets 25 points'),
        clienttranslate('${player_name} have reached the highest level of skyscraper n°3 and gets 12 points'),
      ],
      30 => [
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°4 first and gets 15 points'),
        clienttranslate('${player_name} have reached the lowest level of skyscraper n°4 and gets 7 points'),
      ],
      38 => [
        clienttranslate('${player_name} have reached the highest level of skyscraper n°4 first and gets 10 points'),
        clienttranslate('${player_name} have reached the highest level of skyscraper n°4 and gets 5 points'),
      ]
    ];
  }

  public function actFilledSkyscraper()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $numberSlot = $this->getCtxArg('slot');
    $slots = $this->bonuses[$numberSlot];
    $msgs = $this->getMsgs()[$numberSlot];
    foreach ($slots as $i => $slot) {
      if ($scoresheet->hasScribbledSlot($slot)) continue;

      $firstToFill = $i == 0;

      // Register for phase 5 if first
      if ($firstToFill) {
        $scoresheet->prepareForPhaseFive(['slot' => $slot]);
      }

      $scribble = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);
      Notifications::filledSkyscraper($player, $scribble, $msgs[$i]);

      // ASTRA BONUS
      if (Globals::isSolo() && $firstToFill) {
        $astra = Players::getAstra();
        $bonusScribble = $astra->circleNextBonus();
        Notifications::gainOneSoloBonus($player, $bonusScribble);
      }
      break;
    }
  }
}
