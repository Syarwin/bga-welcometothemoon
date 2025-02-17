<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;

class ActivateSabotage extends \Bga\Games\WelcomeToTheMoon\Models\Action
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
    $slotToCheck = $this->getCtxArg('source')['slot'];
    return !$player->scoresheet()->hasScribbledSlot($slotToCheck, SCRIBBLE);
  }

  public function isIndependent(?Player $player = null): bool
  {
    return true;
  }

  public function getDescription(): string|array
  {
    return $this->isDoable($this->getPlayer()) ? clienttranslate('Trigger sabotage') : "";
  }

  public function stActivateSabotage()
  {
    return [];
  }

  public function actActivateSabotage()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    // Scribble the bonus slot with a CIRCLE
    $source = $this->getCtxArg('source');
    $scribble = $scoresheet->addScribble($source['slot'], SCRIBBLE_CIRCLE);
    // Register for phase 5
    $sabotages = Globals::getTriggeredSabotages();
    $sabotages[] = $source['slot'];
    Globals::setTriggeredSabotages($sabotages);

    Notifications::activateSabotage($player, $scribble, $source['name']);

    // ASTRA BONUS
    if (Globals::isSolo()) {
      $astra = Players::getAstra();
      $bonusScribble = $astra->circleNextBonus();
      Notifications::resolveSabotageAstra($player, $bonusScribble);
    }
  }
}
