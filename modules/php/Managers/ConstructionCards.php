<?php

namespace Bga\Games\WelcomeToTheMoon\Managers;

use Bga\Games\WelcomeToTheMoon\Helpers\CachedPieces;
use Bga\Games\WelcomeToTheMoon\Models\ConstructionCard;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Helpers\Collection;

class ConstructionCards extends CachedPieces
{
  protected static string $table = 'construction_cards';
  protected static string $prefix = 'card_';
  protected static array $customFields = ['number', 'action'];
  protected static bool $autoremovePrefix = false;
  protected static bool $autoIncrement = false;
  protected static array $autoreshuffleCustom = [
    'deck' => 'discard',
    'deck-0' => 'discard-0',
    'deck-1' => 'discard-1',
    'deck-2' => 'discard-2',
  ];
  protected static bool $autoreshuffle = true;
  protected static ?array $autoreshuffleListener = ['obj' => 'Bga\Games\WelcomeToTheMoon\Managers\ConstructionCards', 'method' => 'onReshuffle'];

  protected static function cast($row): ConstructionCard
  {
    return new ConstructionCard($row);
  }

  protected static $deck = [
    1 => [1, ROBOT],
    2 => [1, ENERGY],

    3 => [2, ROBOT],
    4 => [2, PLANT],

    5 => [3, WATER],
    6 => [3, ASTRONAUT],
    7 => [3, PLANNING],

    8 => [4, ENERGY],
    9 => [4, PLANT],
    10 => [4, ASTRONAUT],
    11 => [4, PLANNING],

    12 => [5, ROBOT],
    13 => [5, ROBOT],
    14 => [5, ENERGY],
    15 => [5, ENERGY],
    16 => [5, PLANT],

    17 => [6, ROBOT],
    18 => [6, ENERGY],
    19 => [6, PLANT],
    20 => [6, WATER],
    21 => [6, ASTRONAUT],
    22 => [6, PLANNING],

    23 => [7, ROBOT],
    24 => [7, ENERGY],
    25 => [7, ENERGY],
    26 => [7, PLANT],
    27 => [7, PLANT],
    28 => [7, WATER],

    29 => [8, ROBOT],
    30 => [8, ROBOT],
    31 => [8, PLANT],
    32 => [8, PLANT],
    33 => [8, WATER],
    34 => [8, ASTRONAUT],
    35 => [8, PLANNING],

    36 => [9, ROBOT],
    37 => [9, ENERGY],
    38 => [9, ENERGY],
    39 => [9, PLANT],
    40 => [9, PLANT],
    41 => [9, WATER],

    42 => [10, ROBOT],
    43 => [10, ENERGY],
    44 => [10, PLANT],
    45 => [10, WATER],
    46 => [10, ASTRONAUT],
    47 => [10, PLANNING],

    48 => [11, ROBOT],
    49 => [11, ROBOT],
    50 => [11, ENERGY],
    51 => [11, ENERGY],
    52 => [11, PLANT],

    53 => [12, ENERGY],
    54 => [12, PLANT],
    55 => [12, ASTRONAUT],
    56 => [12, PLANNING],

    57 => [13, WATER],
    58 => [13, ASTRONAUT],
    59 => [13, PLANNING],

    60 => [14, ROBOT],
    61 => [14, PLANT],

    62 => [15, ROBOT],
    63 => [15, ENERGY],

    112 => [-1, SOLO],
    113 => [-2, SOLO],
    114 => [-3, SOLO],

    149 => [NUMBER_X, ENERGY],
    150 => [NUMBER_X, PLANT],
    151 => [NUMBER_X, ROBOT],
    152 => [0, ENERGY_WATER],
    153 => [8.5, ASTRONAUT_PLANT],
    154 => [42, ROBOT_PLANNING],
    155 => [4, JOKER],
    156 => [NUMBER_6_9, JOKER],
    157 => [12, JOKER],
  ];


  public static function onReshuffle($fromLocation)
  {
    if ($fromLocation == 'deck-0') {
      Notifications::midMessage(clienttranslate("Reshuffling decks of construction cards"));
    }
  }

  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////

  /* Creation of all cards */
  public static function setupNewGame(array $players, array $options): void
  {
    $cards = [];
    foreach (self::$deck as $id => $card) {
      $cards[] = [
        'id' => $id,
        'number' => $card[0],
        'action' => $card[1],
        'nbr' => 1,
      ];
    }

    self::create($cards, 'box');
  }


  /* Setup construction cards for a scenario */
  public static function setupScenario(): void
  {
    // Keep only relevant cards
    $ids = [];
    foreach (self::$deck as $cardId => $cardInfo) {
      if ($cardId <= 63) {
        $ids[] = $cardId;
      }
    }

    // Split intro three decks of same size
    $decks = [[], [], []];
    shuffle($ids);
    foreach ($ids as $i => $cardId) {
      $decks[$i % 3][] = $cardId;
    }

    // Solo: add solo cards in third deck and merge into a single deck
    if (Globals::isSolo()) {
      $decks[2][] = 112;
      $decks[2][] = 113;
      $decks[2][] = 114;
      for ($i = 2; $i >= 0; $i--) {
        shuffle($decks[$i]);
        foreach ($decks[$i] as $cardId) {
          self::insertOnTop($cardId, "deck");
        }
      }
    }
    // Standard: place the cards into three decks
    //  and then populate one card per stack
    else {
      foreach ($decks as $deckNumber => $cardIds) {
        self::move($cardIds, "deck-$deckNumber");
      }
      for ($i = 0; $i < 3; $i++) {
        self::shuffle("deck-$i");
      }

      self::newTurn();
      self::endOfTurn();
    }
  }

  ////////////////////////////////////
  //  _____                 
  // |_   _|   _ _ __ _ __  
  //   | || | | | '__| '_ \ 
  //   | || |_| | |  | | | |
  //   |_| \__,_|_|  |_| |_|  
  ////////////////////////////////////

  public static function newTurn()
  {
    $drawnCards = [];
    for ($i = 0; $i < 3; $i++) {
      $fromLocation = Globals::isSolo() ? "deck" : "deck-$i";
      $drawnCard = self::pickOneForLocation($fromLocation, "stack-$i", 0);
      $drawnCards[$i] = $drawnCard;
    }

    return $drawnCards;
  }

  public static function getPendingSoloCards()
  {
    return self::getInLocation("stack-%")->filter(fn($card) => $card->getAction() == SOLO);
  }

  public static function endOfTurn()
  {
    for ($i = 0; $i < 3; $i++) {
      $stack = "stack-$i";

      if (Globals::isStandard()) {
        // Standard mode : Discard last flipped card if any, flip the current construction card if any, draw a new card
        self::moveAllInLocation($stack, "discard-$i", 1);
        self::moveAllInLocation($stack, $stack, 0, 1);
      } else {
        // Discard all previously drawn cards (card given to ASTRA is handled in an atomic action)
        self::moveAllInLocation($stack, 'discard', 0);
      }
    }
  }

  /*
   * Get the content of the three stacks
   */
  public static function getUiData()
  {
    $cards = [];
    for ($i = 0; $i < 3; $i++) {
      $cards[$i] = self::getTopOf("stack-$i", 2, false)->toArray();
    }

    return $cards;
  }

  /*
   * Get all the possible combinations
   */
  public static function getPossibleCombinations($canUseJokerAction = false)
  {
    $stacks = self::getUiData();
    $baseResult = [];

    // Standard mode => on each stack, first card is for the action, second is the number
    if (Globals::isStandard()) {
      for ($i = 0; $i < 3; $i++) {
        $baseResult[] = [
          'stacks' => [$i],
          'action' => $stacks[$i][0]->getAction(),
          'number' => $stacks[$i][1]->getNumber(),
        ];
      }
    }
    // Solo mode => can pick any pair of cards
    else {
      for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 3; $j++) {
          if ($i == $j) continue;

          $baseResult[] = [
            'stacks' => [$i, $j],
            'number' => $stacks[$i][0]->getNumber(),
            'action' => $stacks[$j][0]->getAction(),
          ];
        }
      }
    }

    // Handle joker
    $result = [];
    foreach ($baseResult as $combination) {
      if ($combination['action'] == JOKER || $canUseJokerAction) {
        foreach (ALL_ACTIONS as $action) {
          $combination['action'] = $action;
          $result[] = $combination;
        }
      } else {
        $result[] = $combination;
      }
    }

    return $result;
  }
}
