<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

class Quarter
{
  private int $id;
  private string $name;
  private array $slots;
  private array $plantsSlots;
  private array $pointsSlots;

  public function __construct($data)
  {
    list($this->id, $this->name, $this->slots, $this->plantsSlots, $this->pointsSlots) = $data;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function hasSlot(int $slot): bool
  {
    return in_array($slot, $this->slots);
  }

  public function getSlots(): array
  {
    return $this->slots;
  }

  public function getPlantsSlots(): array
  {
    return $this->plantsSlots;
  }

  public function getPointsSlots(): array
  {
    return $this->pointsSlots;
  }
}
