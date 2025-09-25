<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class CloseWalkway extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S6_CLOSE_WALKWAY;
  }

  public function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    return $scoresheet->getSectionSlots('walkways');
  }

  public function getDescription(): string
  {
    return clienttranslate("Close a walkway");
  }

  public function actCloseWalkway(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribbles = [];
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_LINE);
    $scribbles[] = $scribble;
    $reactions = $scoresheet->getScribbleReactions($scribble, 'actCloseWalkway');
    $this->insertAsChild($reactions);

    // Bonus slot?
    $bonusSlot = $this->getCtxArg('bonusSlot');
    if (!is_null($bonusSlot)) {
      $scribbles[] = $scoresheet->addScribble($bonusSlot);
    }

    Notifications::closeWalkway($player, $scribbles);
  }
}
