<?php

/*
 * Game options
 */
const OPTION_ADVENTURE = 110;
const OPTION_ASTRA_LEVEL = 111;

/*
 * User preferences
 */
const OPTION_CONFIRM = 103;
const OPTION_CONFIRM_DISABLED = 0;
const OPTION_CONFIRM_ENABLED = 2;
const OPTION_CONFIRM_TIMER = 3;

const OPTION_CONFIRM_UNDOABLE = 104;

const OPTION_CIRCLE_LINKED_SLOT = 105;
const OPTION_CIRCLE_AUTOMATIC = 1;
const OPTION_CIRCLE_MANUAL = 0;

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
const ST_WRITE_X = 32;
const ST_ROCKET_ACTIVATION = 33;
const ST_ACCOMPLISH_MISSION = 34;
const ST_PLACE_ENERGY_WALL = 35;
const ST_GIVE_CARD_TO_ASTRA = 36;
const ST_PROGRAM_ROBOT = 37;
const ST_CIRCLE_PLANT = 38;
const ST_CIRCLE_SINGLE_LINKED = 39;
const ST_CROSS_OFF_SABOTAGE = 40;
const ST_CROSS_OFF_MULTIPLIER = 41;
const ST_IMPROVE_BONUS = 42;
const ST_BUILD_ROBOT_TUNNEL = 43;
const ST_CROSS_OFF_FILLED_QUARTER_BONUS = 44;
const ST_S4_CIRCLE_PLANT_OR_WATER = 45;
const ST_S4_FACTORY_UPGRADE = 46;
const ST_S4_CROSS_OFF_FACTORY_BONUS = 47;
const ST_S5_SPLIT_DOME = 48;
const ST_S5_BUILD_DOME = 49;
const ST_S5_ENERGY_UPGRADE = 50;
const ST_S5_CROSS_OFF_SKYSCRAPER_BONUS = 51;

// Engine state
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
const ST_END_SCENARIO = 86;
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
const ACCOMPLISH_MISSION = 'AccomplishMission';
const GIVE_CARD_TO_ASTRA = 'GiveCardToAstra';
const REPLACE_SOLO_CARD = 'ReplaceSoloCard';
const WRITE_X = 'WriteX';
const CIRCLE_SINGLE_LINKED = 'CircleSingleLinked';
const CIRCLE_NEXT_IN_ROW = 'CircleNextInRow';

// SCENARIO 1
const TAKE_BONUS = 'Scenario1\TakeBonus';
const CROSS_ROCKETS = 'Scenario1\CrossRockets';
const ROCKET_ACTIVATION = 'Scenario1\RocketActivation';
const ACTIVATE_SABOTAGE = 'Scenario1\ActivateSabotage';
const CROSS_OFF_SABOTAGE = 'Scenario1\CrossOffSabotage';

// SCENARIO 2
const CIRCLE_ENERGY = 'Scenario2\CircleEnergy';
const PLACE_ENERGY_WALL = 'Scenario2\PlaceEnergyWall';
const PROGRAM_ROBOT = 'Scenario2\ProgramRobot';
const CIRCLE_PLANT = 'Scenario2\CirclePlant';
const CROSS_OFF_MULTIPLIER = 'Scenario2\CrossOffMultiplier';

// SCENARIO 3
const CIRCLE_GREENHOUSE = 'Scenario3\CircleGreenhouse';
const IMPROVE_BONUS = 'Scenario3\ImproveBonus';
const BUILD_ROBOT_TUNNEL = 'Scenario3\BuildRobotTunnel';
const FILLED_QUARTER = 'Scenario3\FilledQuarter';
const CROSS_OFF_FILLED_QUARTER_BONUS = 'Scenario3\CrossOffFilledQuarterBonus';

// SCENARIO 4
const S4_CIRCLE_PLANT_OR_WATER = 'Scenario4\CirclePlantOrWater';
const S4_FACTORY_UPGRADE = 'Scenario4\FactoryUpgrade';
const S4_EXTRACT_RESOURCES = 'Scenario4\ExtractResources';
const S4_CROSS_OFF_FACTORY_BONUS = 'Scenario4\CrossOffFactoryBonus';

// SCENARIO 5
const S5_SPLIT_DOME = 'Scenario5\SplitDome';
const S5_BUILD_DOME = 'Scenario5\BuildDome';
const S5_ENERGY_UPGRADE = 'Scenario5\EnergyUpgrade';
const S5_FILLED_SKYSCRAPER = 'Scenario5\FilledSkyscraper';
const S5_CROSS_OFF_SKYSCRAPER_BONUS = 'Scenario5\CrossOffSkyscraperBonus';

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
const SOLO = 'solo';

/*
 * MISC
 */

const MODE_APPLY = 0;
const MODE_PRIVATE = 1;
const MODE_REPLAY = 2;

const ROCKET = 'rocket';
const ACTIVATION = 'activation';
const SYSTEM_ERROR = 'error';
const SABOTAGE = 'sabotage';

const RUBY = 'ruby';
const PEARL = 'pearl';

const NUMBER_X = 100;
const NUMBER_6_9 = 200;
const SCRIBBLE = 300;
const SCRIBBLE_ARROW = 301;
const SCRIBBLE_CIRCLE = 302;
const SCRIBBLE_CHECKMARK = 303;
const SCRIBBLE_LINE = 304;
const SCRIBBLE_LINE_ORTHOGONAL = 305;

const SCRIBBLE_INSIGNA_CIRCLE = 310;
const SCRIBBLE_INSIGNA_SQUARE = 311;
const SCRIBBLE_INSIGNA_TRIANGLE = 312;
const SCRIBBLE_INSIGNA_STAR = 313;
const SCRIBBLE_INSIGNA_SPIRAL = 314;
const SCRIBBLE_INSIGNA_HEART = 315;
const SCRIBBLE_INSIGNAS = [
  SCRIBBLE_INSIGNA_CIRCLE,
  SCRIBBLE_INSIGNA_SQUARE,
  SCRIBBLE_INSIGNA_TRIANGLE,
  SCRIBBLE_INSIGNA_STAR,
  SCRIBBLE_INSIGNA_SPIRAL,
  SCRIBBLE_INSIGNA_HEART,
];

const SOLO_CARDS_STACKS = [
  112 => 'A',
  113 => 'B',
  114 => 'C',
];

const TOP_RIGHT_CORNER_SLOT = 400;

const CIRCLE_TYPE_WATER_S2 = 0;
const CIRCLE_TYPE_RUBY = 1;
const CIRCLE_TYPE_PEARL = 2;
const CIRCLE_TYPE_FILLING_BONUS = 3;
const CIRCLE_TYPE_WATER_S4 = 4;
const CIRCLE_TYPE_PLANT_S4 = 5;

const FACTORY_TYPE_MAIN = 0;
const FACTORY_TYPE_SECONDARY = 1;
const FACTORY_TYPE_ASTRONAUT = 2;
const FACTORY_TYPE_PLANNING = 3;

const CIRCLE_SYMBOL_ASTRONAUT = 0;
const CIRCLE_SYMBOL_PLANNING = 1;
const CIRCLE_SYMBOL_WATER = 2;
const CIRCLE_SYMBOL_PLANT = 3;
const CIRCLE_SYMBOL_RUBY = 4;
const CIRCLE_SYMBOL_PEARL = 5;
/******************
 ****** STATS ******
 ******************/

const STAT_TURNS = 10;
const STAT_ENDING_NONE = 0;
const STAT_ENDING_FILLED_ALL = 1;
const STAT_ENDING_MISSIONS = 2;
const STAT_ENDING_SYSTEM_ERRORS = 3;
