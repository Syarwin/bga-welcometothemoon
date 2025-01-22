<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;

class Scoresheet
{
  protected ?Player $player;
  protected int $scenario;
  protected Collection $scribbles;
  protected array $scribblesBySlots;

  public function __construct(?Player $player)
  {
    $this->player = $player;
    $this->fetch();

    // Extract info froms datas
    foreach ($this->datas['sections'] as $section) {
      $this->slotsBySection[$section['id']] = [];
      foreach ($section['elts'] as $elt) {
        $this->slotsBySection[$section['id']][] = $elt['id'];
      }
    }
  }

  public function fetch()
  {
    // Fetch scribbles
    $this->scribbles = Scribbles::getOfPlayer($this->player);
    $this->scribblesBySlots = [];
    foreach ($this->scribbles as $scribble) {
      $this->scribblesBySlots[$scribble->getSlot()] = $scribble;
    }
  }

  public function addScribble($location, $type)
  {
    $scribble = Scribbles::add($this->player, [
      'type' => $type,
      'location' => "slot-$location",
    ]);
    $this->scribbles[$scribble->getId()] = $scribble;
    $this->scribblesBySlots[$scribble->getSlot()] = $scribble;
    return $scribble;
  }


  /**
   * getAvailableSlotsForNumber : where can I put a given number
   *  - considering filled-up slots
   *  - considering increasing sequence constraint
   */
  protected array $increasingConstraints = [];
  public function getAvailableSlotsForNumber(int $number, string $action)
  {
    $allSlots = $this->slotsBySection['numbers'];

    // Remove already used slots
    $allSlots = array_values(array_diff($allSlots, array_keys($this->scribblesBySlots)));

    // Number X can be placed anyway
    if ($number == NUMBER_X) {
      return $allSlots;
    }

    // Check each constraints
    $forbiddenSlots = [];
    foreach ($this->increasingConstraints as $slotSequence) {
      $curr = [];
      $previous = -1;
      foreach ($slotSequence as $i => $slotId) {
        $scribble = $this->scribblesBySlots[$slotId] ?? null;
        if (is_null($scribble) || $scribble->getNumber() == NUMBER_X) {
          $curr[] = $slotId;
        } else {
          if ($scribble->getNumber() < $number) {
            $forbiddenSlots = array_merge($forbiddenSlots, $curr);
          }
          $curr = [];
          $previous = $scribble->getNumber();
        }
      }
      if ($number <= $previous) {
        $forbiddenSlots = array_merge($forbiddenSlots, $curr);
      }
      $allSlots = array_values(array_diff($allSlots, $forbiddenSlots));
    }
    return $allSlots;
  }

  // DATAS
  protected array $datas = [];
  protected array $slotsBySection = [];
  public function getUiData()
  {
    return $this->datas;
  }
}
