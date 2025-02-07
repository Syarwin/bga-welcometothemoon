<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet1;

class Scoresheet
{
  protected ?Player $player;
  protected int $scenario;
  protected Collection $scribbles;
  protected array $scribblesBySlots;

  // PHASE 5
  public static function phase5Check(): void
  {
    switch (Globals::getScenario()) {
      case 1:
        Scoresheet1::phase5Check();
        break;
      default:
        die("Unsupported phase 5 for this scenario");
    }
  }

  public function getMissionSlotNumber(int $stack): int
  {
    return $this->slotsBySection['plans'][$stack] ?? 0;
  }

  public function __construct(?Player $player)
  {
    if (is_null($player)) return; // Used to extract datas
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

  public function fetch(): void
  {
    // Fetch scribbles
    $this->scribbles = Scribbles::getOfPlayer($this->player);
    $this->scribblesBySlots = [];
    foreach ($this->scribbles as $scribble) {
      $this->scribblesBySlots[$scribble->getSlot()][] = $scribble;
    }
  }

  public function hasScribbledSlot(int $slot, ?int $type = null): bool
  {
    foreach (($this->scribblesBySlots[$slot] ?? []) as $scribble) {
      if (is_null($type) || $scribble->getType() == $type) {
        return true;
      }
    }

    return false;
  }

  public function hasScribbledSlots(array $slots): bool
  {
    foreach ($slots as $slot2) {
      if (!$this->hasScribbledSlot($slot2)) {
        return false;
      }
    }
    return true;
  }

  public function addScribble($location, $type = SCRIBBLE): Scribble
  {
    $scribble = Scribbles::add($this->player, [
      'type' => $type,
      'location' => "slot-$location",
    ]);
    $this->scribbles[$scribble->getId()] = $scribble;
    $this->scribblesBySlots[$scribble->getSlot()][] = $scribble;
    return $scribble;
  }

  public function getScribbleReactions(Scribble $scribble): array
  {
    return [];
  }

  /**
   * getAvailableSlotsForNumber : where can I put a given number
   *  - considering filled-up slots
   *  - considering increasing sequence constraint
   */
  protected array $increasingConstraints = [];
  protected function getIncreasingSequences()
  {
    return $this->increasingConstraints;
  }
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
    foreach ($this->getIncreasingSequences() as $slotSequence) {
      $curr = [];
      $previous = -1;
      foreach ($slotSequence as $i => $slotId) {
        $scribble = $this->scribblesBySlots[$slotId][0] ?? null;
        if (is_null($scribble) || $scribble->getNumber() == NUMBER_X) {
          $curr[] = $slotId;
        } else {
          if ($scribble->getNumber() <= $number || $previous >= $number) {
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

  // SYSTEM ERRORS
  public function getFreeSystemErrorSlot(): ?int
  {
    $allSlots = $this->slotsBySection['errors'];
    foreach ($allSlots as $slot) {
      if (!$this->hasScribbledSlot($slot)) {
        return $slot;
      }
    }

    return null;
  }

  // DATAS
  protected array $datas = [];
  protected array $slotsBySection = [];
  public function getUiData()
  {
    return $this->datas;
  }
}
