<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Models\Astra;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use BgaUserException;

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

  public static $nextTable = [];
  public static $prevTable = [];
  public static function initTables(): void
  {
    if (!empty(static::$nextTable)) return;

    $pIds = Players::getAll()->getIds();
    for ($i = 0; $i < count($pIds); $i++) {
      static::$nextTable[$pIds[$i]] = $pIds[($i + 1) % count($pIds)];
      static::$prevTable[$pIds[$i]] = $pIds[($i - 1 + count($pIds)) % count($pIds)];
    }
  }


  public static function getNextId(Player|int $player): int
  {
    $pId = is_int($player) ? $player : $player->getId();
    static::initTables();
    return static::$nextTable[$pId];
  }

  public static function getNext(Player $player): Player
  {
    return self::get(self::getNextId($player));
  }

  public static function getNextOrAstra(Player $player): Player|Astra
  {
    return Globals::isSolo() ? self::getAstra() : self::getNext($player);
  }

  public static function getPrevId(Player|int $player): int
  {
    $pId = is_int($player) ? $player : $player->getId();
    static::initTables();
    return static::$prevTable[$pId];
  }

  public static function getPrev(Player $player): Player
  {
    return self::get(self::getPrevId($player));
  }

  public static function getPrevOrAstra(Player $player): Player|Astra
  {
    return Globals::isSolo() ? self::getAstra() : self::getPrev($player);
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
  public static function getSolo(): Player
  {
    if (!Globals::isSolo()) {
      throw new \BgaUserException('Call to getSolo in a non solo game');
    }

    return self::getAll()->first();
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
}
