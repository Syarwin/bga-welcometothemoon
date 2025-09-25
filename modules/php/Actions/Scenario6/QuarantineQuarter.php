<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class QuarantineQuarter extends Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function stQuarantineQuarter()
  {
    return [];
  }

  public function actQuarantineQuarter()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $quarterId = $this->getCtxArg('quarter');
    $quarter = Scoresheet6::getQuarters()[$quarterId];
    $scribble = $scoresheet->addScribble($quarter['virus'], SCRIBBLE);
    Notifications::quarantineQuarter($player, $scribble, $quarterId);
  }
}
