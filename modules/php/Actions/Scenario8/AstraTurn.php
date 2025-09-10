<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario8;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;

class AstraTurn extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_S8_ASTRA_TURN;
  }

  /*
  * Given a number/action combination (as assoc array), compute the set of writtable numbers on the sheet
  */
  public static function getAvailableNumbersOfCombination($player, $combination)
  {
    $numbers = [$combination['number']];
    $result = [];
    foreach ($numbers as $number) {
      $slots = $player->scoresheet()->getAvailableSlotsForNumber($number, $combination['action']);
      if (!empty($slots)) {
        $result[$number] = $slots;
      }
    }
    return $result;
  }


  public function argsAstraTurn()
  {
    $player = Players::getCurrentSolo();
    $card = ConstructionCards::getInLocation('stack-0')->first();
    $combination = ['number' => $card->getNumber(), 'action' => '']; // Give a fake action to make sure it's not using astronaut
    $numbers = self::getAvailableNumbersOfCombination($player, $combination);

    // If this/these planets are full, go to next planet
    $safeCheck = 0;
    while (empty($numbers) && $safeCheck++ < 15) {
      $combination['number']++;
      if ($combination['number'] > 15) $combination['number'] = 1;
      $numbers = self::getAvailableNumbersOfCombination($player, $combination);
    }
    if ($safeCheck >= 15) {
      throw new \BgaUserException('Astra cannot write anywhere. Should not happen.');
    }

    return [
      'numbers' => $numbers,
    ];
  }

  public function actAstraTurn(string $slot, int $number)
  {
    $args = $this->getArgs();
    $slots = $args['numbers'][$number] ?? [];
    if (!in_array($slot, $slots)) {
      throw new \BgaUserException('You cannot write this number here. Should not happen.');
    }

    $player = Players::getCurrentSolo();
    $soloPlayer = Players::getSolo();
    $scoresheet = $player->scoresheet();
    $scribble = $scoresheet->addScribble($slot, SCRIBBLE_INSIGNA_TRIANGLE, false);
    Notifications::writeNumberS8AstraTurn($soloPlayer, $number, [$scribble]);

    // Reaction to the scribble itself (filled up quarter bonuses, etc)
    $planetId = $scoresheet->getPlanetIdBySlot($slot);
    $scoresheet->resolvePlanetWinnerIfNeeded($soloPlayer, $planetId);

    // Action corresponding to the combination
    $card = ConstructionCards::getInLocation('stack-0')->first();
    $action = $card->getAction();
    if ($action == ASTRONAUT) $action = PLANNING;
    if ($action == ENERGY) $action = ROBOT;

    // PLANNING
    $planet = $scoresheet->getPlanetOfSlot($slot);
    if ($action == PLANNING) {
      $slotId = $scoresheet->getFirstUnscribbled($planet['moonSlots']);
      if (!is_null($slotId)) {
        $scribble = $scoresheet->addScribble($slotId, SCRIBBLE_INSIGNA_TRIANGLE, false);
        Notifications::drawOnMoon($soloPlayer, [$scribble], $planet['type'], true);
      }
    }

    // ROBOT
    if ($action == ROBOT) {
      $slotIds = $scoresheet->getSectionSlots('asteroids');
      if (Globals::getTurn() % 2 == 0) $slotIds = array_reverse($slotIds);
      $slotId = $scoresheet->getFirstUnscribbled($slotIds);
      if (!is_null($slotId)) {
        $scribble = $scoresheet->addScribble($slotId, SCRIBBLE_INSIGNA_TRIANGLE, false);
        Notifications::addScribbles($soloPlayer, [$scribble], clienttranslate('Astra draws insignia on the first available asteroid'));
      }
    }

    // WATER/PLANT
    if ($action == WATER || $action == PLANT) {
      $slotIds = $action == WATER ? $scoresheet->planetsWaters[$planetId] : $scoresheet->planetsPlants[$planetId];
      $slotId = $scoresheet->getFirstUnscribbled($slotIds);
      if (!is_null($slotId)) {
        $scribble = $scoresheet->addScribble($slotId, SCRIBBLE, false);
        Notifications::addScribbles($soloPlayer, [$scribble], clienttranslate('Astra scribbles off a corresponding symbol on the planet'));
      }
    }
  }
}
