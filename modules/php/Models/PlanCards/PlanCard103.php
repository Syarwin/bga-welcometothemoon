<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet7;

class PlanCard103 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number 3 starships completely.')
    ];
    $this->rewards = [10, 5];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $blocks = Scoresheet7::getBlockInfos();
    $starships = array_fill(0, 6, true);
    foreach ($blocks as $blockId => $block) {
      $starship = $block['starship'];
      if ($starships[$starship] && !$scoresheet->isCompletelyNumbered($blockId)) {
        $starships[$starship] = false;
      }
    }
    return count(array_filter($starships)) >= 3;
  }
}
