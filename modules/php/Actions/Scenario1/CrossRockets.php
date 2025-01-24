<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario1;

use Bga\Games\WelcomeToTheMoon\Models\Player;

class CrossRockets extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isOptional(): bool
  {
    return true;
  }

  public function getDescription(): string|array
  {
    return "CROSS ROCKETS";
  }

  public function stCrossRockets()
  {
    die("test");
  }
}
