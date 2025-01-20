<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

class ConstructionCard extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Model
{
  protected $table = 'construction_cards';
  protected $primary = 'card_id';
  protected $attributes = [
    'id' => ['card_id', 'int'],
    'location' => 'card_location',
    'state' => ['card_state', 'int'],
    'number' => ['number', 'float'],
    'action' => 'action',
  ];
  protected int $id;
  protected string $location;
  protected int $state;
  protected float $number;
  protected string $action;
}
