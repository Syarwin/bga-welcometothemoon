<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario1\CrossRockets;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet5;

class AccomplishMission extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_ACCOMPLISH_MISSION;
  }

  public function isDoable($player): bool
  {
    return PlanCards::getAccomplishablePlans($player)->count() > 0;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function argsAccomplishMission()
  {
    $player = $this->getPlayer();
    return [
      'plans' => PlanCards::getAccomplishablePlans($player)->getIds(),
    ];
  }

  public function actAccomplishMission(int $planId)
  {
    $args = $this->getArgs();
    if (!in_array($planId, $args['plans'])) {
      throw new \BgaUserException('You cannot accomplish this mission. Should not happen.');
    }

    $player = $this->getPlayer();
    $plan = PlanCards::get($planId);
    $scoresheet = $player->scoresheet();
    $scenarioId = Globals::getScenario();
    $scribbles = [];

    // Was the plan already validated or not ?
    $validationScribble = Scribbles::getInLocation("plan-$planId")->first();
    $firstValidation = is_null($validationScribble) || $validationScribble->getTurn() == Globals::getTurn();

    // Mark the plan as validated
    $scribbles[] = Scribbles::add($player, [
      'location' => "plan-$planId",
      'type' => SCRIBBLE_PLAN_MARKER,
    ]);

    $validatedPlans = PGlobals::getValidatedPlans($player->getId());
    $validatedPlans[] = $planId;
    PGlobals::setValidatedPlans($player->getId(), $validatedPlans);
    $scribbles[] = Scribbles::add($player, [
      'location' => "plan-$planId",
      'type' => SCRIBBLE_CHECKMARK,
    ]);

    // Compute the reward
    $reward = $plan->getReward($firstValidation);

    // Increment a corresponding stat
    $firstValidation ? Stats::incMissionsFirstNumber($player->getId(), 1) : Stats::incMissionsSecondNumber($player->getId(), 1);

    ///// Mark the scoresheet /////
    $slotId = $scoresheet->getMissionSlotNumber($plan->getStackIndex());
    // SCENARIO 1 => reward are rockets, not points, just put a checkmark instead
    if ($scenarioId == 1) {
      $scribbles[] = $scoresheet->addScribble($slotId, SCRIBBLE_CHECKMARK);
    }
    // SCENARIO 5 => checkmark + circle symbol instead
    else if ($scenarioId == 5) {
      $scribbles[] = $scoresheet->addScribble($slotId, SCRIBBLE_CHECKMARK);
      $symbolSlotId = Scoresheet5::$planSymbols[$slotId][$firstValidation ? 0 : 1];
      $scribbles[] = $scoresheet->addScribble($symbolSlotId, SCRIBBLE_CIRCLE);
    }
    // SCENARIO 6 => might trigger a virus!
    else if ($scenarioId == 6 && $firstValidation) {
      // Reward
      $scribbles[] = $scoresheet->addScribble($slotId, $reward);

      // Check if it's REALLY the first validation
      $i = $plan->getStackIndex();
      $slot = $scoresheet->getSectionSlots('missionviruses')[$i];
      if (!$scoresheet->hasScribbledSlot($slot)) {
        $scribbles[] = $scoresheet->addScribble($slot);

        // register for phase5 again
        $virusType = [VIRUS_RED, VIRUS_PURPLE, VIRUS_YELLOW][$i];
        $viruses = Globals::getActivatedViruses();
        $viruses[] = $virusType;
        Globals::setActivatedViruses($viruses);
      }
    } else {
      $scribbles[] = $scoresheet->addScribble($slotId, $reward);
    }

    // Notify
    Notifications::accomplishMission($player, $plan, $scribbles, $firstValidation);

    // SPECIAL REWARD FOR SOME SCENARIO
    // SCENARIO 1 => cross reward
    if ($scenarioId == 1) {
      CrossRockets::crossRocketAux($player, $reward, clienttranslate("accomplished mission"));
    }

    // Any additional mission to accomplish ?
    if (PlanCards::getAccomplishablePlans($player)->count() > 0) {
      $this->insertAsChild([
        'action' => ACCOMPLISH_MISSION,
      ]);
    }
  }
}
