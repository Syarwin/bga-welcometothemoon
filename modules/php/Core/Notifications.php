<?php

namespace Bga\Games\WelcomeToTheMoon\Core;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Cards;
use Bga\Games\WelcomeToTheMoon\Managers\Meeples;
use Bga\Games\WelcomeToTheMoon\Managers\Tiles;

class Notifications
{
  public static function setupScenario($scenario)
  {
    self::notifyAll('message', clienttranslate('Starting scenario ${n}: ${name}'), [
      'n' => $scenario,
      'name' => 'TODO',
      'i18n' => ['name'],
    ]);
  }

  public static function newTurn(int $turn, array $cards)
  {
    self::notifyAll('newTurn', clienttranslate('Turn ${turn}'), [
      'turn' => $turn,
      'cards' => $cards,
    ]);
  }


  public static function chooseCards($player)
  {
    // $combination = $player->getCombination();
    // $names = [
    //   SURVEYOR => clienttranslate('Surveyor'),
    //   ESTATE => clienttranslate('Real Estate Agent'),
    //   PARK => clienttranslate('Landscaper'),
    //   POOL => clienttranslate('Pool Manufacturer'),
    //   TEMP => clienttranslate('Temp agency'),
    //   BIS => clienttranslate('Bis'),
    // ];
    static::pnotify($player, 'chooseCards', clienttranslate('${player_name} chooses the combination : ${number} & ${action}.'), [
      'player' => $player,
      'i18n' => ['action'],
      'action' => "TODO",
      'number' => "TODO",
      // 'action' => $names[$combination['action']],
      // 'number' => $combination['number'],
    ]);
  }

  public static function writeNumber($player, $number, $scribble)
  {
    static::pnotify($player, 'addScribble', clienttranslate('${player_name} writes ${number} on his scoresheet'), [
      'player' => $player,
      'number' => $number,
      'scribble' => $scribble,
    ]);
  }

  /*************************
   **** GENERIC METHODS ****
   *************************/
  protected static function notifyAll($name, $msg, $data)
  {
    self::updateArgs($data, true);
    Game::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($player, $name, $msg, $data)
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::updateArgs($data);
    Game::get()->notifyPlayer($pId, $name, $msg, $data);
  }

  protected static function pnotify($player, $name, $msg, $data)
  {
    $mode = Globals::getMode();
    $pId = is_int($player) ? $player : $player->getId();
    $data['player'] = $player;
    self::updateArgs($data, $mode == \MODE_APPLY);

    // PRIVATE MODE => send private notif
    if ($mode == MODE_PRIVATE) {
      Game::get()->notifyPlayer($pId, $name, $msg, $data);
      self::flush();
    }
    // PUBLIC MODE => send public notif with ignore flag
    elseif ($mode == \MODE_APPLY && ($data['public'] ?? true)) {
      $data['ignore'] = $pId;
      $data['preserve'][] = 'ignore';
      Game::get()->notifyAllPlayers($name, $msg, $data);
    }
  }

  public static function message($txt, $args = [])
  {
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = [])
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::notify($pId, 'message', $txt, $args);
  }

  public static function newUndoableStep($player, $stepId)
  {
    self::notify($player, 'newUndoableStep', clienttranslate('Undo here'), [
      'stepId' => $stepId,
      'preserve' => ['stepId'],
    ]);
  }

  public static function clearTurn($player, $notifIds)
  {
    self::notify($player, 'clearTurn', clienttranslate('You restart your turn'), [
      'player' => $player,
      'notifIds' => $notifIds,
    ]);
  }

  public static function refreshUI($pId, $datas)
  {
    // // Keep only the thing that matters
    $fDatas = [
      'players' => $datas['players'],
      'scribbles' => $datas['scribbles'],
    ];

    self::notify($pId, 'refreshUI', '', [
      'datas' => $fDatas,
    ]);
  }

  public static function flush()
  {
    self::notifyAll('flush', '', []);
  }

  public static function scores()
  {
    // TODO
  }

  ///////////////////////////////////////////////////////////////
  //  _   _           _       _            _
  // | | | |_ __   __| | __ _| |_ ___     / \   _ __ __ _ ___
  // | | | | '_ \ / _` |/ _` | __/ _ \   / _ \ | '__/ _` / __|
  // | |_| | |_) | (_| | (_| | ||  __/  / ___ \| | | (_| \__ \
  //  \___/| .__/ \__,_|\__,_|\__\___| /_/   \_\_|  \__, |___/
  //       |_|                                      |___/
  ///////////////////////////////////////////////////////////////

  /*
   * Automatically adds some standard field about player and/or card
   */
  protected static function updateArgs(&$data, $public = false)
  {
    if (isset($data['player'])) {
      $data['player_name'] = $data['player']->getName();
      $data['player_id'] = $data['player']->getId();
      // if (!$public) {
      //   $data['scores'] = Players::scores($data['player']->getId(), false);
      // }
      unset($data['player']);
    }
    if (isset($data['player2'])) {
      $data['player_name2'] = $data['player2']->getName();
      $data['player_id2'] = $data['player2']->getId();
      unset($data['player2']);
    }
    if (isset($data['player3'])) {
      $data['player_name3'] = $data['player3']->getName();
      $data['player_id3'] = $data['player3']->getId();
      unset($data['player3']);
    }
    if (isset($data['players'])) {
      $args = [];
      $logs = [];
      foreach ($data['players'] as $i => $player) {
        $logs[] = '${player_name' . $i . '}';
        $args['player_name' . $i] = $player->getName();
      }
      $data['players_names'] = [
        'log' => join(', ', $logs),
        'args' => $args,
      ];
      $data['i18n'][] = 'players_names';
      unset($data['players']);
    }
  }
}
