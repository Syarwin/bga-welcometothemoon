<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

class DrawOnMoon extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S8_DRAW_ON_MOON;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $planets = $this->getCtxArg('planets');
    $slots = [];
    foreach ($planets as $planetData) {
      if (!$scoresheet->hasScribbledSlot($planetData['final'])) {
        $slots = array_merge($slots, $planetData['moonSlots']);
      }
    }

    return $slots;
  }

  /**
   * @throws \BgaUserException
   * @throws \BgaVisibleSystemException
   */
  public function actDrawOnMoon(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, $this->getCtxArg('insignia'));
    $planet = $this->getPlanetByMoonSlot($slot);

    Notifications::drawOnMoon($player, [$scribble], $planet['type']);
    if (!$this->getCtxArg('isBonus')) {
      $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actDrawOnMoon');
      $this->insertAsChild($reactions);
    }
  }

  private function getPlanetByMoonSlot(int $slot)
  {
    foreach ($this->getCtxArg('planets') as $planet) {
      if (in_array($slot, $planet['moonSlots'])) {
        return $planet;
      }
    }
    throw new \BgaVisibleSystemException('getPlanetByMoonSlot: cannot find a planet by moon id ' . $slot);
  }
}
