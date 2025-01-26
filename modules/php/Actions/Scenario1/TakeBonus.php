<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class TakeBonus extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function getFlow(Player $player): array
  {
    $flow = null;
    foreach ($this->getCtxArg('bonus') as $type => $n) {
      if ($type == ROCKET) {
        $flow = [
          'action' => CROSS_ROCKETS,
          'args' => ['n' => $n]
        ];
      }
    }

    return $flow;
  }

  public function getFlowTree(Player $player)
  {
    $flow = $this->getFlow($player);
    return is_null($flow) ? null : Engine::buildTree($flow);
  }

  public function isOptional(): bool
  {
    $player = $this->getPlayer();
    if (is_null($this->getFlowTree($player))) {
      return true;
    }
    return $this->getFlowTree($player)->isOptional();
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    $flowTree = $this->getFlowTree($player);
    return is_null($flowTree) ? false : $flowTree->isDoable($player);
  }

  // public function doNotDisplayIfNotDoable(): bool
  // {
  //   $player = Players::getActive();
  //   $flowTree = $this->getFlowTree($player);
  //   return is_null($flowTree) ? false : $flowTree->doNotDisplayIfNotDoable();
  // }

  public function isIndependent(?Player $player = null): bool
  {
    $flowTree = $this->getFlowTree($player);
    return is_null($flowTree) ? false : $flowTree->isIndependent($player);
  }

  public function getDescription(): string|array
  {
    $flowTree = $this->getFlowTree($this->getPlayer());
    if (is_null($flowTree)) {
      return '';
    }

    return $flowTree->getDescription();
  }

  public function stTakeBonus()
  {
    return [];
  }

  public function actTakeBonus()
  {
    $player = $this->getPlayer();
    $slot = $this->getCtxArg('slot');
    $type = SCRIBBLE;
    $scribble = Scribbles::add($player, [
      'type' => $type,
      "location" => "slot-$slot",
    ]);
    Notifications::takeBonus($player, $scribble);
    $flow = $this->getFlow($player);
    $this->insertAsChild($flow);
  }
}
