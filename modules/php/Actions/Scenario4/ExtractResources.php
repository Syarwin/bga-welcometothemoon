<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario4;

use Bga\Games\WelcomeToTheMoon\Actions\GenericPickSlot;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Player;

class ExtractResources extends GenericPickSlot
{
  public function getState(): int
  {
    return ST_GENERIC_AUTOMATIC;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return true;
  }

  public function isDoable(Player $player): bool
  {
    return true;
  }

  public function getDescription(): string
  {
    return clienttranslate('Extract resources');
  }

  public function stExtractResources()
  {
    return [];
  }

  public function actExtractResources()
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $slots = $this->getCtxArg('slots');
    $column = $this->getCtxArg('column');

    $map = [
      CIRCLE_TYPE_RUBY => RUBY,
      CIRCLE_TYPE_PEARL => PEARL,
      WATER => WATER,
      PLANT => PLANT,
    ];
    $itemsCount = [RUBY => 0, PEARL => 0, WATER => 0, PLANT => 0];
    $slotIds = [];
    $scribbles = [];
    $reactions = [];
    foreach ($slots as $infos) {
      $slotId = $infos['slot'];
      $type = $infos['type'];

      if (in_array($slotId, $slotIds)) continue; // Avoid duplicate
      $slotIds[] = $slotId;
      if (!$scoresheet->hasScribbledSlot($slotId)) continue;

      // Scribble the resource on the column
      $scribbles[] = $scoresheet->addScribble($slotId);

      // Scribble the corresponding factory
      $factoryType = $map[$type];
      $itemsCount[$factoryType]++;
      $section = $scoresheet->getFactorySection($factoryType);
      $factorySlot = $scoresheet->getFirstUnscribbled($scoresheet->getSectionSlots($section));
      $scribble = $scoresheet->addScribble($factorySlot);
      $scribbles[] = $scribble;
      $reactions = array_merge($reactions, $scoresheet->getScribbleReactions($scribble, 'stExtractResources'));
    }

    // Scribble the top thing
    $extractPumpSlotId = 232 + $column;
    $scribbles[] = $scoresheet->addScribble($extractPumpSlotId);

    Notifications::extractResourcesFromMine($player, $scribbles, $column, $itemsCount);
  }
}
