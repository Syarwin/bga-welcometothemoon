<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

class Scribbles extends \Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces
{
  protected static string $table = 'scribbles';
  protected static string $prefix = 'scribble_';
  protected static array $customFields = ['type', 'type_arg', 'turn'];
  protected static ?Collection $datas = null;
  protected static bool $autoremovePrefix = false;
  protected static bool $autoIncrement = false;

  protected static function cast($meeple)
  {
    return new Scribble($meeple);
  }

  public static function getUiData()
  {
    return self::getAll()->order(function ($a, $b) {
      return $a->getTurn() > $b->getTurn();
    })->toArray();
  }

  public static function getOfPlayer(Player|int $player, $type = null)
  {
    $pId = is_int($player) ? $player : $player->getId();
    $datas = static::getAll()->filter(fn($scribble) => $scribble->getPId() == $pId);

    if (!is_null($type)) {
      $datas = $datas->where('type', $type);
    }

    return $datas;
  }

  public static function getMaxIndexOfPlayer(Player|int $player)
  {
    $maxId = 0;
    foreach (self::getOfPlayer($player) as $scribble) {
      $maxId = max($maxId, $scribble->getIndex());
    }
    return $maxId;
  }

  public static function add(Player|int $player, array $info): Scribble
  {
    $pId = is_int($player) ? $player : $player->getId();
    $index = self::getMaxIndexOfPlayer($pId) + 1;
    $info['id'] = "$pId-$index";
    $info['turn'] = Stats::getTurns();
    return static::singleCreate($info);
  }
}
