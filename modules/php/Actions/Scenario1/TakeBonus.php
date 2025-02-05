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
    $bonus = $this->getCtxArg('bonus');
    foreach ($bonus as $type => $n) {
      if ($type == ROCKET) {
        $flow = [
          'action' => CROSS_ROCKETS,
          'args' => ['n' => $n]
        ];
        // Rocket requiring activation first
        if (array_key_exists('check', $bonus)) {
          $flow['args']['check'] = $bonus['check'];
        }
      } else if ($type == NUMBER_X) {
        $flow = [
          'action' => WRITE_X,
        ];
      } else if ($type == ACTIVATION) {
        $flow = [
          'action' => ROCKET_ACTIVATION,
        ];
      } else if ($type == SABOTAGE) {
        $flow = [
          'action' => ACTIVATE_SABOTAGE,
        ];
      }
    }

    if (is_null($flow)) {
      die("Unrecognized bonus: " . var_dump($this->getCtxArg('bonus')));
    } else {
      $flow['pId'] = $player->getId();
    }

    // Tag the slot and the name of quarter
    $slot = $this->getCtxArg('slot');
    $name = $this->getCtxArg('name');
    $flow['args']['source'] = [
      'slot' => $slot,
      'name' => $name,
    ];

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
    $flowTree = $this->getFlowTree($player);
    return is_null($flowTree) ? true : $flowTree->isOptional();
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
    // $slot = $this->getCtxArg('slot');
    // $name = $this->getCtxArg('name');
    // $scribble = $player->scoresheet()->addScribble($slot);
    // Notifications::takeBonus($player, $scribble, $name);
    $flow = $this->getFlow($player);
    $this->insertAsChild($flow);
  }
}
