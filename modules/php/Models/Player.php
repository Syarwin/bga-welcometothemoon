<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Model
{
  protected string $table = 'player';
  protected string $primary = 'player_id';
  protected array $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
  ];
  protected int $id;

  public function getPref(int $prefId)
  {
    return Game::get()->getGameUserPreference($this->id, $prefId);
  }

  public function getStat($name)
  {
    $name = 'get' . Utils::ucfirst($name);
    return Stats::$name($this->id);
  }

  public function canTakeAction($action, $ctx)
  {
    return Actions::isDoable($action, $ctx, $this);
  }

  protected ?Scoresheet $scoresheet = null;
  public function scoresheet(): ?Scoresheet
  {
    if (is_null($this->scoresheet) && Globals::getScenario() != 0) {
      $className = 'Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet' . Globals::getScenario();
      $this->scoresheet = new $className($this);
    }
    return $this->scoresheet;
  }

  public function getCombination()
  {
    $stack = PGlobals::getStack($this->id);
    if (is_null($stack)) return null;

    $combination = ConstructionCards::getCombination($stack[0]);
    return $combination;
  }
}
