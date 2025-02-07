<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;

class PlanCard69 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Have 5 System Error boxes circled and not crossed off.')
    ];
    $this->rewards = [3, 2];
  }
}
