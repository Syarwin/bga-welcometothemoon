<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PropagateVirus extends Action
{
  public function getState(): int
  {
    return ST_S6_PROPAGATE_VIRUS;
  }

  public function getVirus(): int
  {
    $quarter = Scoresheet6::getQuarters()[$this->getCtxArg('quarter')];
    return Scoresheet6::getVirusOfQuarter($quarter[5]);
  }

  public function getVirusName(): string
  {
    $names = [
      VIRUS_BLUE => clienttranslate('the blue virus'),
      VIRUS_RED => clienttranslate('the red virus'),
      VIRUS_GREEN => clienttranslate('the green virus'),
      VIRUS_YELLOW => clienttranslate('the yellow virus'),
      VIRUS_PURPLE => clienttranslate('the purple virus'),
      VIRUS_GREY => clienttranslate('the grey virus'),
    ];

    return $names[$this->getVirus()];
  }

  public function argsPropagateVirus()
  {
    // TODO : distinguish the case where quarter is full and not full
    return [
      'virus_name' => $this->getVirusName(),
      'i18n' => ['virus_name']
    ];
  }

  public function actPropagateVirus(array $slots)
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
  }
}
