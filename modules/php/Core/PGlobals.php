<?php

namespace Bga\Games\WelcomeToTheMoon\Core;

use Bga\Games\WelcomeToTheMoon\Game;

/*
 * PGlobals: private globals => reduce potential deadlock
 */

class PGlobals extends \Bga\Games\WelcomeToTheMoon\Helpers\DB_Manager
{
  protected static bool $initialized = false;
  protected static array $variables = [
    'state' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'engine' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'engineChoices' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'flags' => 'obj', // Useful for flagging a "once per turn action" as flagged,

    'combination' => 'obj',
    'validatedPlans' => 'obj',
  ];

  protected static string $table = 'pglobal_variables';
  protected static string $primary = 'name'; // Name is actually name-pId
  protected static function cast($row)
  {
    list($name, $pId) = explode('-', $row['name']);
    if (!isset(self::$variables[$name])) {
      return null;
    }
    $val = stripslashes(str_replace('\\\\', '\\\\\\\\', $row['value']));
    $val = json_decode($val, true, 512, JSON_UNESCAPED_SLASHES);

    return self::$variables[$name] == 'int' ? ((int) $val) : $val;
  }

  /*
   * Fetch all existings variables from DB
   */
  protected static $datas = [];
  public static function fetch()
  {
    // Turn of LOG to avoid infinite loop (Globals::isLogging() calling itself for fetching)
    $tmp = self::$log;
    self::$log = false;

    foreach (
      self::DB()
        ->select(['value', 'name'])
        ->get(false)
      as $uid => $variable
    ) {
      list($name, $pId) = explode('-', $uid);

      if (\array_key_exists($name, self::$variables)) {
        self::$datas[$pId][$name] = $variable;
      }
    }
    self::$initialized = true;
    self::$log = $tmp;
  }

  /*
   * Create and store a global variable declared in this file but not present in DB yet
   *  (only happens when adding globals while a game is running)
   */
  public static function create($name, $pId)
  {
    if (!\array_key_exists($name, self::$variables)) {
      return;
    }

    $default = [
      'int' => 0,
      'obj' => [],
      'bool' => false,
      'str' => '',
    ];
    $val = $default[self::$variables[$name]];
    self::DB()->insert(
      [
        'name' => $name . '-' . $pId,
        'value' => \json_encode($val),
      ],
      true
    );
    self::$datas[$pId][$name] = $val;
  }

  /**
   * get all the variables of a given name
   */
  public static function getAll($name)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    $t = [];
    foreach (self::$datas as $pId => $data) {
      if (isset($data[$name])) {
        $t[$pId] = $data[$name];
      }
    }
    return $t;
  }

  /*
   * Magic method that intercept not defined static method and do the appropriate stuff
   */
  public static function __callStatic($method, $args)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    // First argument is always pId
    $pId = $args[0];

    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = mb_strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, self::$variables)) {
        throw new \InvalidArgumentException("Property {$name} doesn't exist");
      }

      // Create in DB if don't exist yet
      if (!\array_key_exists($name, self::$datas[$pId] ?? [])) {
        self::create($name, $pId);
      }

      if ($match[1] == 'get') {
        // Basic getters
        return self::$datas[$pId][$name];
      } elseif ($match[1] == 'is') {
        // Boolean getter
        if (self::$variables[$name] != 'bool') {
          throw new \InvalidArgumentException("Property {$name} is not of type bool");
        }
        return (bool) self::$datas[$pId][$name];
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        $value = $args[1];
        if (self::$variables[$name] == 'int') {
          $value = (int) $value;
        }
        if (self::$variables[$name] == 'bool') {
          $value = (bool) $value;
        }

        self::$datas[$pId][$name] = $value;
        if (Globals::getMode() == MODE_APPLY || in_array($name, ['state', 'engine', 'engineChoices'])) {
          self::DB()->update(['value' => \addslashes(\json_encode($value, JSON_UNESCAPED_SLASHES))], $name . '-' . $pId);
        }

        return $value;
      } elseif ($match[1] == 'inc') {
        if (self::$variables[$name] != 'int') {
          throw new \InvalidArgumentException("Trying to increase {$name} which is not an int");
        }

        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        return self::$setter($args[0], self::$getter($args[0]) + ($args[1] ?? 1));
      }
    }
    debug_print_backtrace();
    throw new \feException(print_r("ERROR"));
    return null;
  }

  /*
   * Setup new game
   */
  public static function setupNewGame($players, $options) {}
}
