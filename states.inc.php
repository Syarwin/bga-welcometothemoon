<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * Planet Unknown game states description
 *
 */

$machinestates = [
  // The initial state. Please do not modify.
  ST_GAME_SETUP => [
    'name' => 'gameSetup',
    'description' => '',
    'type' => 'manager',
    'action' => 'stGameSetup',
    'transitions' => ['' => ST_SETUP_BRANCH],
  ],


  //////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  //////////////////////////////////
  ST_SETUP_BRANCH => [
    'name' => 'setupBranch',
    'description' => '',
    'type' => 'game',
    'action' => 'stSetupBranch',
  ],

  // Used to load game when it's not launchable
  ST_SETUP_DEBUG => [
    'name' => 'test',
    'type' => ACTIVE_PLAYER,
    'description' => "foo",
    'descriptionmyturn' => "foo",
  ],

  ST_SETUP_SCENARIO => [
    'name' => 'setupScenario',
    'type' => GAME,
    'description' => '',
    'action' => 'stSetupScenario',
  ],

  ////////////////////////////////////////////////////////////////////
  //  ____  _             _            __   _____
  // / ___|| |_ __ _ _ __| |_    ___  / _| |_   _|   _ _ __ _ __
  // \___ \| __/ _` | '__| __|  / _ \| |_    | || | | | '__| '_ \
  //  ___) | || (_| | |  | |_  | (_) |  _|   | || |_| | |  | | | |
  // |____/ \__\__,_|_|   \__|  \___/|_|     |_| \__,_|_|  |_| |_|
  ////////////////////////////////////////////////////////////////////

  ST_START_TURN => [
    'name' => 'startTurn',
    'description' => '',
    'type' => 'game',
    'action' => 'stStartTurn',
  ],

  //////////////////////////
  // LAUNCH ENGINE
  /////////////////////////
  ST_START_TURN_ENGINE => [
    'name' => 'startTurnEngine',
    'type' => 'game',
    'action' => 'stStartTurnEngine',
  ],

  ////////////////////////////////////////////////////////////
  //  _____           _          __   _____
  // | ____|_ __   __| |   ___  / _| |_   _|   _ _ __ _ __
  // |  _| | '_ \ / _` |  / _ \| |_    | || | | | '__| '_ \
  // | |___| | | | (_| | | (_) |  _|   | || |_| | |  | | | |
  // |_____|_| |_|\__,_|  \___/|_|     |_| \__,_|_|  |_| |_|
  ////////////////////////////////////////////////////////////

  ST_END_TURN => [
    'name' => 'endTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stEndTurn',
    'updateGameProgression' => true,
  ],

  ////////////////////////////////////
  //  _____             _
  // | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__, |_|_| |_|\___|
  //              |___/
  ////////////////////////////////////
  ST_GENERIC_NEXT_PLAYER => [
    'name' => 'genericNextPlayer',
    'type' => 'game',
  ],

  ST_SETUP_PRIVATE_ENGINE => [
    'name' => 'setupEngine',
    'type' => 'multipleactiveplayer',
    'description' => clienttranslate('Waiting for everyone to confirm their turn'),
    'descriptionmyturn' => '',
    'args' => 'argsSetupEngine',
    'initialprivate' => ST_INIT_PRIVATE_ENGINE,
    'possibleactions' => ['actCancel'],
    'transitions' => ['done' => ST_APPLY_ENGINE],
  ],

  ST_APPLY_ENGINE => [
    'name' => 'applyEngine',
    'type' => 'game',
    'action' => 'stApplyEngine',
  ],

  ST_INIT_PRIVATE_ENGINE => [
    'name' => 'initPrivateEngine',
    'action' => 'stInitPrivateEngine',
    'descriptionmyturn' => '',
    'args' => 'test',
    'type' => 'private',
  ],

  ST_RESOLVE_STACK => [
    'name' => 'resolveStack',
    'type' => 'game',
    'action' => 'stResolveStack',
    'transitions' => [],
  ],

  ST_CONFIRM_TURN => [
    'name' => 'confirmTurn',
    'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
    'type' => 'private',
    'args' => 'argsConfirmTurn',
    'action' => 'stConfirmTurn',
    'possibleactions' => ['actConfirmTurn', 'actRestart'],
  ],

  ST_RESOLVE_CHOICE => [
    'name' => 'resolveChoice',
    'description' => clienttranslate('${actplayer} must choose which effect to resolve'),
    'descriptionmyturn' => clienttranslate('${you} must choose which effect to resolve'),
    'descriptionxor' => clienttranslate('${actplayer} must choose exactly one effect'),
    'descriptionmyturnxor' => clienttranslate('${you} must choose exactly one effect'),
    'type' => 'private',
    'args' => 'argsResolveChoice',
    'action' => 'stResolveChoice',
    'possibleactions' => ['actChooseAction', 'actRestart'],
  ],

  ST_IMPOSSIBLE_MANDATORY_ACTION => [
    'name' => 'impossibleAction',
    'description' => clienttranslate('${actplayer} can\'t take the mandatory action and must restart his turn'),
    'descriptionmyturn' => clienttranslate(
      '${you} can\'t take the mandatory action. Restart your turn'
    ),
    'type' => 'private',
    'args' => 'argsImpossibleAction',
    'possibleactions' => ['actRestart'],
  ],

  ////////////////////////////////////////////////////////////////////////////
  //     _   _                  _         _        _   _
  //    / \ | |_ ___  _ __ ___ (_) ___   / \   ___| |_(_) ___  _ __  ___
  //   / _ \| __/ _ \| '_ ` _ \| |/ __| / _ \ / __| __| |/ _ \| '_ \/ __|
  //  / ___ \ || (_) | | | | | | | (__ / ___ \ (__| |_| | (_) | | | \__ \
  // /_/   \_\__\___/|_| |_| |_|_|\___/_/   \_\___|\__|_|\___/|_| |_|___/
  //
  ////////////////////////////////////////////////////////////////////////////

  ST_CHOOSE_CARDS => [
    'name' => 'chooseCards',
    'descriptionmyturn' => clienttranslate('${you} must pick a pair of construction cards'),
    'descriptionmyturnimpossible' => clienttranslate('${you} can\'t write any number. You must cross off one System Error box.'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actChooseCards', 'actRefusal', 'actRestart'],
  ],

  ST_WRITE_NUMBER => [
    'name' => 'writeNumber',
    'descriptionmyturn' => clienttranslate('${you} must write the number on your scoresheet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actWriteNumber', 'actRefusal', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////////////
  //  _____           _    ___   __    ____
  // | ____|_ __   __| |  / _ \ / _|  / ___| __ _ _ __ ___   ___
  // |  _| | '_ \ / _` | | | | | |_  | |  _ / _` | '_ ` _ \ / _ \
  // | |___| | | | (_| | | |_| |  _| | |_| | (_| | | | | | |  __/
  // |_____|_| |_|\__,_|  \___/|_|    \____|\__,_|_| |_| |_|\___|
  //////////////////////////////////////////////////////////////////

  ST_PRE_END_GAME_TURN => [
    'name' => 'preEndGameTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stPreEndGameTurn', //reveal civ cards
    'transitions' => [
      '' => ST_END_GAME_TURN,
    ],
  ],

  ST_END_GAME_TURN => [
    'name' => 'endGameTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stEndGameTurn',
  ],

  ST_PRE_END_OF_GAME => [
    'name' => 'preEndOfGame',
    'description' => '',
    'type' => GAME,
    'action' => 'stPreEndOfGame',
    'transitions' => ['' => ST_END_GAME],
  ],

  // Final state.
  // Please do not modify (and do not overload action/args methods).
  ST_END_GAME => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd',
  ],
];
