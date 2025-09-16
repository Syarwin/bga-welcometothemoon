<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\PGlobals;

class PlanCard extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Model
{
  protected string $table = 'plan_cards';
  protected string $primary = 'card_id';
  protected array $attributes = [
    'id' => 'card_id',
    'location' => 'card_location',
    'state' => ['card_state', 'int'],
  ];
  protected string $id;
  protected string $location;
  protected int $state;

  protected array $staticAttributes = [
    ['desc', 'obj']
  ];
  protected array $desc = [];
  protected array $rewards = [];

  public function isValidated(Player|Astra $player): bool
  {
    return in_array($this->id, PGlobals::getValidatedPlans($player->getId()));
  }

  public function canAccomplish(Player $player): bool
  {
    return false;
  }

  public function getStackIndex()
  {
    $map = [
      'stack-A' => 0,
      'stack-B' => 1,
      'stack-C' => 2,
    ];

    return $map[$this->location];
  }

  public function getReward(bool $firstValidation): int
  {
    return $this->rewards[$firstValidation ? 0 : 1] ?? 0;
  }
}
