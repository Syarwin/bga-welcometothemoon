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


// Engine state
const ST_PRE_END_GAME_TURN = 80;
const ST_END_GAME_TURN = 81;
const ST_POST_END_GAME_TURN = 82;

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

const PLACE_TILE = 'PlaceTile';
const MOVE_TRACK = 'MoveTrack';
const CHOOSE_TRACKS = 'ChooseTracks'; //choose which track to move between what energy offer to you (and/or synergy??)
const PLACE_ROVER = 'PlaceRover';
const MOVE_TRACKER_BY_ONE = 'MoveTrackerByOne';
const TAKE_CIV_CARD = 'TakeCivCard';
const MOVE_ROVER = 'MoveRover';
const COLLECT_MEEPLE = 'CollectMeeple';
const DESTROY_ALL_IN_ROW = 'DestroyAllInRow';
const MOVE_TRACKERS_TO_FIVE = 'MoveTrackersToFive';
const PLACE_MEEPLE = 'PlaceMeeple';
const CHOOSE_ROTATION_ENGINE = 'ChooseRotation';
const DESTROY_P_O_CARD = 'DestroyPOCard';
const POSITION_LIFEPOD_ON_TRACK = 'PositionLifepodOnTrack';
const CHOOSE_FLUX_TRACK = 'ChooseFluxTrack';
const PEEK_NEXT_EVENT = 'PeekNextEvent';
const POSITION_LIFEPOD_ON_TECH = 'PositionLifepodOnTech';
const CLAIM_ALL_IN_A_ROW = 'ClaimAllInARow';
const CHOOSE_OBJECTIVE_FOR_ALL = 'ChooseObjectiveForAll';
const REACH_NEXT_MILESTONE = 'ReachNextMilestone';
const RESET_TRACK = 'ResetTrack';
const GAIN_BIOMASS_PATCH = 'GainBiomassPatch';
const EMPTY_SLOT = 'EmptySlot';

/*
 * Phases
 */
const NORMAL_PHASE = 0;
const END_OF_TURN_PHASE = 1;
const END_OF_GAME_PHASE = 2;

/*
 * Resources
 */
const CIV = 'civ';
const WATER = 'water';
const ROVER = 'rover';
const TECH = 'tech';
const ENERGY = 'energy';
const BIOMASS = 'biomass';

const ALL_TYPES = [CIV, WATER, BIOMASS, ROVER, TECH];

const MEDAL = 'medal';
const SYNERGY = 'Synergy';
const SYNERGY_CIV = 'synergy_civ';
const SYNERGY_ROVER = 'synergy_rover';
const SYNERGY_TECH = 'synergy_tech';
const SYNERGY_WATER = 'synergy_water';
const SKIP = 'skip'; //doesn't exists, placeholder in the array;

/*
 * Constraints Rules
 */

const NOT_ONTO_BIOMASS = 'notOntoBiomass';
const NOT_ONTO_CIV = 'notOntoCiv';
const NOT_ONTO_TECH = 'notOntoTech';
const NOT_ONTO_ROVER = 'notOntoRover';
const NOT_ONTO_ENERGY = 'notOntoEnergy';
const NOT_ONTO_WATER = 'notOntoWater';
const FORBIDDEN_TERRAINS = [
  NOT_ONTO_CIV => CIV,
  NOT_ONTO_TECH => TECH,
  NOT_ONTO_ROVER => ROVER,
  NOT_ONTO_WATER => WATER,
  NOT_ONTO_ENERGY => ENERGY,
  NOT_ONTO_BIOMASS => BIOMASS,
];
const ADD_ROVER = 'addRover';
const ONLY_ONE_MOVE_TRACKER = 'onlyOneMoveTracker';
const CANNOT_PLACE_ON_EDGE = 'cannotPlaceOnEdge';
const NO_MILESTONE = 'noMilestone';
const NO_MATCHING_TERRAINS = 'noMatchingTerrains';
const CANNOT_PLACE_ON_ICE = 'cannotPlaceOnIce';
const NO_SYNERGY = 'noSynergy';

/*
* Direction (for placing tile purpose or moving rover)
*/


const DIRECTIONS = [['x' => -1, 'y' => 0], ['x' => 0, 'y' => -1], ['x' => 1, 'y' => 0], ['x' => 0, 'y' => 1]];
const DIRECTIONS_DIAG = [
  ['x' => -1, 'y' => 0],
  ['x' => -1, 'y' => -1],
  ['x' => -1, 'y' => 1],
  ['x' => 0, 'y' => -1],
  ['x' => 1, 'y' => 0],
  ['x' => 1, 'y' => 1],
  ['x' => 1, 'y' => -1],
  ['x' => 0, 'y' => 1],
];


/*
 *	Cards
 */

const IMMEDIATE = 'immediate';
const END_GAME = 'at the end';

const GREEN = 'green';
const ORANGE = 'orange';
const RED = 'red';
const SOLO_EVENT_CARDS = [65, 66, 67, 79, 110, 116];

/*
 *	Terrains types
 */
const NOTHING = 'nothing';
const LAND = 'land';
const ICE = 'ice';

//they are LAND too
const LIFEPOD = 'lifepod';
const RING = 'ring';
const HOLE = 'hole';
const CITY = 'city';
const TOXIC = 'toxic';
const ELECTRIC = 'electric';

const METEOR = 'meteor';
const ROVER_MEEPLE = 'rover-meeple';
const FLUX_MEEPLE = 'flux';

/**
 * Corporation Tech
 */

const TECH_BYPASS_ADJACENT_CONSTRAINT = 'tech_0_1';
const TECH_CAN_STORE_BIOMASS_PATCH = 'tech_0_2';
const TECH_ROVER_MOVE_PLUS_ONE = 'tech_0_3';
const TECH_WATER_ADVANCE_TWICE = 'tech_0_4';
const TECH_NO_METEOR = 'tech_0_5';

const COSMOS_INC = 1;
const TECH_ROVER_MOVE_DIAG = 'tech_1_1';
const TECH_REPOSITION_THREE_LIFEPODS_ONCE = 'tech_1_2';
const TECH_REPOSITION_LIFEPOD_AFTER_ENERGY = 'tech_1_3';
const TECH_FREE_MOVE_ON_ENERGY = 'tech_1_4';
const TECH_REPOSITION_ONE_LIFEPOD_EACH_TURN = 'tech_1_5';

const FLUX = 2;
const TECH_GET_2_MOVES_ON_FLUX = 'tech_2_1';
const TECH_FLUX_TO_NEXT_MILESTONE = 'tech_2_2';
const TECH_UPGRADED_FLUX_TRACK = 'tech_2_3';
const TECH_COLLECT_METEOR_FLUX = 'tech_2_4';
const TECH_ADVANCE_FLUX = 'tech_2_5';

const HORIZON_GROUP = 3;
const TECH_ROVER_TILES_EVERYWHERE = 'tech_3_1';
const TECH_GET_1_MOVE_CARRYING_METEOR = 'tech_3_2';
const ROVER_CARRYING_METEOR_NUMBER = 'nb_rover_with_meteor';
const TECH_GET_BIOMASS_COLLECTING_METEOR = 'tech_3_3';
const TECH_DESTROY_METEORITE_ON_WATER = 'tech_3_4';
const TECH_ADVANCE_ROVER_TRACKER_EACH_ROUND = 'tech_3_5';

const JUMP_DRIVE = 4;
const TECH_GET_SYNERGY_INSTEAD_OF_BIOMASS_PATCH_ONCE_PER_ROUND = 'tech_4_1';
const TECH_TELEPORT_ROVER_SAME_TERRAIN_ONCE_PER_ROUND = 'tech_4_2';
const TECH_TWICE_SYNERGY_ONCE_PER_ROUND = 'tech_4_3';
const TECH_TREAT_TECH_AS_ENERGY = 'tech_4_4';
const TECH_CLAIM_ALL_BENEFITS_IN_A_ROW_ONCE_PER_GAME = 'tech_4_5';
const TECH_ADD_OBJECTIVE_FOR_ALL_ONCE_PER_GAME = 'tech_4_6';

const MAKE_SHIFT = 5;
const TECH_CIV_TECH_ADJACENT = 'tech_5_1';
const TECH_PLUS_1_ROVER_IF_MULTIPLE_TRACKERS = 'tech_5_2';
const TECH_SHIFT_TRACKER = 'tech_5_3';
const TECH_REGRESS_TRACKER = 'tech_5_4';
const TECH_SCORE_HIGHEST_TRACKER = 'tech_5_5';

const OASIS = 6;
const TECH_SKIP_OVER_TRACKER = 'tech_6_1';
const TECH_GET_1_MOVE_STARTING_ON_WATER = 'tech_6_2';
const TECH_MOVE_WATER_IF_NO_ICE = 'tech_6_3';
const TECH_GET_BIOMASS_WITH_WATER = 'tech_6_4';
const TECH_GET_SYNERGY_WITH_WATER = 'tech_6_5';

const REPUBLIC = 7;
const TECH_REPUBLIC_MOVE_ROVER_WITH_CIV_TILE = 'tech_7_1';
const TECH_REPUBLIC_CAN_CHOOSE_UPGRADED_CIV_CARD = 'tech_7_2';
const TECH_REPUBLIC_TELEPORT_ROVER_CIV_TERRAIN = 'tech_7_3';
const TECH_REPUBLIC_GET_SYNERGY_WITH_CIV_MILESTONE = 'tech_7_4';
const TECH_REPUBLIC_GET_2_CIV_CARDS_END_OF_GAME = 'tech_7_5';
const REPUBLIC_TILE_PLACED = 'tile_placed';

const WORMHOLE = 8;
const TECH_WORMHOLE_RESET_BIOMASS = 'tech_8_1';
const TECH_WORMHOLE_PATCH_ON_TILE = 'tech_8_2';
const TECH_WORMHOLE_GAIN_TWO_BIOMASS_PATCHES = 'tech_8_3';
const TECH_WORMHOLE_CAN_STORE_BIOMASS_PATCH = 'tech_8_4';
const TECH_WORMHOLE_CAN_DESTROY_METEOR_WITH_PATCH = 'tech_8_5';

/*
 * Tiles and Space station
 */

const TILE_N = 0;
const TILE_F = 1;
const TILE_I = 2;
const TILE_v = 3;
const TILE_t = 4;
const TILE_s = 5;
const TILE_L = 6;
const TILE_U = 7;
const TILE_i = 8;
const TILE_BIG_I = 9;
const TILE_O = 10;
const TILE_S = 11;
const BIOMASS_PATCH = 'biomass_patch';

const LARGE_RING = [TILE_S, TILE_F, TILE_s, TILE_BIG_I, TILE_N, TILE_U];
const SMALL_RING = [TILE_I, TILE_t, TILE_v, TILE_L, TILE_i, TILE_O];

/*
 * PLANETS
 */

const ADVANCED_PLANETS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
const ALL_PLANETS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

/*
 * CORPORATIONS
 */
const ADVANCED_CORPORATIONS = [1, 2, 3, 4, 5, 6, 7, 8];
const ALL_CORPORATIONS = [0, 1, 2, 3, 4, 5, 6, 7, 8];

/*
 * MISC
 */

const MODE_APPLY = 0;
const MODE_PRIVATE = 1;
const MODE_REPLAY = 2;

const NO_PLACEMENT = -1;

/************************
 ********** TILES *******
 ************************/

/******************
 ****** STATS ******
 ******************/

const STAT_TURNS = 10;
const STAT_POINTS_BY_PLANETS = 11;
const STAT_POINTS_BY_TRACKS = 12;
const STAT_POINTS_BY_LIFEPODS = 13;
const STAT_POINTS_BY_METEORS = 14;
const STAT_POINTS_BY_CIVS = 15;
const STAT_POINTS_BY_OBECTIVES = 16;
const STAT_CIV_LEVEL = 17;
const STAT_WATER_LEVEL = 18;
const STAT_BIOMASS_LEVEL = 19;
const STAT_ROVER_LEVEL = 20;
const STAT_TECH_LEVEL = 21;
const STAT_TILES_FROM_INTERIOR = 22;
const STAT_TILES_FROM_EXTERIOR = 23;
const STAT_BIOMASS_PATCHES = 24;
