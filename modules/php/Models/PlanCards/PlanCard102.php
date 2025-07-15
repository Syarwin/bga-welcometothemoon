<?php

namespace Bga\Games\WelcomeToTheMoon\Models\PlanCards;

use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet7;

class PlanCard102 extends PlanCard
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->desc = [
      clienttranslate('Number 7 modules completely.')
    ];
    $this->rewards = [9, 4];
  }

  public function canAccomplish(Player $player): bool
  {
    $scoresheet = $player->scoresheet();
    $modules = array_filter(Scoresheet7::getBlockInfos(), function ($block) {
      return $block['type'] === BLOCK_MODULE;
    });
    $numberedModulesCount = 0;
    foreach ($modules as $moduleId => $module) {
      if ($scoresheet->isCompletelyNumbered($moduleId)) {
        $numberedModulesCount++;
      }
    }
    return $numberedModulesCount >= 7;
  }
}
