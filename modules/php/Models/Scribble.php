<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

class Scribble extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Model
{
  protected $table = 'meeples';
  protected $primary = 'meeple_id';
  protected $attributes = [
    'id' => 'scribble_id',
    'location' => 'meeple_location',
    'state' => 'meeple_state',
    'type' => ['type', 'int'],
    'typeArg' => 'type_arg',
    'turn' => ['turn', 'int'],
  ];
}
