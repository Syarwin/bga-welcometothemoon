<?php

namespace Bga\Games\WelcomeToTheMoon\Helpers;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;

/**
 * Class that allows to log DB change: useful for undo feature
 *
 * Associated DB table :
 *  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *  `move_id` int(10),
 *  `table` varchar(32) NOT NULL,
 *  `primary` varchar(32) NOT NULL,
 *  `type` varchar(32) NOT NULL,
 *  `affected` JSON,
 */

class Log extends \APP_DbObject
{
  public static function clearCache($invalidateEngine = true)
  {
    Globals::fetch();
    PGlobals::fetch();
    ConstructionCards::invalidate();
    Players::invalidate();
    PlanCards::invalidate();
    Scribbles::invalidate();
    // Stats::invalidate();

    if ($invalidateEngine) {
      Engine::invalidate();
    }
    Notifications::resetCache();
  }

  /**
   * Add an entry
   */
  static $moveId = null;
  public static function addEntry($entry)
  {
    if (isset($entry['affected'])) {
      $entry['affected'] = \json_encode($entry['affected'], JSON_UNESCAPED_SLASHES);
    }
    if (!isset($entry['table'])) {
      $entry['table'] = '';
    }
    if (!isset($entry['primary'])) {
      $entry['primary'] = '';
    }
    if (!isset($entry['player_id'])) {
      $entry['player_id'] = Players::getCurrentId(true) ?? 0;
    }

    if (is_null(static::$moveId)) {
      static::$moveId = self::getUniqueValueFromDB('SELECT global_value FROM global WHERE global_id = 3') ?? 0;
    }
    $entry['move_id'] = static::$moveId;
    $query = new QueryBuilder('log', null, 'id');
    return $query->insert($entry);
  }

  // Create a new checkpoint : anything before that checkpoint cannot be undo (unless in studio)
  public static function checkpoint($pId = 0)
  {
    self::clearUndoableStepNotifications();
    return self::addEntry(['type' => 'checkpoint', 'player_id' => $pId]);
  }

  // Create a new step to allow undo step-by-step
  public static function step()
  {
    return self::addEntry(['type' => 'step']);
  }

  // Log the start of engine to allow "restart turn"
  public static function startEngine()
  {
    if (!Globals::isSolo()) {
      self::checkpoint();
    }

    return self::addEntry(['type' => 'engine']);
  }

  // Find the last checkpoint
  public static function getLastCheckpoint($pId, $includeEngineStarts = false)
  {
    $query = new QueryBuilder('log', null, 'id');
    $query = $query->select(['id']);
    if ($includeEngineStarts) {
      $query = $query->whereIn('type', ['checkpoint', 'engine']);
    } else {
      $query = $query->where('type', 'checkpoint');
    }
    $query = $query->whereIn('player_id', [0, $pId]);

    $log = $query
      ->orderBy('id', 'DESC')
      ->limit(1)
      ->get()
      ->first();

    return is_null($log) ? 1 : $log['id'];
  }

  // Find all the moments available to undo
  public static function getUndoableSteps($pId, $onlyIds = true)
  {
    $checkpoint = self::getLastCheckpoint($pId);
    $query = new QueryBuilder('log', null, 'id');
    $query = $query->select(['id', 'move_id'])->where('type', 'step');
    if ($pId != 'all') {
      $query = $query->whereIn('player_id', [0, $pId]);
    }

    $logs = $query
      ->where('id', '>', $checkpoint)
      ->orderBy('id', 'DESC')
      ->get();
    return $onlyIds ? $logs->getIds() : $logs;
  }

  /**
   * Revert all the way to the last checkpoint or the last start of turn
   */
  public static function undoTurn($pId)
  {
    $checkpoint = static::getLastCheckpoint($pId, true);
    return self::revertTo($pId, $checkpoint);
  }

  /**
   * Revert to a given step (checking first that it exists)
   */
  public static function undoToStep($pId, $stepId)
  {
    $query = new QueryBuilder('log', null, 'id');
    $step = $query
      ->where('id', '=', $stepId)
      ->get()
      ->first();
    if (is_null($step)) {
      throw new \BgaVisibleSystemException('Cant undo here');
    }

    self::revertTo($pId, $stepId - 1);
  }

  /**
   * Revert all the logged changes up to an id
   */
  public static function revertTo($pId, $id)
  {
    $query = new QueryBuilder('log', null, 'id');
    $logs = $query
      ->select(['id', 'table', 'primary', 'type', 'affected', 'move_id'])
      ->where('player_id', $pId)
      ->where('id', '>', $id)
      ->orderBy('id', 'DESC')
      ->get();

    $moveIds = [];
    foreach ($logs as $log) {
      if (in_array($log['type'], ['step', 'engine'])) {
        continue;
      }

      $log['affected'] = str_replace('\\\\', '\\\\\\\\', $log['affected']);
      $log['affected'] = json_decode($log['affected'], true, 512, JSON_UNESCAPED_SLASHES);
      $moveIds[] = intval($log['move_id']);

      foreach ($log['affected'] as $row) {
        $q = new QueryBuilder($log['table'], null, $log['primary']);

        if ($log['type'] != 'create') {
          foreach ($row as $key => $val) {
            if (isset($row[$key])) {
              $val = str_replace('\\', '\\\\', $val);
              $val = str_replace("'", "\\'", \stripcslashes($val));
              $row[$key] = $val;
            }
          }
        }

        // UNDO UPDATE -> NEW UPDATE
        if ($log['type'] == 'update') {
          $q->update($row)->run($row[$log['primary']]);
        }
        // UNDO DELETE -> CREATE
        elseif ($log['type'] == 'delete') {
          $q->insert($row);
        }
        // UNDO CREATE -> DELETE
        elseif ($log['type'] == 'create') {
          $q->delete()->run($row);
        }
      }
    }

    // Clear logs
    $query = new QueryBuilder('log', null, 'id');
    $query
      ->where('id', '>', $id)
      ->where('player_id', $pId)
      ->delete()
      ->run();

    // Cancel the game notifications
    $query = new QueryBuilder('gamelog', null, 'gamelog_packet_id');
    if (!empty($moveIds)) {
      $notifications = $query
        ->whereIn('gamelog_move_id', $moveIds)
        ->get();
      $notifIds = self::extractNotifIds($notifications);
      Notifications::clearTurn(Players::get($pId), $notifIds);
    }

    // Force to clear cached informations
    self::clearCache();

    // Notify
    $datas = Game::get()->getAllDatas();
    Notifications::refreshUI($pId, $datas);

    // Force notif flush to be able to delete "restart turn" notif
    Game::get()->sendNotifications();
    if (!empty($moveIds)) {
      // Delete notifications
      $query = new QueryBuilder('gamelog', null, 'gamelog_packet_id');
      $query
        ->delete()
        ->where('gamelog_player', $pId)
        ->where('gamelog_move_id', '>=', min($moveIds), true)
        ->run();
    }

    return $moveIds;
  }

  /**
   * getCancelMoveIds : get all cancelled notifs IDs from BGA gamelog, used for styling the notifications on page reload
   */
  protected static function extractNotifIds($notifications)
  {
    $notificationUIds = [];
    foreach ($notifications as $packet) {
      $data = \json_decode($packet['gamelog_notification'], true);
      foreach ($data as $notification) {
        array_push($notificationUIds, $notification['uid']);
      }
    }
    return $notificationUIds;
  }


  /**
   * clearUndoableStepNotifications : extract and remove all notifications of type 'newUndoableStep' in the gamelog
   */
  public static function clearUndoableStepNotifications($clearAll = false)
  {
    // Get move ids corresponding to last step
    if ($clearAll) {
      $minMoveId = 1;
    } else {
      $moveIds = [];
      foreach (self::getUndoableSteps('all', false) as $step) {
        $moveIds[] = (int) $step['move_id'];
      }
      if (empty($moveIds)) {
        return;
      }
      $minMoveId = min($moveIds);
    }

    // Get packets
    $query = new QueryBuilder('gamelog', null, 'gamelog_packet_id');
    $packets = $query->where('gamelog_move_id', '>=', $minMoveId)->get();
    foreach ($packets as $packet) {
      $id = $packet['gamelog_packet_id'];

      // Filter notifs based on type
      $data = \json_decode($packet['gamelog_notification'], true);
      $notifs = [];
      $ignored = 0;
      foreach ($data as $notification) {
        if ($notification['type'] != 'newUndoableStep') {
          $notifs[] = $notification;
        } else {
          $ignored++;
        }
      }
      if ($ignored == 0) {
        continue;
      }

      $query = new QueryBuilder('gamelog', null, 'gamelog_packet_id');

      // Delete or update
      if (empty($notifs)) {
        $query->delete($id);
      } else {
        $query->update(
          [
            'gamelog_notification' => addslashes(json_encode($notifs)),
          ],
          $id
        );
      }
    }
  }
}
