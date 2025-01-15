<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;


class PlanCards extends CachedPieces
{
  protected static $table = 'plan_cards';
  protected static $prefix = 'card_';
  protected static $customFields = [];
  protected static $datas = null;
  protected static $autoremovePrefix = false;

  protected static function cast($row): PlanCard
  {
    return new PlanCard($row);
  }

  public static function getUiData()
  {
    return static::getInLocation('deck');
  }

  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////

  /* Creation of all cards */
  public static function setupNewGame($players, $options)
  {
    $data = [];

    static::create($data);
  }
}
