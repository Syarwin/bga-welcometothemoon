<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Actions\Scenario1\CrossRockets;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;

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
    $scenarioId = Globals::getScenario();
    $scribbles = [];

    // Was the plan already validated or not ?
    $validationScribble = Scribbles::getInLocation("plan-$planId")->first();
    $firstValidation = is_null($validationScribble) || $validationScribble->getTurn() == Globals::getTurn();

    // Mark the plan as validated
    if (is_null($validationScribble)) {
      $scribbles[] = Scribbles::add(0, [
        'location' => "plan-$planId",
        'type' => SCRIBBLE,
      ]);
    }

    $validatedPlans = PGlobals::getValidatedPlans($player->getId());
    $validatedPlans[] = $planId;
    PGlobals::setValidatedPlans($player->getId(), $validatedPlans);
    $scribbles[] = Scribbles::add($player, [
      'location' => "plan-$planId",
      'type' => SCRIBBLE_CHECKMARK,
    ]);

    // Compute the reward
    $plan = PlanCards::get($planId);
    $reward = $plan->getReward($firstValidation);

    ///// Mark the scoresheet /////
    $slotId = $player->scoresheet()->getMissionSlotNumber($plan->getStackIndex());
    // SCENARIO 1 => reward are rockets, not points, just put a checkmark instead
    if ($scenarioId == 1) {
      $scribbles[] = $player->scoresheet()->addScribble($slotId, SCRIBBLE_CHECKMARK);
    } else {
      $scribbles[] = $player->scoresheet()->addScribble($slotId, $reward);
    }

    // Notify
    Notifications::accomplishMission($player, $plan, $scribbles, $firstValidation);

    // SPECIAL REWARD FOR SOME SCENARIO
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
