<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet8;

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
    'state' => 'player_state',
  ];
  protected int $id;

  public function getUiData()
  {
    $datas = parent::getUiData();
    $scoresheet = $this->scoresheet();
    if (!is_null($scoresheet)) {
      $datas['scoresheet'] = $scoresheet->computeUiData();
      $datas['score'] = $scoresheet->getScore();
    }
    return $datas;
  }

  public function getId(): int
  {
    return $this->id;
  }

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
    $scenarioId = Globals::getScenario();
    if (is_null($this->scoresheet) && $scenarioId != 0) {
      // Scenario 8 has this weird double player thing
      if ($scenarioId == 8) {
        // Even turn, play on "my" sheet
        if (Globals::getTurn() % 2 == 0) {
          $this->scoresheet = new Scoresheet8($this, Players::getPrevOrAstra($this), 1);
        } else {
          $this->scoresheet = new Scoresheet8(Players::getNextOrAstra($this), $this, 2);
        }
      }
      // Otherwise it's just as usual
      else {
        $className = 'Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet' . $scenarioId;
        $this->scoresheet = new $className($this);
      }
    }
    return $this->scoresheet;
  }
  public function refreshScoresheet(): void
  {
    $this->scoresheet = null;
  }
  // USEFUL ONLY FOR ASTRA BECAUSE OF WEIRDNESS
  public function scoresheetForScore(): ?Scoresheet
  {
    return $this->scoresheet();
  }

  public function getCombination()
  {
    $combination = PGlobals::getCombination($this->id);
    if (is_null($combination) || empty($combination)) return null;
    return $combination;
  }
}
