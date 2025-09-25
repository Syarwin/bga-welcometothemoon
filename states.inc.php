<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Welcome To The Moon implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * WTTM game states description
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
    'updateGameProgression' => true,
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
    'descriptionaccomplishMission' => clienttranslate('Waiting for some players to accomplish mission(s) or pass'),
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

  ST_GENERIC_AUTOMATIC => [
    'name' => "genericAutomatic",
    'descriptionmyturn' => "",
    'type' => "private",
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction'
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
    'descriptionmyturnsolo' => clienttranslate('${you} must first pick a card for the number'),
    'descriptionmyturnimpossible' => clienttranslate('<SYSTEM-ERROR> ${you} can\'t write any number. You must cross off one System Error box <SYSTEM-ERROR>'),
    'descriptionmyturnimpossible1' => clienttranslate('<SYSTEM-ERROR> ${you} can\'t write any number. You must circle one System Error box <SYSTEM-ERROR>'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseCards', 'actSystemError', 'actRestart'],
  ],

  ST_WRITE_NUMBER => [
    'name' => 'writeNumber',
    'descriptionmyturn' => clienttranslate('${you} must write the number on your scoresheet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actWriteNumber', 'actSystemError', 'actRestart'],
  ],

  ST_ACCOMPLISH_MISSION => [
    'name' => 'accomplishMission',
    'descriptionmyturn' => clienttranslate('${you} may select a mission to accomplish'),
    'type' => 'private',
    'action' => 'stAtomicAction',
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actAccomplishMission', 'actPassOptionalAction', 'actRestart'],
  ],

  ST_GIVE_CARD_TO_ASTRA => [
    'name' => 'giveCardAstra',
    'descriptionmyturn' => clienttranslate('${you} must choose which card to give to Astra'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actGiveCardToAstra', 'actUseSoloBonus', 'actRestart'],
  ],

  ST_WRITE_X => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must write an X on your scoresheet'),
    'descriptionmyturnskippable' => clienttranslate('${you} may write an X on your scoresheet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actWriteX', 'actRestart', 'actPassOptionalAction'],
  ],

  //////////////////////////////////////////////////
  //  ____                            _         _ 
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   / |
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | |
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | | |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |_|
  //////////////////////////////////////////////////                                   

  ST_ROCKET_ACTIVATION => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must activate an inactive Rocket quarter bonus on your scoresheet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actRocketActivation', 'actRestart'],
  ],

  ST_CROSS_OFF_SABOTAGE => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off one available Sabotage effect'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffSabotage', 'actRestart'],
  ],

  //////////////////////////////////////////////////////
  //  ____                            _         ____  
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   |___ \ 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \    __) |
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  / __/ 
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |_____|
  //////////////////////////////////////////////////////

  ST_PLACE_ENERGY_WALL => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must divide a zone on your trajectory'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceEnergyWall', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_PROGRAM_ROBOT => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must program a robot sent towards any station'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actProgramRobot', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CIRCLE_PLANT => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} may circle a plant at a station'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCirclePlant', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CIRCLE_SINGLE_LINKED => [
    'name' => 'circleSingleLinked',
    // In case we forgot to send descSuffix which shouldn't happen
    'descriptionmyturn' => clienttranslate('${you} may choose to circle an attached item'),
    'descriptionmyturnA2Water' => clienttranslate('${you} may choose to stir the attached water tank'),
    'descriptionmyturnA3Water' => clienttranslate('${you} may choose to circle the attached water tank'),
    'descriptionmyturnA4Water' => clienttranslate('${you} may choose to circle the attached mineral'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCircleSingleLinked', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CROSS_OFF_MULTIPLIER => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a multiplier'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffMultiplier', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////
  //  ____                            _         _____ 
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   |___ / 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \    |_ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  ___) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |____/ 
  //////////////////////////////////////////////////////////

  ST_IMPROVE_BONUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a scoring bonus for plants, water or antennas'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actImproveBonus', 'actRestart'],
  ],

  ST_BUILD_ROBOT_TUNNEL => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must build a pressurized tunnel'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actBuildRobotTunnel', 'actRestart'],
  ],

  ST_CROSS_OFF_FILLED_QUARTER_BONUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a filled quarter bonus'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffFilledQuarterBonus', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////
  //  ____                            _         _  _
  //   ___|  ___ ___ _ __   __ _ _ __(_) ___   | || |
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | || |_
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | |__   _|
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/     |_|
  //////////////////////////////////////////////////////////

  ST_S4_CIRCLE_PLANT_OR_WATER => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} may choose to circle an attached item'),
    'descriptionmyturnwater' => clienttranslate('${you} may choose to circle an attached water'),
    'descriptionmyturnplant' => clienttranslate('${you} may choose to circle an attached plant'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCirclePlantOrWater', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_S4_FACTORY_UPGRADE => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} may cross off a factory upgrade'),
    'descriptionmyturnenergy' => clienttranslate('${you} may cross off an Energy symbol at any factory'),
    'descriptionmyturnrobot' => clienttranslate('${you} may cross off a Robot symbol at any factory'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actFactoryUpgrade', 'actRestart', 'actPassOptionalAction'],
  ],


  ST_S4_CROSS_OFF_FACTORY_BONUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a filling bonus of a main factory'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffFactoryBonus', 'actRestart'],
  ],


  ////////////////////////////////////////////////////////
  //  ____                            _         ____  
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   | ___| 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  |___ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  ___) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |____/ 
  ////////////////////////////////////////////////////////

  ST_S5_SPLIT_DOME => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must choose a dome section to split'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actSplitDome', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_S5_BUILD_DOME => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must choose a dome section to build'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actBuildDome', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_S5_ENERGY_UPGRADE => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must choose field to upgrade'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actEnergyUpgrade', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_S5_CROSS_OFF_SKYSCRAPER_BONUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a skyscraper bonus'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffSkyscraperBonus', 'actRestart'],
  ],


  ///////////////////////////////////////////////////////////
  //  ____                            _          __   
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___    / /_  
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | '_ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | | (_) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/   \___/ 
  ///////////////////////////////////////////////////////////

  ST_S6_INIT_GREY_VIRUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must choose a quarter for the grey virus and its first infection'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actInitGreyVirus', 'actRestart'],
  ],

  ST_S6_CIRCLE_ENERGY => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must circle an energy in a non-complete quarter'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCircleEnergy', 'actRestart'],
  ],

  ST_S6_CLOSE_WALKWAY => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must close a walkway'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCloseWalkway', 'actRestart'],
  ],

  ST_S6_PROPAGATE_VIRUS => [
    'name' => 'propagateVirus',
    'descriptionmyturn' => clienttranslate('${you} must propagate ${virus_name}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPropagateVirus', 'actRestart'],
  ],

  ST_S6_CROSS_OFF_PROPAGATION_SYMBOL => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a Propagation symbol'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffPropagationSymbol', 'actRestart'],
  ],

  ST_S6_CROSS_OFF_VIRUS => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off a Blue or a Green virus icon'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffVirus', 'actRestart'],
  ],

  /////////////////////////////////////////////////////////
  //  ____                            _         _____ 
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   |___  |
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \     / / 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |   / /  
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/   /_/   
  /////////////////////////////////////////////////////////

  ST_S7_ACTIVATE_AIRLOCK => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must activate an airlock'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actActivateAirlock', 'actRestart'],
  ],

  ST_S7_CROSS_OFF_MULTIPLIER => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must cross off one x2 multiplier'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCrossOffx2Multiplier', 'actRestart'],
  ],

  /////////////////////////////////////////////////////////
  //  ____                            _          ___  
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___    ( _ ) 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \   / _ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | | (_) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/   \___/                                                  
  /////////////////////////////////////////////////////////

  ST_S8_DRAW_ON_MOON => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must draw your insignia on a moon of your choice'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actDrawOnMoon', 'actRestart'],
  ],

  ST_START_ASTRA_S8_TURN_ENGINE => [
    'name' => 'startTurnEngine',
    'type' => 'game',
    'action' => 'stStartAstraS8TurnEngine',
  ],

  ST_S8_ASTRA_TURN => [
    'name' => 'writeNumberS8AstraTurn',
    'descriptionmyturn' => clienttranslate('${you} must write the number for Astra'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actAstraTurn'],
  ],

  ST_S8_ASTRA_DRAW_ON_MOON => [
    'name' => 'pickOneSlot',
    'descriptionmyturn' => clienttranslate('${you} must draw Astra\'s insignia on a moon (${n}/2)'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actAstraDrawOnMoon', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////////////
  //  _____           _    ___   __    ____
  // | ____|_ __   __| |  / _ \ / _|  / ___| __ _ _ __ ___   ___
  // |  _| | '_ \ / _` | | | | | |_  | |  _ / _` | '_ ` _ \ / _ \
  // | |___| | | | (_| | | |_| |  _| | |_| | (_| | | | | | |  __/
  // |_____|_| |_|\__,_|  \___/|_|    \____|\__,_|_| |_| |_|\___|
  //////////////////////////////////////////////////////////////////

  ST_END_SCENARIO => [
    'name' => 'scenarioEnd',
    'description' => '',
    'type' => GAME,
    'action' => 'stEndOfScenario',
    'transitions' => ['' => ST_PRE_END_OF_GAME],
  ],

  ST_PRE_END_OF_GAME => [
    'name' => 'preEndOfGame',
    'description' => '',
    'descriptionmyturn' => '',
    // 'type' => ACTIVE_PLAYER,
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
