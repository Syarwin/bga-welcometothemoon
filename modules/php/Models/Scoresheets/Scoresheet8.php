<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

include_once dirname(__FILE__) . "/../../Material/Scenario8.php";

class Scoresheet8 extends Scoresheet
{
  protected int $scenario = 8;
  protected array $datas = DATAS8;
  protected array $numberBlocks = [
    [1, 2, 3],
    [4, 5, 6, 7, 8],
    [9, 10, 11, 12, 13],
    [14, 15, 16, 17, 18, 19, 20],
    [21, 22, 23, 24, 25],
    [26, 27, 28, 29, 30],
    [31, 32, 33]
  ];


  protected Player|Astra $player1; // Player at the BOTTOM
  protected Player|Astra $player2; // Player at the TOP

  public function __construct(Player|Astra|null $player1, Player|Astra|null $player2 = null)
  {
    if (is_null($player1)) return; // Used to extract datas
    $this->player1 = $player1;
    $this->player2 = $player2;
    $this->fetch();

    // Extract info from datas
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
    $this->scribbles = Scribbles::getOfPlayer($this->player1->getId());
    $this->scribblesBySlots = [];
    foreach ($this->scribbles as $scribble) {
      $slot = $scribble->getSlot();
      if (is_null($slot)) continue;

      $this->scribblesBySlots[$slot][] = $scribble;
    }
  }




  // PHASE 5
  public static function phase5Check(): void {}

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    return [];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    return null;
  }

  public function prepareForPhaseFive(array $args) {}


  /**
   * UI DATA
   */
  public function computeUiData(): array
  {
    $data = [];

    // Number of numbered slots
    $nNumberedSlots = $this->countScribblesInSection('numbers');
    $data[] = ["overview" => "numbers", "v" => $nNumberedSlots, 'max' => count($this->getSectionSlots('numbers'))];
    $data[] = ["panel" => "numbers", "v" => $nNumberedSlots];

    // // Missions
    // $missionPoints = $this->computeMissionsUiData($data);
    // $data[] = ["slot" => 37, "v" => $missionPoints];


    // System errors
    // $scribbledErrors = $this->countScribblesInSection('errors');
    // $negativePoints = 5 * $scribbledErrors;
    // $data[] = ["slot" => 46, "v" => $negativePoints];
    // $data[] = ["overview" => "errors", "v" => -$negativePoints, "details" => ($scribbledErrors . " / 3")];
    // $data[] = ["panel" => "errors", "v" => $scribbledErrors];


    // Total score
    // $data[] = [
    //   "slot" => 47,
    //   "score" => true,
    //   "overview" => "total",
    //   "v" => -$negativePoints,
    // ];

    return $data;
  }
}
