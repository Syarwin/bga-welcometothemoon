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
  protected int $type;

  public function getPId(): int
  {
    return (int) explode("-", $this->id)[0];
  }

  public function getIndex(): int
  {
    return (int) explode("-", $this->id)[1];
  }

  public function getSlot(): int
  {
    return (int) explode('-', $this->location)[1];
  }

  public function getNumber(): int
  {
    return $this->type;
  }
}
