<?php

namespace Bga\Games\WelcomeToTheMoon\Core;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

class Notifications
{
  public static function setupScenario($scenario)
  {
    $names = [
      1 => clienttranslate("The Launch"),
      2 => clienttranslate("The Journey"),
    ];
    self::notifyAll('message', clienttranslate('Starting scenario ${n}: ${name}'), [
      'n' => $scenario,
      'name' => $names[$scenario],
      'i18n' => ['name'],
    ]);
  }

  public static function newTurn(int $turn, array $cards)
  {
    self::notifyAll('newTurn', clienttranslate('Turn ${turn}'), [
      'turn' => $turn,
      'cards' => $cards,
      'deckCount' => ConstructionCards::getCardsLeft(),
    ]);
  }


  public static function endGameTriggered(?Player $player, string $condition)
  {
    self::notifyAll('endGameTriggered', clienttranslate('End triggered'), []);

    $msgs = [
      'errors' => Globals::getScenario() == 1 ?
        clienttranslate('${player_name} circled all the System Error <SYSTEM-ERROR> boxes, therefore triggering the end of the adventure')
        : clienttranslate('${player_name} crossed off all the System Error <SYSTEM-ERROR> boxes, therefore triggering the end of the adventure'),

      'plans' => clienttranslate('${player_name} accomplished all three Missions, therefore triggering the end of the adventure'),

      'houses' => clienttranslate('${player_name} filled in all the spaces of the marking area, therefore triggering the end of the adventure'),

      'launch' => clienttranslate('${player_name} launched his rocket successfully, therefore triggering the end of the adventure'),
      'astraLaunch' => clienttranslate('ASTRA launched his rocket successfully, therefore triggering the end of the adventure'),

      'soloDraw' => clienttranslate('The draw pile has been exhausted for the second time, therefore triggering the end of the adventure')
    ];

    self::notifyAll('endGameMessage', $msgs[$condition], [
      'player' => $player
    ]);
  }


  public static function chooseCards(Player $player, array $combination, bool $useJoker)
  {
    $names = [
      ASTRONAUT => clienttranslate('Astronaut'),
      ROBOT => clienttranslate('Robot'),
      PLANT => clienttranslate('Plant'),
      WATER => clienttranslate('Water'),
      PLANNING => clienttranslate('Planning'),
      ENERGY => clienttranslate('Energy'),
      JOKER => clienttranslate('Joker'),
    ];

    $msg = $useJoker ?
      clienttranslate('${player_name} chooses the combination ${number} ${action}${action_icon} using an action joker.')
      : clienttranslate('${player_name} chooses the combination ${number} ${action}${action_icon}.');
    static::pnotify($player, 'chooseCards', $msg, [
      'player' => $player,
      'i18n' => ['action'],
      'action' => $names[$combination['action']],
      'action_icon' => "",
      'number' => $combination['number'],
    ]);
  }

  // Generic addScribble notif
  public static function addScribble(?Player $player, Scribble $scribble, $msg = '', $args = [])
  {
    $args['scribble'] = $scribble;
    if (is_null($player)) {
      self::notifyAll('addScribble', $msg, $args);
    } else {
      $args['player'] = $player;
      self::pnotify($player, 'addScribble', $msg, $args);
    }
  }

  // Generic addScribbles notif
  public static function addScribbles(?Player $player, array $scribbles, $msg, $args = [])
  {
    $args['scribbles'] = $scribbles;
    if (is_null($player)) {
      self::notifyAll('addScribbles', $msg, $args);
    } else {
      $args['player'] = $player;
      self::pnotify($player, 'addScribbles', $msg, $args);
    }
  }

  public static function writeNumber(Player $player, int $number, array $scribbles, ?string $source = null)
  {
    $msg = clienttranslate('${player_name} writes ${number} on his scoresheet');
    $data = [
      'number' => $number,
    ];

    if ($number == NUMBER_X) {
      $msg = clienttranslate('${player_name} writes an <X> on his scoresheet (${source})');
      $data['source'] = $source;
      $data['i18n'][] = 'source';
    }

    self::addScribbles($player, $scribbles, $msg, $data);
  }

  public static function accomplishMission(Player $player, PlanCard $plan, array $scribbles, bool $firstValidation)
  {
    $stacks = ['A', 'B', 'C'];
    $msg = $firstValidation ? clienttranslate('${player_name} accomplishes mission ${stack} (first fulfillment)') : clienttranslate('${player_name} accomplishes mission ${stack}');
    static::pnotify($player, 'accomplishMission', $msg, [
      'player' => $player,
      'scribbles' => $scribbles,
      'stack' => $stacks[$plan->getStackIndex()],
      'firstValidation' => $firstValidation,
      'planId' => $plan->getId()
    ]);
  }

  public static function systemError(Player $player, Scribble $scribble)
  {
    $msg = Globals::getScenario() == 1 ?
      clienttranslate('<SYSTEM-ERROR> ${player_name} cannot write down any number and must circle one System Error box <SYSTEM-ERROR>')
      : clienttranslate('<SYSTEM-ERROR> ${player_name} cannot write down any number and must cross off one System Error box <SYSTEM-ERROR>');

    self::addScribble($player, $scribble, $msg);
  }

  public static function giveCardToAstra(Player $player, ConstructionCard $card)
  {
    $names = [
      ASTRONAUT => clienttranslate('Astronaut'),
      ROBOT => clienttranslate('Robot'),
      PLANT => clienttranslate('Plant'),
      WATER => clienttranslate('Water'),
      PLANNING => clienttranslate('Planning'),
      ENERGY => clienttranslate('Energy'),
      JOKER => clienttranslate('Joker'),
    ];

    static::pnotify($player, 'giveCardToAstra', clienttranslate('${player_name} leaves a ${action}${action_icon} card to ASTRA.'), [
      'player' => $player,
      'i18n' => ['action'],
      'action' => $names[$card->getAction()],
      'card' => $card,
      'action_icon' => "",
    ]);
  }

  public static function useSoloBonus(Player $player, Scribble $scribble)
  {
    self::addScribble($player, $scribble, clienttranslate('${player_name} uses a solo bonus instead of giving a card to ASTRA.'));
  }

  public static function replaceSoloCard($player, $stack, $card, $drawnCard)
  {
    self::pnotify($player, 'replaceSoloCard', clienttranslate('Replacing solo card ${stack} by another construction card'), [
      'stack' => $stack,
      'oldCard' => $card,
      'newCard' => $drawnCard,
      'deckCount' => ConstructionCards::getCardsLeft(),
    ]);
  }

  ///////////////////////////////////////////////////
  //  ____                            _         _ 
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   / |
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | |
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | | |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |_|
  ///////////////////////////////////////////////////

  public static function crossRockets(Player $player, int $mRockets, int $mErrors, array $scribbles, string $source, int $mCrossed, int $mCircled)
  {
    $msg = clienttranslate('${player_name} crosses ${mRockets} rockets <ROCKET> and ${mErrors} system errors <SYSTEM-ERROR> (${mCrossed} crossed / ${mCircled} circled) (${source})');
    if ($mRockets == 0) $msg = clienttranslate('${player_name} crosses ${mErrors} system errors <SYSTEM-ERROR> (${mCrossed} crossed / ${mCircled} circled) (${source})');
    if ($mErrors == 0) $msg = clienttranslate('${player_name} crosses ${mRockets} rockets <ROCKET> (${source})');

    static::addScribbles($player, $scribbles, $msg, [
      'mRockets' => $mRockets,
      'mErrors' => $mErrors,
      'mCrossed' => $mCrossed,
      'mCircled' => $mCircled,
      'source' => $source,
      'i18n' => ['source'],
    ]);
  }

  public static function activateRocket(Player $player, array $scribbles, string $source)
  {
    static::addScribbles($player, $scribbles, clienttranslate('${player_name} activate <ARROW> an Inactivate Rocket quarter bonus (${source})'), [
      'source' => $source,
      'i18n' => ['source'],
    ]);
  }

  public static function activateSabotage(Player $player, Scribble $scribble, string $source)
  {
    self::addScribble($player, $scribble, clienttranslate('${player_name} triggers a Sabotage <SABOTAGE> (${source})'), [
      'source' => $source,
      'i18n' => ['source'],
    ]);
  }

  public static function resolveSabotage(array $players, array $scribbles, string $source)
  {
    static::notifyAll('resolveSabotage', clienttranslate('<SABOTAGE> Sabotage <SABOTAGE> : ${players_names} must circle a System Error (${source})'), [
      'players' => $players,
      'scribbles' => $scribbles,
      'source' => $source,
      'i18n' => ['source'],
    ]);
  }

  public static function astraCrossRockets(Player $player, int $mRockets, array $scribbles)
  {
    static::addScribbles($player, $scribbles, clienttranslate('ASTRA crosses ${mRockets} rockets <ROCKET>'), [
      'mRockets' => $mRockets,
    ]);
  }


  public static function resolveSabotageAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} gains one solo bonus'));
  }

  public static function crossOffSabotage(Player $player, array $scribbles)
  {
    static::addScribbles($player, $scribbles, clienttranslate('${player_name} crosses off one available Sabotage bonus <SABOTAGE> and circle one System Error'));
  }


  /////////////////////////////////////////////////////////
  //  ____                            _         ____  
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   |___ \ 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \    __) |
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  / __/ 
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |_____|
  /////////////////////////////////////////////////////////

  public static function circleEnergy(Player $player, Scribble $scribble, bool $mustBuildWall)
  {
    $msg = $mustBuildWall ?
      clienttranslate('${player_name} circles one Energy symbol <ENERGY> and must divide a zone on his trajectory')
      : clienttranslate('${player_name} circles one Energy symbol <ENERGY>');

    static::addScribble($player, $scribble, $msg);
  }

  public static function placeEnergyWall(Player $player, array $scribbles)
  {
    static::addScribbles($player, $scribbles, clienttranslate('${player_name} divides a zone on his trajectory'));
  }

  public static function programRobot(Player $player, Scribble $scribble)
  {
    $msg = clienttranslate('${player_name} programs a robot');
    static::addScribble($player, $scribble, $msg);
  }

  public static function circleMultiplier(Player $player, Scribble $scribble, int $multiplierValue)
  {
    $msg = clienttranslate('${player_name} have programmed all robots of a single station and gets a multiplier x${multiplierValue}');
    static::addScribble($player, $scribble, $msg, [
      'multiplierValue' => $multiplierValue
    ]);
  }

  public static function crossOffMultiplier(Player $player, Scribble $scribble, int $multiplierValue)
  {
    $msg = clienttranslate('${player_name} crosses off a multiplier x${multiplierValue}');
    static::addScribble(null, $scribble, $msg, [
      'player' => $player,
      'multiplierValue' => $multiplierValue,
    ]);
  }

  public static function circlePlant(Player $player, Scribble $scribble, int $stationNumber)
  {
    $msg = clienttranslate('${player_name} circles a plant at a station number ${stationNumber}');
    static::addScribble($player, $scribble, $msg, [
      'stationNumber' => $stationNumber
    ]);
  }

  public static function stirWaterTanks(Player $player, Scribble $scribble, int $waterValue)
  {
    $msg = clienttranslate('${player_name} stirs a water tank with the value of ${waterValue}');
    static::addScribble($player, $scribble, $msg, [
      'waterValue' => $waterValue
    ]);
  }

  public static function circleStationHighMultAstra(Player $player, array $scribbles)
  {
    static::addScribbles($player, $scribbles, clienttranslate('${player_name} gains two solo bonuses'));
  }

  public static function crossOffMultiplierAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off one station multiplier'));
  }

  public static function scribbleAstronaut(Player $player, Scribble $scribble)
  {
    $msg = clienttranslate('${player_name} crosses off an Astronaut symbol');
    static::pnotify($player, 'addScribble', $msg, [
      'player' => $player,
      'scribble' => $scribble,
    ]);
  }

  public static function circleJoker(Player $player, Scribble $scribble)
  {
    $msg = clienttranslate('${player_name} circles a Wild Action symbol');
    static::pnotify($player, 'addScribble', $msg, [
      'player' => $player,
      'scribble' => $scribble,
    ]);
  }

  /////////////////////////////////////
  //   ____           _          
  //  / ___|__ _  ___| |__   ___ 
  // | |   / _` |/ __| '_ \ / _ \
  // | |__| (_| | (__| | | |  __/
  //  \____\__,_|\___|_| |_|\___|
  /////////////////////////////////////

  protected static $listeners = [
    [
      'name' => 'scoresheet',
      'player' => true,
      'scoresheet' => true,
      'method' => 'computeUiData',
    ],
    [
      'name' => 'astra',
      'method' => ['Bga\Games\WelcomeToTheMoon\Managers\Players', 'getAstraDatas'],
    ]
  ];

  protected static $cachedValues = [];

  public static function resetCache()
  {
    foreach (self::$listeners as $listener) {
      $method = $listener['method'];
      if ($listener['player'] ?? false) {
        foreach (Players::getAll() as $pId => $player) {
          $val = null;
          if ($listener['scoresheet'] ?? false) {
            $val = is_null($player->scoresheet()) ? null : $player->scoresheet()->$method();
          } else {
            $val = $player->$method();
          }
          self::$cachedValues[$listener['name']][$pId] = $val;
        }
      } else {
        self::$cachedValues[$listener['name']] = call_user_func($method);
      }
    }
  }

  public static function updateIfNeeded(&$args, $notifName, $notifType)
  {
    foreach (self::$listeners as $listener) {
      $name = $listener['name'];
      $method = $listener['method'];

      if ($listener['player'] ?? false) {
        foreach (Players::getAll() as $pId => $player) {
          $val = null;
          if ($listener['scoresheet'] ?? false) {
            $val = is_null($player->scoresheet()) ? null : $player->scoresheet()->$method();
          } else {
            $val = $player->$method();
          }

          if ($val !== (self::$cachedValues[$name][$pId] ?? null)) {
            $args['infos'][$name][$pId] = $val;
            // // Only bust cache when a public non-ignored notif is sent to make sure everyone gets the info
            // if ($notifType == 'public' && !in_array($notifName, self::$ignoredNotifs)) {
            self::$cachedValues[$name][$pId] = $val;
            // }
          }
        }
      } else {
        $val = call_user_func($method);
        if ($val !== (self::$cachedValues[$name] ?? null)) {
          $args['infos'][$name] = $val;
          // // Only bust cache when a public non-ignored notif is sent to make sure everyone gets the info
          // if ($notifType == 'public' && !in_array($notifName, self::$ignoredNotifs)) {
          self::$cachedValues[$name] = $val;
          // }
        }
      }
    }
  }


  ///////////////////////////////////////////////////////////////////////////////////
  //   ____                      _        __  __      _   _               _     
  //  / ___| ___ _ __   ___ _ __(_) ___  |  \/  | ___| |_| |__   ___   __| |___ 
  // | |  _ / _ \ '_ \ / _ \ '__| |/ __| | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|
  // | |_| |  __/ | | |  __/ |  | | (__  | |  | |  __/ |_| | | | (_) | (_| \__ \
  //  \____|\___|_| |_|\___|_|  |_|\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/
  ///////////////////////////////////////////////////////////////////////////////////

  protected static function notifyAll($name, $msg, $data)
  {
    self::updateIfNeeded($data, $name, "public");
    self::updateArgs($data, true);
    Game::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($player, $name, $msg, $data)
  {
    self::updateIfNeeded($data, $name, "private");
    $pId = is_int($player) ? $player : $player->getId();
    self::updateArgs($data);
    Game::get()->notifyPlayer($pId, $name, $msg, $data);
  }

  protected static function pnotify($player, $name, $msg, $data)
  {
    $mode = Globals::getMode();
    $pId = is_int($player) ? $player : $player->getId();
    $data['player'] = $player;
    self::updateArgs($data, $mode == \MODE_APPLY);

    // PRIVATE MODE => send private notif
    if ($mode == MODE_PRIVATE) {
      self::updateIfNeeded($data, $name, "private");
      Game::get()->notifyPlayer($pId, $name, $msg, $data);
      self::flush();
    } // PUBLIC MODE => send public notif with ignore flag
    elseif ($mode == \MODE_APPLY && ($data['public'] ?? true)) {
      if (!empty(PGlobals::getEngine($pId))) {
        $data['ignore'] = $pId;
        $data['preserve'][] = 'ignore';
      }
      self::updateIfNeeded($data, $name, "public");
      Game::get()->notifyAllPlayers($name, $msg, $data);
    }
  }

  public static function message($txt, $args = [])
  {
    self::notifyAll('message', $txt, $args);
  }

  public static function midMessage($txt, $args = [])
  {
    self::notifyAll('midMessage', $txt, $args);
  }

  public static function pmidMessage(Player $player, $txt, $args = [])
  {
    self::pnotify($player, 'midMessage', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = [])
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::notify($pId, 'message', $txt, $args);
  }

  public static function newUndoableStep($player, $stepId)
  {
    self::notify($player, 'newUndoableStep', clienttranslate('Undo here'), [
      'stepId' => $stepId,
      'preserve' => ['stepId'],
    ]);
  }

  public static function clearTurn($player, $notifIds)
  {
    self::notify($player, 'clearTurn', clienttranslate('You restart your turn'), [
      'player' => $player,
      'notifIds' => $notifIds,
    ]);
  }

  public static function refreshUI($pId, $datas)
  {
    // // Keep only the thing that matters
    $fDatas = [
      'players' => $datas['players'],
      'scribbles' => $datas['scribbles'],
      'constructionCards' => $datas['constructionCards'],
    ];

    self::notify($pId, 'refreshUI', '', [
      'datas' => $fDatas,
    ]);
  }

  public static function flush()
  {
    self::notifyAll('flush', '', []);
  }

  public static function scores()
  {
    // TODO
  }

  ///////////////////////////////////////////////////////////////
  //  _   _           _       _            _
  // | | | |_ __   __| | __ _| |_ ___     / \   _ __ __ _ ___
  // | | | | '_ \ / _` |/ _` | __/ _ \   / _ \ | '__/ _` / __|
  // | |_| | |_) | (_| | (_| | ||  __/  / ___ \| | | (_| \__ \
  //  \___/| .__/ \__,_|\__,_|\__\___| /_/   \_\_|  \__, |___/
  //       |_|                                      |___/
  ///////////////////////////////////////////////////////////////

  /*
   * Automatically adds some standard field about player and/or card
   */
  protected static function updateArgs(&$data, $public = false)
  {
    if (isset($data['player'])) {
      $data['player_name'] = $data['player']->getName();
      $data['player_id'] = $data['player']->getId();
      // if (!$public) {
      //   $data['scores'] = Players::scores($data['player']->getId(), false);
      // }
      unset($data['player']);
    }
    if (isset($data['player2'])) {
      $data['player_name2'] = $data['player2']->getName();
      $data['player_id2'] = $data['player2']->getId();
      unset($data['player2']);
    }
    if (isset($data['player3'])) {
      $data['player_name3'] = $data['player3']->getName();
      $data['player_id3'] = $data['player3']->getId();
      unset($data['player3']);
    }
    if (isset($data['players'])) {
      $args = [];
      $logs = [];
      foreach ($data['players'] as $i => $player) {
        $logs[] = '${player_name' . $i . '}';
        $args['player_name' . $i] = $player->getName();
      }
      $data['players_names'] = [
        'log' => join(', ', $logs),
        'args' => $args,
      ];
      $data['i18n'][] = 'players_names';
      unset($data['players']);
    }
  }
}
