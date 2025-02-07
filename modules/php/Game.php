<?php

/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * WelcomeToTheMoon implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 */

declare(strict_types=1);

namespace Bga\Games\WelcomeToTheMoon;

require_once(APP_GAMEMODULE_PATH . "module/table/table.game.php");

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\States\EngineTrait;
use Bga\Games\WelcomeToTheMoon\States\SetupTrait;
use Bga\Games\WelcomeToTheMoon\DebugTrait;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\PlanCards;
use Bga\Games\WelcomeToTheMoon\States\TurnTrait;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Managers\Scribbles;
use Bga\Games\WelcomeToTheMoon\States\EndGameTrait;

class Game extends \Table
{
    use SetupTrait;
    use DebugTrait;
    use TurnTrait;
    use EngineTrait;
    use EndGameTrait;

    public static $instance = null;
    function __construct()
    {
        parent::__construct();
        self::$instance = $this;
        $this->bSelectGlobalsForUpdate = true;
        self::initGameStateLabels([
            'mode' => 10, // DO NOT TOUCH, USED FOR SIMULATING DB MODIFICATION
        ]);

        // Stats::checkExistence();
        // Notifications::resetCache();
    }

    public static function get()
    {
        return self::$instance;
    }

    protected function initTable(): void
    {
        Engine::boot();
    }


    protected function getGameName()
    {
        return "welcometothemoon";
    }


    /*
     * Gather all information about current game situation (visible by the current player).
     *
     * The method is called each time the game interface is displayed to a player, i.e.:
     *
     * - when the game starts
     * - when a player refreshes the game page (F5)
     */
    public function getAllDatas()
    {
        $currentPId = (int) $this->getCurrentPlayerId();

        $datas =  [
            'players' => Players::getUiData($currentPId),
            'constructionCards' => ConstructionCards::getUiData(),
            'planCards' => PlanCards::getUiData(),
            'scribbles' => Scribbles::getUiData(),

            'standard' => Globals::isStandard(),
            'scenario' => Globals::getScenario(),
            'turn' => Globals::getTurn(),
        ];

        return $datas;
    }


    /**
     * Compute and return the current game progression.
     *
     * The number returned must be an integer between 0 and 100.
     *
     * This method is called each time we are in a game state with the "updateGameProgression" property set to true.
     *
     * @return int
     * @see ./states.inc.php
     */
    public function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ////////////   Custom Turn Order   ////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    public function initCustomTurnOrder($key, $order, $callback, $endCallback, $loop = false, $autoNext = true, $args = [])
    {
        $turnOrders = Globals::getCustomTurnOrders();
        $turnOrders[$key] = [
            'order' => $order ?? Players::getTurnOrder(),
            'index' => -1,
            'callback' => $callback,
            'args' => $args, // Useful mostly for auto card listeners
            'endCallback' => $endCallback,
            'loop' => $loop,
        ];
        Globals::setCustomTurnOrders($turnOrders);

        if ($autoNext) {
            $this->nextPlayerCustomOrder($key);
        }
    }

    public function initCustomDefaultTurnOrder($key, $callback, $endCallback, $loop = false, $autoNext = true)
    {
        $this->initCustomTurnOrder($key, null, $callback, $endCallback, $loop, $autoNext);
    }

    public function nextPlayerCustomOrder($key)
    {
        $turnOrders = Globals::getCustomTurnOrders();
        if (!isset($turnOrders[$key])) {
            throw new \BgaVisibleSystemException('Asking for the next player of a custom turn order not initialized : ' . $key);
        }

        // Increase index and save
        $o = $turnOrders[$key];
        $i = $o['index'] + 1;
        if ($i == count($o['order']) && $o['loop']) {
            $i = 0;
        }
        $turnOrders[$key]['index'] = $i;
        Globals::setCustomTurnOrders($turnOrders);

        if ($i < count($o['order'])) {
            $this->gamestate->jumpToState(ST_GENERIC_NEXT_PLAYER);
            $this->gamestate->changeActivePlayer($o['order'][$i]);
            $this->jumpToOrCall($o['callback'], $o['args']);
        } else {
            $this->endCustomOrder($key);
        }
    }

    public function endCustomOrder($key)
    {
        $turnOrders = Globals::getCustomTurnOrders();
        if (!isset($turnOrders[$key])) {
            throw new \BgaVisibleSystemException('Asking for ending a custom turn order not initialized : ' . $key);
        }

        $o = $turnOrders[$key];
        $turnOrders[$key]['index'] = count($o['order']);
        Globals::setCustomTurnOrders($turnOrders);
        $callback = $o['endCallback'];
        $this->jumpToOrCall($callback);
    }

    public function jumpToOrCall($mixed, $args = [])
    {
        if (is_int($mixed) && array_key_exists($mixed, $this->gamestate->states)) {
            $this->gamestate->jumpToState($mixed);
        } elseif (method_exists($this, $mixed)) {
            $method = $mixed;
            $this->$method($args);
        } else {
            throw new \BgaVisibleSystemException('Failing to jumpToOrCall  : ' . $mixed);
        }
    }


    ////////////////////////////////////
    ////////////   Zombie   ////////////
    ////////////////////////////////////
    /*
   * zombieTurn:
   *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
   *   You can do whatever you want in order to make sure the turn of this player ends appropriately
   */
    public function zombieTurn($state, $activePlayer): void
    {
        die("TODO: zombie mode");

        $stateName = $state['name'];
        if ($state['type'] == 'activeplayer') {
        } elseif ($state['type'] == 'multipleactiveplayer') {
            if ($stateName == 'breakDiscard') {
            }
            // Make sure player is in a non blocking status for role turn
            else {
                $this->gamestate->setPlayerNonMultiactive($activePlayer, 'zombiePass');
            }
        }
    }

    /////////////////////////////////////
    //////////   DB upgrade   ///////////
    /////////////////////////////////////
    // You don't have to care about this until your game has been published on BGA.
    // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
    // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
    //   update the game database and allow the game to continue to run with your new version.
    /////////////////////////////////////
    /*
   * upgradeTableDb
   *  - int $from_version : current version of this game database, in numerical form.
   *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
   */
    public function upgradeTableDb($from_version)
    {
        // if ($from_version <= 2107011810) {
        //   $sql = 'ALTER TABLE `DBPREFIX_player` ADD `new_score` INT(10) NOT NULL DEFAULT 0';
        //   self::applyDbUpgradeToAllDB($sql);
        // }
    }

    /////////////////////////////////////////////////////////////
    // Exposing protected methods, please use at your own risk //
    /////////////////////////////////////////////////////////////

    // Exposing protected method getCurrentPlayerId
    public function getCurrentPId($bReturnNullIfNotLogged = false)
    {
        return self::get()->getCurrentPlayerId($bReturnNullIfNotLogged);
    }

    // Exposing protected method translation
    public static function translate($text)
    {
        return self::get()->_($text);
    }
}
