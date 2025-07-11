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
  protected array $allowedBlocksByNumber = [
    0 => [0],
    1 => [0],
    2 => [0],
    3 => [0, 1],
    4 => [1],
    5 => [1, 2],
    6 => [2],
    7 => [2, 3],
    8 => [3],
    9 => [3, 4],
    10 => [4],
    11 => [4, 5],
    12 => [5],
    13 => [5, 6],
    14 => [6],
    15 => [6],
    16 => [6],
    17 => [6]
  ];


  public function setupScenario(): void
  {
    $this->addScribble(221, SCRIBBLE_INSIGNAS[$this->player2->getNo()], true);
    $this->addScribble(222, SCRIBBLE_INSIGNAS[$this->player1->getNo()], true);
  }


  public function getAvailableSlotsForNumber(int $number, string $action): array
  {
    // Merge available block for that number
    $blocks = $this->allowedBlocksByNumber[$number];
    $slots = [];
    foreach ($blocks as $blockId) {
      $slot = $this->getFirstUnscribbled($this->numberBlocks[$blockId]);
      if (!is_null($slot)) $slots[] = $slot;
    }

    return $slots;
  }



  // PHASE 5
  public static function phase5Check(): void {}

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    return [];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {

    switch ($combination['action']) {
      case ASTRONAUT:
        return [
          'action' => CIRCLE_NEXT_IN_ROW,
          'args' => [
            'symbol' => CIRCLE_SYMBOL_ASTRONAUT,
            'slots' => $this->getPlayerSectionSlots('astronautmarkers'),
            'scribbleType' => SCRIBBLE_CIRCLE,
          ]
        ];
        // case PLANNING:
        //   return $this->getStandardPlanningAction();
        // case ROBOT:
        //   return [
        //     'action' => S5_BUILD_DOME,
        //     'args' => ['parity' => $combination['number'] % 2],
        //   ];
        // case ENERGY:
        //   return [
        //     'action' => S5_ENERGY_UPGRADE
        //   ];

        // case PLANT:
        // case WATER:
    }
    return null;
  }

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


  //////////////////////////////////////////
  /////// HANDLE DOUBLE PLAYER /////////////
  //////////////////////////////////////////
  protected Player|Astra $player1; // Player at the BOTTOM
  protected Player|Astra $player2; // Player at the TOP
  protected int $whoIsPlaying = 0;

  public function __construct(Player|Astra|null $player1, Player|Astra|null $player2 = null, int|null $whoIsPlaying = null)
  {
    if (is_null($player1)) return; // Used to extract datas
    $this->player1 = $player1;
    $this->player2 = $player2;
    $this->whoIsPlaying = $whoIsPlaying;
    $this->fetch();

    // Extract info from datas
    foreach ($this->datas['sections'] as $section) {
      $this->slotsBySection[$section['id']] = [];
      foreach ($section['elts'] as $elt) {
        $this->slotsBySection[$section['id']][] = $elt['id'];
      }
    }
  }

  public function getPId(): int
  {
    return $this->player1->getId();
  }

  public function addScribble($location, $type = SCRIBBLE, $isSetup = false): Scribble
  {
    if (!$isSetup) {
      $currentPlayer = Globals::getTurn() % 2 == 0 ? $this->player2 : $this->player1;
      $currentInsigna = SCRIBBLE_INSIGNAS[$currentPlayer->getNo()];
      if (!in_array($type, [SCRIBBLE, SCRIBBLE_CIRCLE])) {
        $type = $currentInsigna;
      }
    }

    $scribble = Scribbles::add($this->player1, [
      'type' => $type,
      'location' => "slot-$location",
    ]);
    $this->scribbles[$scribble->getId()] = $scribble;
    $this->scribblesBySlots[$scribble->getSlot()][] = $scribble;
    return $scribble;
  }

  // Useful for section slots that are mirrored on top and bottom
  public function getPlayerSectionSlots(string $section): array
  {
    return $this->slotsBySection[$section . $this->whoIsPlaying] ?? [];
  }


  public function isEndOfGameTriggered(): bool
  {
    // // System errors
    // if (is_null($this->getNextFreeSystemErrorSlot())) {
    //   Stats::setEnding(STAT_ENDING_SYSTEM_ERRORS);
    //   Notifications::endGameTriggered($this->player, 'errors');
    //   return true;
    // }

    // // Plans
    // $unsatisfiedPlans = PlanCards::getCurrent()->filter(fn($plan) => !$plan->isValidated($this->player));
    // if ($unsatisfiedPlans->empty()) {
    //   Stats::setEnding(STAT_ENDING_MISSIONS);
    //   Notifications::endGameTriggered($this->player, 'plans');
    //   return true;
    // }

    // // Full scoresheet
    // if ($this->countAllUnscribbledSlots() === 0) {
    //   Stats::setEnding(STAT_ENDING_FILLED_ALL);
    //   Notifications::endGameTriggered($this->player, 'houses');
    //   return true;
    // }

    return false;
  }

  //////////////////////////////////////////
  //////////////////////////////////////////
  //////////////////////////////////////////

}
