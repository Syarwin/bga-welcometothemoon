<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario4;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class FactoryUpgrade extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_S4_FACTORY_UPGRADE;
  }

  protected function getSlots(Player $player): array
  {
    $section = $this->getCtxArg('type') === ROBOT ? 'robotmarkers' : 'energymarkers';
    return $player->scoresheet()->getSectionSlots($section);
  }

  /**
   * @throws \BgaVisibleSystemException
   */
  public function actFactoryUpgrade(int $slot)
  {
    $this->sanityCheck($slot);

    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();

    $scribble = $scoresheet->addScribble($slot);

    $scribbledObject = $this->getCtxArg('type') === ROBOT ? clienttranslate('a Robot') : clienttranslate('an Energy');
    $factory = $this->getFactoryBySlot($slot);
    $factoryTypeText = $this->getFactoryTypeForNotification($factory['type']);
    Notifications::factoryUpgrade($player, $scribble, $factoryTypeText, $scribbledObject);

    if ($scoresheet->countScribbledSlots($factory['slots']) === count($factory['slots'])) {
      $factoryBonus = $factory['bonus'];
      $actions = $factoryBonus['actions'] ?? null;
      $bonusesScribbles = [];
      if ($actions) {
        foreach ($actions as $action) {
          $this->insertAsChild($action);
        }
      } else {
        foreach ($factoryBonus['slots'] as $slot) {
          $bonusesScribbles[] = $player->scoresheet()->addScribble($slot);
        }
      }

      $bonusesScribbles = array_merge($bonusesScribbles, array_map(function ($slot) use ($player) {
        return $player->scoresheet()->addScribble($slot);
      }, $factoryBonus['slots']));
      Notifications::finishFactory($player, $bonusesScribbles, $factoryTypeText, $this->getFactoryUpgradeText($factory['type']));
    }
  }

  /**
   * @throws \BgaVisibleSystemException
   */
  private function getFactoryBySlot(int $slot): array
  {
    foreach (self::$factories as $index => $factoryData) {
      if (in_array($slot, $factoryData['slots'])) {
        return self::$factories[$index];
      }
    }
    throw new \BgaVisibleSystemException('findFactoryBySlot: no factory found for slot id ' . $slot);
  }

  private static array $factories = [
    ['type' => FACTORY_TYPE_MAIN, 'slots' => [85, 69, 70, 71], 'bonus' => ['slots' => [218]]],
    ['type' => FACTORY_TYPE_MAIN, 'slots' => [86, 72, 73], 'bonus' => ['slots' => [219]]],
    ['type' => FACTORY_TYPE_MAIN, 'slots' => [87, 88, 74], 'bonus' => ['slots' => [220]]],
    ['type' => FACTORY_TYPE_MAIN, 'slots' => [89, 90, 75, 76], 'bonus' => ['slots' => [221]]],
    ['type' => FACTORY_TYPE_ASTRONAUT, 'slots' => [91, 77], 'bonus' => ['slots' => [222]]],
    ['type' => FACTORY_TYPE_PLANNING, 'slots' => [92, 78], 'bonus' => ['slots' => [223]]],
    [
      'type' => FACTORY_TYPE_SECONDARY,
      'slots' => [79, 63],
      'bonus' => [
        'actions' => [
          [
            'action' => CIRCLE_NEXT_IN_ROW,
            'args' => [
              'symbol' => CIRCLE_SYMBOL_PLANT,
              'section' => 'plants',
              'amount' => 2,
            ]
          ]
        ],
        'slots' => [147, 148],
      ]
    ],
    [
      'type' => FACTORY_TYPE_SECONDARY,
      'slots' => [80, 64, 65],
      'bonus' => [
        'actions' => [
          [
            'action' => CIRCLE_NEXT_IN_ROW,
            'args' => [
              'symbol' => CIRCLE_SYMBOL_WATER,
              'section' => 'waters',
              'amount' => 2,
            ]
          ]
        ],
        'slots' => [149, 150],
      ]
    ],
    [
      'type' => FACTORY_TYPE_SECONDARY,
      'slots' => [81, 66, 67],
      'bonus' => [
        'actions' => [
          [
            'action' => CIRCLE_NEXT_IN_ROW,
            'args' => [
              'symbol' => CIRCLE_SYMBOL_PEARL,
              'section' => 'pearls',
              'amount' => 2,
            ]
          ]
        ],
        'slots' => [151, 152],
      ]
    ],
    [
      'type' => FACTORY_TYPE_SECONDARY,
      'slots' => [82, 68],
      'bonus' => [
        'actions' => [
          [
            'action' => CIRCLE_NEXT_IN_ROW,
            'args' => [
              'symbol' => CIRCLE_SYMBOL_RUBY,
              'section' => 'rubies',
              'amount' => 2,
            ]
          ]
        ],
        'slots' => [153, 154],
      ]
    ],
    [
      'type' => FACTORY_TYPE_SECONDARY,
      'slots' => [83, 84],
      'bonus' => [
        'actions' => [
          ['action' => S4_FACTORY_UPGRADE, 'args' => ['type' => ROBOT]],
          ['action' => S4_FACTORY_UPGRADE, 'args' => ['type' => ROBOT]],
          ['action' => S4_FACTORY_UPGRADE, 'args' => ['type' => ROBOT]],
        ],
        'slots' => [155, 156, 157],
      ]
    ],
  ];

  private function getFactoryTypeForNotification(int $factoryType): string
  {
    return [
      FACTORY_TYPE_MAIN => clienttranslate('a main factory'),
      FACTORY_TYPE_ASTRONAUT => clienttranslate('a control center for the astronauts'),
      FACTORY_TYPE_PLANNING => clienttranslate('a control center for the planning'),
      FACTORY_TYPE_SECONDARY => clienttranslate('a secondary factory'),
    ][$factoryType];
  }

  private function getFactoryUpgradeText(int $factoryType): string
  {
    return [
      FACTORY_TYPE_MAIN => clienttranslate('makes it more effective'),
      FACTORY_TYPE_ASTRONAUT => clienttranslate('starts getting points for circled off astronauts'),
      FACTORY_TYPE_PLANNING => clienttranslate('stops losing points for circled off planning'),
      FACTORY_TYPE_SECONDARY => clienttranslate('gets an instant bonus'),
    ][$factoryType];
  }
}
