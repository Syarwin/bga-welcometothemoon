<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

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
}
