<?php

/*
 * Game options
 */


/*
 * User preferences
 */
const OPTION_CONFIRM = 103;
const OPTION_CONFIRM_DISABLED = 0;
const OPTION_CONFIRM_ENABLED = 2;
const OPTION_CONFIRM_TIMER = 3;

const OPTION_CONFIRM_UNDOABLE = 104;

/*
 * State constants
 */

const GAME = 'game';
const MULTI = 'multipleactiveplayer';
const PRIVATESTATE = 'private';
const END_TURN = 'endTurn';
const ACTIVE_PLAYER = 'activeplayer';

const ST_GAME_SETUP = 1;
const ST_SETUP_BRANCH = 2;

// Setup
const ST_SETUP_DEBUG = 3;
const ST_SETUP_SCENARIO = 5;

const ST_REVEAL_EVENT_CARD = 10;
const ST_PLAY_AFTER_EVENT_CARD = 12;
const ST_CHOOSE_CIV_CARD = 14;
const ST_END_TURN = 15;

const ST_START_TURN = 20;
const ST_CHOOSE_ROTATION = 21;
const ST_START_TURN_ENGINE = 22;

// Atomic actions
const ST_CHOOSE_CARDS = 30;
const ST_WRITE_NUMBER = 31;

// Engine state
const ST_PRE_END_GAME_TURN = 80;
const ST_END_GAME_TURN = 81;
const ST_POST_END_GAME_TURN = 82;

const ST_GENERIC_AUTOMATIC = 88;
const ST_SETUP_PRIVATE_ENGINE = 89;
const ST_RESOLVE_STACK = 90;
const ST_RESOLVE_CHOICE = 91;
const ST_IMPOSSIBLE_MANDATORY_ACTION = 92;
const ST_CONFIRM_TURN = 93;
const ST_CONFIRM_PARTIAL_TURN = 94;
const ST_INIT_PRIVATE_ENGINE = 95;
const ST_APPLY_ENGINE = 96;

const ST_GENERIC_NEXT_PLAYER = 97;
const ST_PRE_END_OF_GAME = 98;
const ST_END_GAME = 99;

/*
 * ENGINE
 */
const NODE_SEQ = 'seq';
const NODE_OR = 'or';
const NODE_XOR = 'xor';
const NODE_PARALLEL = 'parallel';
const NODE_LEAF = 'leaf';

const ZOMBIE = 98;
const PASS = 99;

/*
 * Atomic action
 */

const CHOOSE_CARDS = 'ChooseCards';
const WRITE_NUMBER = 'WriteNumber';

// SCENARIO 1
const TAKE_BONUS = 'Scenario1\TakeBonus';
const CROSS_ROCKETS = 'Scenario1\CrossRockets';

/*
 * Actions
 */

const ROBOT = 'robot';
const WATER = 'water';
const ENERGY = 'energy';
const PLANT = 'plant';
const ASTRONAUT = "astronaut";
const PLANNING = "planning";
const JOKER = "joker";

const ALL_ACTIONS = [ROBOT, WATER, ENERGY, PLANT, ASTRONAUT, PLANNING];

const ENERGY_WATER = 'energy-water';
const ASTRONAUT_PLANT = 'astronaut-plant';
const ROBOT_PLANNING = 'robot-planning';

/*
 * MISC
 */

const MODE_APPLY = 0;
const MODE_PRIVATE = 1;
const MODE_REPLAY = 2;

const ROCKET = 'rocket';
const SYSTEM_ERROR = 'error';

const NUMBER_X = 100;
const NUMBER_6_9 = 200;
const SCRIBBLE = 300;

/******************
 ****** STATS ******
 ******************/

const STAT_TURNS = 10;
