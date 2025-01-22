
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- agricola implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

CREATE TABLE IF NOT EXISTS `construction_cards` (
  `card_id` int(10) unsigned NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_state` int(11) NOT NULL,
  `number` float(11) NOT NULL,
  `action` varchar(30) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `plan_cards` (
  `card_id` int(10) unsigned NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_state` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;


CREATE TABLE IF NOT EXISTS `scribbles` (
  `scribble_id` varchar(10) NOT NULL,
  `scribble_location` varchar(100) NOT NULL,
  `scribble_state` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `type_arg` int(11),
  `turn` int(10) NOT NULL,
  PRIMARY KEY (`scribble_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Additional player's info
-- ALTER TABLE `player` ADD `rover` INT(10) NOT NULL DEFAULT 0;

-- CORE TABLES --
CREATE TABLE IF NOT EXISTS `global_variables` (
  `name` varchar(255) NOT NULL,
  `value` JSON,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pglobal_variables` (
  `name` varchar(255) NOT NULL,
  `value` JSON,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10),
  `move_id` int(10) NOT NULL,
  `table` varchar(32) NOT NULL,
  `primary` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `affected` JSON,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
