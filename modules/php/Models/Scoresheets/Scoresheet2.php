<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario2.php";


class Scoresheet2 extends Scoresheet
{
  protected int $scenario = 2;
  protected array $datas = DATAS2;

  public function getCombinationAtomicAction(array $combination): ?array
  {
    switch ($combination['action']) {
      case ENERGY:
        return ['action' => CIRCLE_ENERGY];
      case ROBOT:
        return ['action' => PROGRAM_ROBOT];
    }
    return null;
  }


  // NUMBER SLOTS IDS ARE 1 - 36
  // WALL SLOTS IDS ARE 90 - 124
  /**
   * getSections: based on current walls, compute the different sections
   */
  public function getSections()
  {
    $sections = [];
    $section = [1];
    for ($numberSlot = 2; $numberSlot <= 36; $numberSlot++) {
      $wallSlotId = 90 + ($numberSlot - 2);
      if ($this->hasScribbledSlot($wallSlotId)) {
        $sections[] = $section;
        $section = [$numberSlot];
      } else {
        $section[] = $numberSlot;
      }
    }
    $sections[] = $section;
    return $sections;
  }

  public function getIncreasingSequencesConstraints()
  {
    return $this->getSections();
  }
}
