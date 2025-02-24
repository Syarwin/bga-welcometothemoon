<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Player;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends \Bga\Games\WelcomeToTheMoon\Helpers\CachedDB_Manager
{
  protected static string $table = 'player';
  protected static string $primary = 'player_id';
  protected static ?Collection $datas = null;

  protected static function cast($row)
  {
    return new \Bga\Games\WelcomeToTheMoon\Models\Player($row);
  }

  public static function setupNewGame($players, $options)
  {
    // Create players
    $gameInfos = Game::get()->getGameinfos();
    $colors = $gameInfos['player_colors'];
    $query = self::DB()->multipleInsert([
      'player_id',
      'player_color',
      'player_canal',
      'player_name',
      'player_avatar',
    ]);
    $playerIndex = 0;
    $values = [];
    foreach ($players as $pId => $player) {
      $color = array_shift($colors);
      $values[] = [
        $pId,
        $color,
        $player['player_canal'],
        $player['player_name'],
        $player['player_avatar'],
      ];
      $playerIndex++;
    }
    $query->values($values);
    self::invalidate();
    Game::get()->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
    Game::get()->reloadPlayersBasicInfos();
  }

  public static function getActiveId()
  {
    return (int) Game::get()->getActivePlayerId();
  }

  public static function getCurrentId($bReturnNullIfNotLogged = false)
  {
    return (int) Game::get()->getCurrentPId($bReturnNullIfNotLogged);
  }

  public static function getActive(): Player
  {
    return self::get(self::getActiveId());
  }

  public static function getCurrent(): Player
  {
    return self::get(self::getCurrentId());
  }

  public static function get($id = null)
  {
    return parent::get($id ?? self::getActiveId());
  }

  public static function getNextId($player)
  {
    $pId = is_int($player) ? $player : $player->getId();
    $table = Game::get()->getNextPlayerTable();
    return $table[$pId];
  }

  public static function getNext($player)
  {
    return self::get(self::getNextId($player));
  }

  /*
   * Return the number of players
   */
  public static function count()
  {
    return self::getAll()->count();
  }

  /*
   * getUiData : get all ui data of all players
   */
  public static function getUiData($pId = null)
  {
    return self::getAll()
      ->map(fn($player) => $player->getUiData($pId))
      ->toAssoc();
  }

  // public static function scores($pId = null, $save = false)
  // {
  //   return self::getAll()
  //     ->map(function ($player) use ($pId, $save) {
  //       return $player->score($pId ?? $player->getId(), $save);
  //     })
  //     ->toAssoc();
  // }

  /*
   * Get current turn order according to first player variable
   */
  public static function getTurnOrder($firstPlayer = null)
  {
    $firstPlayer = $firstPlayer ?? Globals::getFirstPlayer();
    $order = [];
    $p = $firstPlayer;
    do {
      $order[] = $p;
      $p = self::getNextId($p);
    } while ($p != $firstPlayer);
    return $order;
  }


  /*
   * Get Astra
   */
  private static ?Astra $astra;

  public static function getAstra(): ?Astra
  {
    if (!Globals::isSolo()) return null;

    if (!isset(static::$astra)) {
      $className = "Bga\\Games\\WelcomeToTheMoon\\Models\\AstraAdventures\\Astra" . Globals::getScenario();
      static::$astra = new $className();
    }

    return static::$astra;
  }

  public static function getAstraDatas(): array
  {
    $astra = self::getAstra();
    if (is_null($astra)) return [];

    return $astra->getUiData();
  }

  public static function invalidate()
  {
    parent::invalidate();
    static::$astra = null;
  }

  public static function getOrderNumberMostZonesComplete(int $pId): array
  {
    $amount = 0;
    $zones = [];
    /** @var Player $player */
    foreach (self::getAll() as $player) {
      $completeSectionsCount = $player->scoresheet()->getCompleteSectionsCount();
      if ($completeSectionsCount > 0) {
        $zones[$player->getId()] = $completeSectionsCount;
        if ($player->getId() == $pId) {
          $amount = $completeSectionsCount;
        }
      }
    }
    if (Globals::isSolo()) {
      $astra = Players::getAstra();
      $zones['astra'] = intdiv($astra->getCardsByActionMap()[ENERGY], 2);
    }
    if (!isset($zones[$pId])) {
      return [0, 0];
    }
    arsort($zones); // Sorts by value in descending order while keeping keys

    $groupedPlayers = [];
    $previousValue = null;
    foreach ($zones as $key => $value) {
      if ($value !== $previousValue) {
        $groupedPlayers[] = []; // Start a new group for a new value
      }
      $groupedPlayers[array_key_last($groupedPlayers)][] = $key; // Add key to the latest group
      $previousValue = $value;
    }
    foreach ($groupedPlayers as $key => $players) {
      if (in_array($pId, $players)) {
        return [$key + 1, $amount];
      }
    }
    return [0, 0]; // This should never happen as we already returned 0 if pId was filtered having 0 zones
  }
}
