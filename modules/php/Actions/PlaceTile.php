<?php

namespace Bga\Games\WelcomeToTheMoon\Actions;

use Bga\Games\WelcomeToTheMoon\Managers\Meeples;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Tiles;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Stats;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Helpers\FlowConvertor;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Managers\Susan;

class PlaceTile extends \Bga\Games\WelcomeToTheMoon\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_TILE;
  }

  public function isDoable($player)
  {
    if ($this->getCtxArg('type') == 'normal') {
      return true;
    }

    list($tiles, $canPlace) = $this->getPlayableTiles($player, true);
    return $canPlace;
  }

  public function getWithBonus()
  {
    return $this->getCtxArg('withBonus') ?? true;
  }

  public function getForcedTiles()
  {
    $forcedTiles = $this->getCtxArg('forcedTiles') ?? null;
    if (!is_null($forcedTiles)) {
      $ids = [];
      // TODO : remove later
      foreach ($forcedTiles as $tileId => $tile) {
        if (is_array($tile)) {
          $ids[] = $tileId;
        } else {
          $ids[] = $tile;
        }
      }
      $forcedTiles = Tiles::getMany($ids);
    }

    return $forcedTiles;
  }

  public function getPossibleTiles($player)
  {
    $tiles = Susan::getPlayableTilesForPlayer($player);

    if ($player->corporation()->isFlagged(TECH_REPUBLIC_MOVE_ROVER_WITH_CIV_TILE)) {
      Utils::filter($tiles, function ($tile) {
        return in_array(CIV, $tile->getTerrainTypes());
      });
    }
    return $tiles;
  }

  public function getPlayableTiles($player, $checkIsDoable = false, $forcedTiles = null)
  {
    $tiles = [];
    $forcedTiles = $forcedTiles ?? $this->getForcedTiles();

    $specialRule = Globals::getTurnSpecialRule();
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile, $checkIsDoable, $specialRule);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return [$tiles, true];
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return [$tiles, true];
    }

    // Same without special rule
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile, $checkIsDoable);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return [$tiles, true];
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return [$tiles, true];
    }

    // Otherwise, let them pick any tile without placing it
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $tiles[$tile->getId()] = NO_PLACEMENT;
    }
    return [$tiles, false];
  }

  public function isBiomassPatch()
  {
    return $this->getCtxArg('biomassPatch') ?? false;
  }

  public function getDescription()
  {
    $description = $this->isBiomassPatch() ? clienttranslate('a biomass patch') : clienttranslate('a new tile');

    return [
      'log' => \clienttranslate('Place ${description} on your planet'),
      'args' => [
        'description' => $description,
        'i18n' => ['description'],
      ],
    ];
  }

  public function argsPlaceTile()
  {
    $player = $this->getPlayer();
    list($tiles, $canPlace) = $this->getPlayableTiles($player);

    $skippableBiomass = $this->isBiomassPatch() && $player->corporation()->canPlaceBiomassPatchLater();
    return [
      'tiles' => $tiles,
      'descSuffix' => $canPlace ? ($skippableBiomass ? 'skippablebiomass' : '') : 'impossible',
    ];
  }

  public function actPlaceTile($tileId, $pos, $rotation, $flipped)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;

    if (!is_array($tileOptions)) {
      var_dump($this->getPlayableTiles($player));
      var_dump($player->getId(), $tileOptions, $pos, $rotation, $flipped);
      die('test');
    }
    if (is_null($tileOptions)) {
      throw new \BgaVisibleSystemException('You cannot place that tile. Should not happen ' . $tileId);
    }
    $option = Utils::search($tileOptions, function ($option) use ($pos) {
      return $option['pos']['x'] == $pos['x'] && $option['pos']['y'] == $pos['y'];
    });
    if ($option === false) {
      throw new \BgaVisibleSystemException('You cannot place the tile here. Should not happen ' . var_export($pos, true));
    }
    if (!in_array([$rotation, $flipped], $tileOptions[$option]['r'])) {
      throw new \BgaVisibleSystemException('You cannot place the tile here with that rotation/flip. Should not happen');
    }

    // Old location is used to update SUSAN counters in UI
    $oldLocation = Tiles::get($tileId)->getLocation();
    // Wormhole corporation => check number of biomass zones before and after
    if ($player->getCorporationId() == WORMHOLE) {
      $biomassZones = $player->planet()->countZoneNb(BIOMASS);
    }

    // Place it on the board
    list($tile, $symbols, $coveringWater, $meteor) = $player->planet()->addTile($tileId, $pos, $rotation, $flipped);

    //statistics
    if (Globals::getMode() == MODE_APPLY) {
      $shape = $tile->getShape();
      if ($shape === BIOMASS_PATCH) {
        Stats::incBiomassPatches($player->getId(), 1);
      } elseif (in_array($shape, SMALL_RING)) {
        Stats::incInteriorTiles($player->getId(), 1);
      } elseif (in_array($shape, LARGE_RING)) {
        Stats::incExteriorTiles($player->getId(), 1);
      }
    }

    // Wormhole corporation => check number of biomass zones before and after
    if ($player->getCorporationId() == WORMHOLE) {
      $cantAdvanceBiomass = $player->planet()->countZoneNb(BIOMASS) > $biomassZones;
    } elseif ($player->getCorporationId() == REPUBLIC) {
      $player->corporation()->addFlag(REPUBLIC_TILE_PLACED);
    }

    // Record it except if it's a patch
    if (!$tile->isBiomassPatch()) {
      $player->setLastTileId($tileId);
    }

    // Add asteroid meeples UNLESS tech no meteor of universal corporation
    if (!is_null($meteor) && !$player->hasTech(TECH_NO_METEOR)) {
      $meteor = Meeples::addMeteor($player, $meteor);
    }

    // Destroy pod/rover if any are covered
    [$destroyedRover, $destroyedLifepod] = Meeples::destroyCoveredMeeples($player, $tile);

    // Place extra Rover if it's the turn special rules
    if (Globals::getTurnSpecialRule() == ADD_ROVER) {
      $this->pushParallelChild([
        'action' => PLACE_ROVER,
      ]);
    }

    // If the played tile cover no ice, a corpo advance its water tracker
    if ($player->hasTech(TECH_MOVE_WATER_IF_NO_ICE) && !$tile->isBiomassPatch()) {
      if ($player->planet()->isTileOnlyOnLand($tile)) {
        $this->pushParallelChild([
          'action' => MOVE_TRACK,
          'args' => ['type' => WATER, 'n' => 1, 'withBonus' => true],
        ]);
      }
    }

    // Move tracks
    $tileTypes = [];
    $actions = [];

    foreach ($symbols as $symbol) {
      $type = $symbol['type'];
      $tileTypes[] = $type;

      if (!$this->getWithBonus()) {
        continue;
      }

      // Wormhole corpration => must expand a biomass zone to gain symbol
      if ($type == BIOMASS && ($cantAdvanceBiomass ?? false)) {
        continue;
      }

      // Flux tech : collect meteor on a placed tile if it matches flux track
      if (
        $player->hasTech(TECH_COLLECT_METEOR_FLUX) &&
        $type == $player->corporation()->getFluxTrack() &&
        !$tile->isBiomassPatch()
      ) {
        $actions[] = [
          'action' => COLLECT_MEEPLE,
          'args' => [
            'type' => METEOR,
          ],
        ];
      }

      // Energy => compute the possible tracks
      if ($type == ENERGY) {
        //big if for corpo1
        if ($player->hasTech(TECH_REPOSITION_LIFEPOD_AFTER_ENERGY)) {
          $actions[] = [
            'action' => POSITION_LIFEPOD_ON_TRACK,
            'args' => [
              'remaining' => 1,
            ],
          ];
        }

        $types = $player->planet()->getTypesAdjacentToEnergy($symbol['cell']);
        // WORMHOLE : cant advance BIOMASS using energy
        if ($player->getCorporationId() == WORMHOLE) {
          Utils::filter($types, function ($type) {
            return $type != BIOMASS;
          });
        }

        $actions[] = [
          'action' => CHOOSE_TRACKS,
          'args' => [
            'types' => $types,
            'n' => 1,
            'energy' => true,
            'from' => ENERGY,
          ],
        ];
        continue;
      }
      // Weird energy of JumpDrive
      elseif ($player->hasTech(TECH_TREAT_TECH_AS_ENERGY) && $type == TECH) {
        $types = [];
        foreach ($player->planet()->getTileCoveredCells($tile, false) as $i => $cell) {
          if ($player->planet()->getVisible($cell['x'], $cell['y']) != TECH) {
            continue;
          }
          $types = array_merge($types, $player->planet()->getTypesAdjacentToEnergy($cell));
        }
        $types = array_values(array_unique($types));

        $actions[] = [
          'action' => CHOOSE_TRACKS,
          'args' => [
            'types' => $types,
            'n' => 1,
            'energy' => true,
            'from' => ENERGY,
          ],
        ];
        continue;
      }
      // Water => stop if the tile is not covering water or give some action for special corpo
      elseif ($type == WATER) {
        if ($player->hasTech(TECH_GET_BIOMASS_WITH_WATER)) {
          $this->pushParallelChild(Actions::getBiomassPatchFlow());
        }
        if ($player->hasTech(TECH_GET_SYNERGY_WITH_WATER)) {
          $action = $player->getSynergy();
          if ($action) {
            $this->pushParallelChild($action);
          }
        }

        if (!$coveringWater) {
          continue;
        } elseif ($player->corporation()->getId() == OASIS) {
          $actions[] = [
            'action' => MOVE_TRACK,
            'args' => ['type' => $type, 'n' => $coveringWater, 'withBonus' => true],
          ];
          continue;
        }
      }

      // Normal case: add parallel child
      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => 'placingTile', 'withBonus' => true],
      ];
    }

    if (Globals::getTurnSpecialRule() == ONLY_ONE_MOVE_TRACKER) {
      if (!empty($actions)) {
        $this->pushParallelChild([
          'type' => NODE_XOR,
          'childs' => $actions,
        ]);
      }
    } else {
      //normal case
      $this->pushParallelChilds($actions);
    }

    Notifications::placeTile($player, $tile, $meteor, $tileTypes, $oldLocation);

    if ($destroyedLifepod->count()) {
      Notifications::destroyedMeeples($player, $destroyedLifepod, LIFEPOD);
    }
    if ($destroyedRover->count()) {
      Notifications::destroyedMeeples($player, $destroyedRover, ROVER);
    }

    ////////////////
    // WORMHOLE
    if ($tile->getType() === BIOMASS_PATCH) {
      // Destroy meteor with biomass patch
      if ($player->hasTech(TECH_WORMHOLE_CAN_DESTROY_METEOR_WITH_PATCH)) {
        $meeple = $player->getMeepleOnCell($pos, METEOR);
        if (!is_null($meeple)) {
          Meeples::move($meeple->getId(), 'trash');
          Notifications::destroyedMeteorWormholePatch($player, $meeple);
        }
      }
    }
  }

  public function actPlaceTileNoPlacement($tileId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;
    $noPlacement = $tileOptions == NO_PLACEMENT;
    if (is_null($tileOptions) || !$noPlacement) {
      throw new \BgaVisibleSystemException('You cannot place that tile. Should not happen ' . $tileId);
    }

    // Place it on the board
    list($tile, $symbols) = $player->planet()->addTileNoPlacement($tileId);

    // Move tracks
    $tileTypes = [];
    $actions = [];
    foreach ($symbols as $symbol) {
      if (!$this->getWithBonus()) {
        continue;
      }

      $type = $symbol['type'];
      $tileTypes[] = $type;
      $n = 1;
      // if ($type == WATER && $player->hasTech(TECH_WATER_ADVANCE_TWICE)) {
      //   $n *= 2;
      // }

      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => $n, 'withBonus' => true],
      ];
    }

    if (Globals::getTurnSpecialRule() == ONLY_ONE_MOVE_TRACKER) {
      if (!empty($actions)) {
        $this->pushParallelChild([
          'type' => NODE_XOR,
          'childs' => $actions,
        ]);
      }
    } else {
      $this->pushParallelChilds($actions);
    }

    Notifications::placeTileNoPlacement($player, $tile, $tileTypes);
  }

  public function actKeepBiomassPatch()
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    if ($args['descSuffix'] != 'skippablebiomass') {
      throw new \BgaVisibleSystemException('You cannot keep that biomass patch for later. Should not happen ');
    }

    $player->addEndOfGameAction([
      'action' => PLACE_TILE,
      'args' => [
        'forcedTiles' => $this->getForcedTiles()->getIds(),
        'biomassPatch' => true,
      ],
    ]);

    Notifications::delayBiomassPatch($player);
  }
}
