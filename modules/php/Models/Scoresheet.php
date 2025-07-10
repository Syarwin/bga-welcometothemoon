<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet1;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet2;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet3;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet4;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet5;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet7;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

class Scoresheet
{
  protected ?Player $player;
  protected int $scenario;
  protected Collection $scribbles;
  protected array $scribblesBySlots;

  public function setupScenario(): void {}

  public function getPId(): int
  {
    return $this->player->getId();
  }

  // PHASE 5
  public static function phase5Check(): void
  {
    switch (Globals::getScenario()) {
      case 1:
        Scoresheet1::phase5Check();
        break;
      case 2:
        Scoresheet2::phase5Check();
        break;
      case 3:
        Scoresheet3::phase5Check();
        break;
      case 4:
        Scoresheet4::phase5Check();
        break;
      case 5:
        Scoresheet5::phase5Check();
        break;
      case 6:
        Scoresheet6::phase5Check();
        break;
      case 7:
        Scoresheet7::phase5Check();
        break;
      case 8:
        Scoresheet8::phase5Check();
        break;
      default:
        die("Unsupported phase 5 for this scenario");
    }
  }

  protected static function resolveRaceSlots(): array
  {
    $scribbles = [];
    // Might be multipliers in S2, filling bonuses in S4, finished skyscrapers bonuses in S5, etc.
    $raceSlots = array_unique(Globals::getRaceSlots());
    if (empty($raceSlots) && Globals::getScenario() === 2) {
      // Backward compatibility
      // TODO: Remove after all tables will move to Globals::setRaceSlots. Maybe around September 2025?
      $raceSlots = array_unique(Globals::getCircledMultipliers());
      Globals::setCircledMultipliers([]);
    }
    if (count($raceSlots) > 0) {
      $players = Players::getAll();
      foreach ($raceSlots as $raceSlot) {
        /** @var Player $player */
        foreach ($players as $player) {
          $scoresheet = $player->scoresheet();
          if (!$scoresheet->hasScribbledSlot($raceSlot)) {
            $scribbles[] = $scoresheet->addScribble($raceSlot);
          }
        }
      }

      Globals::setRaceSlots([]);
    }
    return $scribbles;
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
    $this->scribbles = Scribbles::getOfPlayer($this->getPId());
    $this->scribblesBySlots = [];
    foreach ($this->scribbles as $scribble) {
      $slot = $scribble->getSlot();
      if (is_null($slot)) continue;

      $this->scribblesBySlots[$slot][] = $scribble;
    }
  }

  public function getScribbles(): Collection
  {
    return $this->scribbles;
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

  public function hasScribbledSomeSlots(array $slots, int $target, ?int $type = null): bool
  {
    $n = 0;
    foreach ($slots as $slot2) {
      if ($this->hasScribbledSlot($slot2, $type)) {
        $n++;
      }

      if ($n >= $target) {
        return true;
      }
    }

    return false;
  }

  public function hasScribbledSlots(array $slots, ?int $type = null): bool
  {
    $slots = array_unique($slots);
    return $this->hasScribbledSomeSlots($slots, count($slots), $type);
  }

  public function countScribbledSlots(array $slots, ?int $type = null): int
  {
    $n = 0;
    foreach ($slots as $slot) {
      if ($this->hasScribbledSlot($slot, $type)) {
        $n++;
      }
    }
    return $n;
  }

  public function countScribblesInSection(string $section, ?int $type = null): int
  {
    return $this->countScribbledSlots($this->getSectionSlots($section), $type);
  }

  public function countAllUnscribbledSlots(): int
  {
    $allSlots = array_merge(...$this->getNumberBlocks());
    $unscribbledSlots = $this->getAllUnscribbled($allSlots);
    return count($unscribbledSlots);
  }

  public function getNumberBlocks(): array
  {
    return $this->numberBlocks;
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

  public function getScribbleReactions(Scribble $scribble, string $methodSource): array
  {
    return [];
  }

  public function getCombinationAtomicAction(array $combination, int $slot): ?array
  {
    return null;
  }

  protected array $jokers = [];

  public function canUseJoker(): bool
  {
    return $this->getFirstUnscribbledJoker() !== null;
  }

  public function getFirstUnscribbledJoker(): int|null
  {
    $circledJokers = array_filter($this->jokers, fn($jokerSlot) => $this->hasScribbledSlot($jokerSlot, SCRIBBLE_CIRCLE));
    return $this->getFirstUnscribbled($circledJokers, SCRIBBLE);
  }


  public function getFirstUnscribbled(array $slots, ?int $type = null): int|null
  {
    $allUnscribbled = $this->getAllUnscribbled($slots, $type);
    return $allUnscribbled ? $allUnscribbled[0] : null;
  }

  public function getAllUnscribbled(array $slots, ?int $type = null): array|null
  {
    return array_values(array_filter($slots, fn($slot) => !$this->hasScribbledSlot($slot, $type)));
  }

  // Given a map : slot => mult, return the mult corresponding to the first unscribbled slot
  public function getMultiplier(array $multPerSlots, int $maxMult): int
  {
    $key = $this->getFirstUnscribbled(array_keys($multPerSlots));
    return is_null($key) ? $maxMult : $multPerSlots[$key];
  }

  protected array $multipliers = [];

  public function getMultiplierOfType(string $type): int
  {
    $infos = $this->multipliers[$type];
    return $this->getMultiplier($infos['mults'], $infos['maxMult']);
  }

  public function isWriteXOptional(): bool
  {
    return true;
  }

  public function computeUiData(): array
  {
    return [];
  }

  // Generic UI-data for missions for all scenarios except the 1st one
  public function computeMissionsUiData(&$data): int
  {
    $missionPoints = 0;
    $stacks = ['stack-A', 'stack-B', 'stack-C'];
    foreach ($this->getSectionSlots('plans') as $i => $slot) {
      $stack = $stacks[$i];
      $scribbles = $this->scribblesBySlots[$slot] ?? [];
      $missionPoint = 0;
      if (!empty($scribbles)) {
        $missionPoint = $scribbles[0]->getNumber();
      }
      $data[] = ["overview" => $stack, "v" => $missionPoint];
      $missionPoints += $missionPoint;
    }

    return $missionPoints;
  }

  public function getScore(): int
  {
    $score = 0;
    foreach ($this->computeUiData() as $slot) {
      if ($slot['score'] ?? false) {
        $score = $slot['v'];
      }
    }
    return $score;
  }

  /**
   * getAvailableSlotsForNumber : where can I put a given number
   *  - considering filled-up slots
   *  - considering increasing sequence constraint
   */
  protected array $numberBlocks = [];

  public function getAvailableSlotsForNumber(int $number, string $action)
  {
    $allSlots = $this->slotsBySection['numbers'];
    // Remove already used slots
    $allSlots = array_values(array_diff($allSlots, array_keys($this->scribblesBySlots)));

    // Number X can be placed anyway
    if ($number == NUMBER_X) {
      return $allSlots;
    }

    // Check each constraint
    $forbiddenSlots = [];
    foreach ($this->getNumberBlocks() as $slotSequence) {
      $curr = [];
      $previous = -1;
      foreach ($slotSequence as $slotId) {
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
      if ($previous <= 17 && $number <= $previous) {
        $forbiddenSlots = array_merge($forbiddenSlots, $curr);
      }
      $allSlots = array_values(array_diff($allSlots, $forbiddenSlots));
    }
    return $allSlots;
  }

  public function isEndOfGameTriggered(): bool
  {
    // System errors
    if (is_null($this->getNextFreeSystemErrorSlot())) {
      Stats::setEnding(STAT_ENDING_SYSTEM_ERRORS);
      Notifications::endGameTriggered($this->player, 'errors');
      return true;
    }

    // Plans
    $unsatisfiedPlans = PlanCards::getCurrent()->filter(fn($plan) => !$plan->isValidated($this->player));
    if ($unsatisfiedPlans->empty()) {
      Stats::setEnding(STAT_ENDING_MISSIONS);
      Notifications::endGameTriggered($this->player, 'plans');
      return true;
    }

    // Full scoresheet
    if ($this->countAllUnscribbledSlots() === 0) {
      Stats::setEnding(STAT_ENDING_FILLED_ALL);
      Notifications::endGameTriggered($this->player, 'houses');
      return true;
    }

    return false;
  }


  /**
   * DATAS
   */
  protected array $datas = [];
  protected array $slotsBySection = [];

  public function getUiData()
  {
    return $this->datas;
  }

  /**
   * getSectionSlots : get all the slots of a given section
   */
  public function getSectionSlots(string $section): array
  {
    return $this->slotsBySection[$section] ?? [];
  }

  /**
   * getSectionFreeSlots : get only the non-scribbled over slots of a section
   */
  public function getSectionFreeSlots(string $section): array
  {
    $allSlots = $this->slotsBySection[$section] ?? [];
    $freeSlots = [];
    foreach ($allSlots as $slot) {
      if (!$this->hasScribbledSlot($slot)) {
        $freeSlots[] = $slot;
      }
    }

    return $freeSlots;
  }

  /**
   * getNextFreeSystemErrorSlot: return the next system Error slot available, if any
   */
  public function getNextFreeSystemErrorSlot(): ?int
  {
    $slots = $this->getSectionFreeSlots('errors');
    return empty($slots) ? null : $slots[0];
  }

  public function getCompleteSectionsCount(): int
  {
    return 0;
  }

  public function prepareForPhaseFive(array $args) {}

  protected function getStandardPlanningAction(): array
  {
    return [
      'action' => WRITE_X,
      'args' => [
        'source' => [
          'name' => clienttranslate('Planning action'),
        ],
      ]
    ];
  }

  protected function getStandardPlanningReaction(array $jokers = [], array $slots = []): array
  {
    if (empty($slots)) {
      $slots = $this->getSectionSlots('planningmarkers');
    }
    return [
      'action' => CIRCLE_NEXT_IN_ROW,
      'args' => [
        'symbol' => CIRCLE_SYMBOL_PLANNING,
        'slots' => $slots,
        'jokers' => $jokers,
        'scribbleType' => SCRIBBLE,
      ]
    ];
  }

  protected function getStandardAstronautAction(array $jokers = [], array $slots = []): array
  {
    if (empty($slots)) {
      $slots = $this->getSectionSlots('astronautmarkers');
    }
    return [
      'action' => CIRCLE_NEXT_IN_ROW,
      'args' => [
        'symbol' => CIRCLE_SYMBOL_ASTRONAUT,
        'slots' => $slots,
        'jokers' => $jokers,
        'scribbleType' => SCRIBBLE,
      ]
    ];
  }

  public function getNegativePointsFromErrors(): int
  {
    $scribbledErrors = $this->countScribblesInSection('errors');
    return 5 * $scribbledErrors;
  }

  public static function getMostAstronautsRankAndAmount(int $pId): array
  {
    $astronauts = [];
    /** @var Player $player */
    foreach (Players::getAll() as $player) {
      $astronautsCount = $player->scoresheet()->countScribblesInSection('astronautmarkers');
      if ($astronautsCount > 0) {
        $astronauts[$player->getId()] = $astronautsCount;
      }
    }
    if (Globals::isSolo()) {
      $astra = Players::getAstra();
      $astronauts['astra'] = $astra->getCardsByActionMap()[ASTRONAUT];
    }

    return Utils::getRankAndAmountOfKey($astronauts, $pId);
  }


  ////// SPECIFIC SCENARIO FUNCTION - TO AVOID IDE SCREAMS //////
  public function getUnbuiltDomeSections(): array
  {
    return [];
  }
}
