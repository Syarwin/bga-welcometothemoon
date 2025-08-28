<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;

class DrawOnMoon extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S8_DRAW_ON_MOON;
  }

  protected array $slots = [79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92];

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
    $msgs = [
      PLANET_TYPE_GREEN => clienttranslate('green planet'),
      PLANET_TYPE_BLUE => clienttranslate('blue planet'),
      PLANET_TYPE_GREY => clienttranslate('grey planet'),
    ];

    Notifications::drawOnMoon($player, [$scribble], $msgs[$planet['type']]);

    $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actDrawOnMoon');
    $this->insertAsChild($reactions);
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
