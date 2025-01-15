<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Helpers\UserException;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Models\Planet;


class Scribbles extends \Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces
{
  protected static $table = 'scribbles';
  protected static $prefix = 'scribble_';
  protected static $customFields = ['type', 'type_arg', 'turn'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;

  protected static function cast($meeple)
  {
    return new \Bga\Games\WelcomeToTheMoon\Models\Scribble($meeple);
  }

  public static function getUiData()
  {
    return self::getAll()->toArray();
  }

  // public static function getOfPlayer($player, $type = null)
  // {
  //   return $type
  //     ? static::getAll()
  //     ->where('pId', $player->getId())
  //     ->where('type', $type)
  //     : static::getAll()->where('pId', $player->getId());
  // }


  // public static function add($type, $players)
  // {
  //   $toCreate = [];
  //   foreach ($players as $playerId => $player) {
  //     $toCreate[] = [
  //       'type' => $type,
  //       'location' => 'corporation',
  //       'player_id' => $playerId,
  //     ];
  //   }
  //   return static::create($toCreate);
  // }
}
