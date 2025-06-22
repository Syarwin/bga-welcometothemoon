<?php

namespace Bga\Games\WelcomeToTheMoon\Core;

use Bga\Games\WelcomeToTheMoon\Game;
use Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;
use Bga\Games\WelcomeToTheMoon\Models\PlanCard;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Quarter;
use Bga\Games\WelcomeToTheMoon\Models\Scribble;

class Notifications
{
  public static function setupScenario($scenario)
  {
    $names = [
      1 => clienttranslate("The Launch"),
      2 => clienttranslate("The Journey"),
      3 => clienttranslate("The Colony"),
      4 => clienttranslate("The Mine"),
      5 => clienttranslate("The Dome"),
      6 => clienttranslate("The Virus"),
      7 => clienttranslate("The Escape"),
      8 => clienttranslate("The Battle"),
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
  public static function addScribbles(?Player $player, array $scribbles, $msg = '', $args = [])
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

  public static function useSoloBonus(Player $player, Scribble $scribble, ConstructionCard $card)
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

    self::pnotify($player, 'useSoloBonus', clienttranslate('${player_name} uses a solo bonus to discard a ${action}${action_icon} instead of giving it to ASTRA.'), [
      'player' => $player,
      'i18n' => ['action'],
      'card' => $card,
      'action' => $names[$card->getAction()],
      'action_icon' => "",
      'scribble' => $scribble,
    ]);
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

  public static function setFinalScore($player, $score)
  {
    self::notifyAll('midMessage', clienttranslate('${player_name}\'s final score is ${score}'), [
      'player' => $player,
      'score' => $score
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

  public static function crossOffFillingBonus(Player $player, Scribble $scribble, int $value, int $factoryNumber)
  {
    $msg = clienttranslate('${player_name} crosses off a filling bonus of ${value} at the factory number ${factoryNumber}');
    static::addScribble(null, $scribble, $msg, [
      'player' => $player,
      'value' => $value,
      'factoryNumber' => $factoryNumber,
    ]);
  }

  public static function circlePlant(Player $player, Scribble $scribble, int $stationNumber)
  {
    $msg = clienttranslate('${player_name} circles a plant at a station number ${stationNumber}');
    static::addScribble($player, $scribble, $msg, [
      'stationNumber' => $stationNumber
    ]);
  }

  public static function circleSingleLinked(Player $player, Scribble $scribble, int $type, int|null $value = null)
  {
    $postfixes = [
      CIRCLE_TYPE_WATER_S2 => clienttranslate('water tank'),
      CIRCLE_TYPE_RUBY => clienttranslate('ruby'),
      CIRCLE_TYPE_PEARL => clienttranslate('pearl'),
      CIRCLE_TYPE_FILLING_BONUS => clienttranslate('filling bonus'),
      CIRCLE_TYPE_WATER_S4 => clienttranslate('water'),
      CIRCLE_TYPE_PLANT_S4 => clienttranslate('plant'),
    ];
    $msg = $value ?
      clienttranslate('${player_name} stirs a water tank with the value of ${waterValue}') :
      clienttranslate('${player_name} circles an attached ${postfix}');
    static::addScribble($player, $scribble, $msg, [
      'waterValue' => $value,
      'postfix' => $postfixes[$type],
      'i18n' => ['postfix'],
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
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off an Astronaut symbol'));
  }

  public static function scribblePlanning(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off a Planning symbol'));
  }

  public static function circleGreenhouse(Player $player, array $scribbles, string $quarterName)
  {
    if (count($scribbles) === 2) {
      $msg = clienttranslate('${player_name} circles last greenhouse in the ${quarterName} quarter');
    } else {
      $msg = clienttranslate('${player_name} circles next greenhouse in the ${quarterName} quarter');
    }
    static::addScribbles($player, $scribbles, $msg, ['quarterName' => $quarterName, 'i18n' => ['quarterName']]);
  }

  //////////////////////////////////////////////////////
  //  ____                            _         _____ 
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   |___ / 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \    |_ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  ___) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |____/ 
  //////////////////////////////////////////////////////

  public static function improveBonus(Player $player, Scribble $scribble, string $bonusName, int $bonusValue)
  {
    $msg = clienttranslate('${player_name} improves ${bonusName} scoring bonus crossing off number ${bonusValue}');
    static::addScribble($player, $scribble, $msg, ['bonusName' => $bonusName, 'bonusValue' => $bonusValue]);
  }

  public static function buildRobotTunnel(Player $player, array $scribbles, int $circledAntennas)
  {
    $msg = $circledAntennas > 0 ?
      clienttranslate('${player_name} builds a pressurized tunnel and connects ${n} antenna(s)') :
      clienttranslate('${player_name} builds a pressurized tunnel to extend his network');
    self::addScribbles($player, $scribbles, $msg, ['n' => $circledAntennas]);
  }

  public static function circleAntennas(Player $player, array $scribbles)
  {
    self::addScribbles($player, $scribbles, clienttranslate('${player_name} connects ${n} antenna(s)'), ['n' => count($scribbles)]);
  }

  public static function filledQuarter(Player $player, Scribble $scribble, Quarter $quarter, bool $firstToFill)
  {
    $msg = $firstToFill ?
      clienttranslate('${player_name} have filled the ${quarter} quarter first and gets 15 points')
      : clienttranslate('${player_name} have filled the ${quarter} quarter and gets 5 points');
    static::addScribble($player, $scribble, $msg, [
      'quarter' => $quarter->getName(),
      'i18n' => ['quarter']
    ]);
  }

  public static function crossOffQuarterPoints(array $players, array $scribbles, Quarter $quarter)
  {
    static::addScribbles(null, $scribbles, clienttranslate('${players_names} crosses off the ${quarter} quarter 15 points bonus because someone else filled it during this turn'), [
      'players' => $players,
      'scribbles' => $scribbles,
      'quarter' => $quarter->getName(),
      'i18n' => ['quarter']
    ]);
  }

  public static function crossOffFilledQuarterBonusAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off one filled quarter highest bonus'));
  }

  //////////////////////////////////////////////////////
  //  ____                            _         _  _
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   | || |
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | || |_
  // ___) | (_|  __/ | | | (_| | |  | | (_) | |__   _|
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/     |_|
  //////////////////////////////////////////////////////

  public static function factoryUpgrade(Player $player, Scribble $scribble, string $factoryType, string $scribbledObject): void
  {
    $msg = clienttranslate('${player_name} upgrades ${factoryType} crossing off ${scribbledObject} in it');
    static::addScribble($player, $scribble, $msg, [
      'scribbledObject' => $scribbledObject,
      'factoryType' => $factoryType,
      'i18n' => ['scribbledObject', 'factoryType'],
    ]);
  }

  public static function finishFactory(Player $player, array $scribbles, string $factoryType, string $finishText): void
  {
    $msg = clienttranslate('${player_name} finishes upgrading ${factoryType} and ${finishText}');
    static::addScribbles($player, $scribbles, $msg, [
      'factoryType' => $factoryType,
      'finishText' => $finishText,
      'i18n' => ['factoryType', 'finishText'],
    ]);
  }

  public static function extractResourcesFromMine(Player $player, array $scribbles, int $column, array $itemsCount): void
  {
    $msg = clienttranslate('${player_name} extracts resources from column n°${column}: ${resources_desc}');

    // String resource descs
    $types = [
      RUBY => clienttranslate('${n} ruby(s)'),
      PEARL => clienttranslate('${n} pearl(s)'),
      WATER => clienttranslate('${n} water(s)'),
      PLANT => clienttranslate('${n} plant(s)'),
    ];
    $args = [];
    $logs = [];
    $i = 0;
    foreach ($itemsCount as $type => $count) {
      if ($count == 0) continue;

      $logs[] = '${resource_' . $i . '}';
      $args['resource_' . $i] = [
        'log' => $types[$type],
        'args' => [
          'n' => $count,
        ]
      ];
      $args['i18n'][] = 'resource_' . $i;
      $i++;
    }


    static::addScribbles($player, $scribbles, $msg, [
      'column' => $column + 1,
      'resources_desc' => [
        'log' => join(', ', $logs),
        'args' => $args,
      ],
      'i18n' => 'resources_desc',
    ]);
  }

  public static function crossOffFactoryBonusAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off one filling bonus of a main factory'));
  }


  //////////////////////////////////////////////////////
  //  ____                            _         ____  
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___   | ___| 
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  |___ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) |  ___) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/  |____/ 
  //////////////////////////////////////////////////////

  public static function splitDome(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} splits one dome section in two'));
  }

  public static function buildDome(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} builds one section of his dome'));
  }

  public static function s5EnergyUpgrade(Player $player, string $sectionType, array $scribbles)
  {
    $msgs = [
      'plan' => clienttranslate('${player_name} upgrades accomplished missions multipliers with energy action'),
      WATER => clienttranslate('${player_name} upgrades plant & water multipliers with energy action'),
      ASTRONAUT => clienttranslate('${player_name} upgrades astronauts multipliers with energy action'),
      'dome' => clienttranslate('${player_name} upgrades dome multiplier with energy action'),
    ];

    static::addScribbles($player, $scribbles, $msgs[$sectionType]);
  }

  public static function filledSkyscraper(Player $player, Scribble $scribble, string $msg)
  {
    static::addScribble($player, $scribble, $msg);
  }

  public static function circleSkyscraperHighMultAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} gains one solo bonus'));
  }

  public static function crossOffSkyscraperFillingBonus(Player $player, Scribble $scribble, int $value, int $skyscraperNumber)
  {
    $msg = clienttranslate('${player_name} crosses off a filling bonus of ${value} at the skyscraper number ${skyscraperNumber}');
    static::addScribble(null, $scribble, $msg, [
      'player' => $player,
      'value' => $value,
      'skyscraperNumber' => $skyscraperNumber,
    ]);
  }

  public static function crossOffSkyscraperBonusAstra(Player $player, Scribble $scribble)
  {
    static::addScribble($player, $scribble, clienttranslate('${player_name} crosses off one filling bonus of a skyscraper'));
  }

  ///////////////////////////////////////////////////////////
  //  ____                            _          __   
  // / ___|  ___ ___ _ __   __ _ _ __(_) ___    / /_  
  // \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \  | '_ \ 
  //  ___) | (_|  __/ | | | (_| | |  | | (_) | | (_) |
  // |____/ \___\___|_| |_|\__,_|_|  |_|\___/   \___/ 
  ///////////////////////////////////////////////////////////

  public static function closeWalkway(Player $player, array $scribbles)
  {
    $msg = count($scribbles) == 1 ?
      clienttranslate('${player_name} closes a walkway')
      : clienttranslate('${player_name} closes a walkway (bonus action)');

    static::addScribbles($player, $scribbles, $msg);
  }

  public static function circleEnergySymbol(Player $player, array $scribbles)
  {
    $msg = count($scribbles) == 1 ?
      clienttranslate('${player_name} circles one Energy symbol <ENERGY>')
      : clienttranslate('${player_name} circles one Energy symbol <ENERGY> (bonus action)');

    static::addScribbles($player, $scribbles, $msg);
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
      'astra' => $datas['astra'],
    ];

    self::notify($pId, 'refreshUI', '', [
      'datas' => $fDatas,
    ]);
  }

  public static function flush()
  {
    self::notifyAll('flush', '', []);
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
