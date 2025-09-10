<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

class AstraDrawOnMoon extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S8_ASTRA_DRAW_ON_MOON;
  }

  public function getArgs(): array
  {
    $args = parent::getArgs();
    $args['n'] = $this->getCtxArg('n');
    return $args;
  }

  protected function getSlots(Player $player): array
  {
    $scoresheet = $player->scoresheet();
    $planets = $scoresheet->getS8Planets();
    $slots = [];
    foreach ($planets as $planetId => $planetData) {
      if ($scoresheet->hasScribbledSlot($planetData['final'])) {
        continue;
      }

      $status = $scoresheet->getCurrentPlanetStatus($planetId);
      $n = $scoresheet->getPId() == 0 ? $status[1] : $status[0];

      foreach ($planetData['moonSlots'] as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot)) {
          $slots[$slot] = $n;
        }
      }
    }

    if (empty($slots)) return [];

    $m = max($slots);
    $maxSlots = [];
    foreach ($slots as $slot => $n) {
      if ($n == $m) {
        $maxSlots[] = $slot;
      }
    }

    return $maxSlots;
  }

  /**
   * @throws \BgaUserException
   * @throws \BgaVisibleSystemException
   */
  public function actAstraDrawOnMoon(int $slot): void
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_INSIGNA_TRIANGLE, false);
    $planet = $this->getPlanetByMoonSlot($slot, $scoresheet);

    Notifications::drawOnMoon($player, [$scribble], $planet['type'], true);
  }

  private function getPlanetByMoonSlot(int $slot, Scoresheet8 $scoresheet)
  {
    $planets = $scoresheet->getS8Planets();
    foreach ($planets as $planet) {
      if (in_array($slot, $planet['moonSlots'])) {
        return $planet;
      }
    }
    throw new \BgaVisibleSystemException('getPlanetByMoonSlot: cannot find a planet by moon id ' . $slot);
  }
}
