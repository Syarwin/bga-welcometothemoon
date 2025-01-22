<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

class Scribble extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Model
{
  protected string $table = 'meeples';
  protected string $primary = 'meeple_id';
  protected array $attributes = [
    'id' => 'scribble_id',
    'location' => 'scribble_location',
    'state' => 'scribble_state',
    'type' => ['type', 'int'],
    'typeArg' => 'type_arg',
    'turn' => ['turn', 'int'],
  ];
  protected string $id;
  protected string $location;

  public function getPId()
  {
    return (int) explode("-", $this->id)[0];
  }

  public function getIndex()
  {
    return (int) explode("-", $this->id)[1];
  }

  public function getSlot()
  {
    return explode('-', $this->location)[1];
  }

  public function getNumber()
  {
    return $this->type;
  }
}
