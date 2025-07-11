<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class CircleSymbol extends Action
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
    return !is_null($this->getLinkedSlot($player));
  }

  private array $waterTanksAtSlots = [
    1 => 136,
    7 => 137,
    13 => 138,
    20 => 139,
    23 => 140,
    34 => 141,
    37 => 142,
    43 => 143,
    47 => 144,
    53 => 145,
  ];

  private array $plantsByQuarter = [
    [120],
    [121],
    [122, 123],
    [124],
    [125, 126],
    [127, 128],
    [129, 130],
    [131],
    [132, 133],
    [134],
    [135]
  ];

  // End lines : scoremarker slot / bonus slot / type of bonus
  private array $linkedBonus = [
    169 => [194, 205, S6_CLOSE_WALKWAY],
    171 => [195, 206, S6_CLOSE_WALKWAY],
    174 => [196, 207, S6_CLOSE_WALKWAY],
    177 => [197, 208, S6_CLOSE_WALKWAY],
    180 => [198, 209, S6_CLOSE_WALKWAY],
    183 => [199, 210, S6_CLOSE_WALKWAY],

    185 => [200, 211, S6_CIRCLE_ENERGY],
    187 => [201, 212, S6_CIRCLE_ENERGY],
    189 => [202, 213, S6_CIRCLE_ENERGY],
    191 => [203, 214, S6_CIRCLE_ENERGY],
    193 => [204, 215, S6_CIRCLE_ENERGY],
    195 => [205, 216, S6_CIRCLE_ENERGY]
  ];


  private function getLinkedSlot(Player $player): ?int
  {
    $slot = $this->getCtxArg('slot');
    $quarter = intdiv($slot - 1, 5);
    $scoresheet = $player->scoresheet();

    // Water
    if ($this->getCtxArg('type') == WATER) {
      return $this->waterTanksAtSlots[$slot] ?? null;
    } // Plant
    else {
      foreach ($this->plantsByQuarter[$quarter] as $slot) {
        if (!$scoresheet->hasScribbledSlot($slot, SCRIBBLE_CIRCLE)) {
          return $slot;
        }
      }
    }
    return null;
  }

  public function getDescription(): string
  {
    $symbol = $this->getCtxArg('type');
    return [
      WATER => clienttranslate('Circle a water symbol'),
      PLANT => clienttranslate('Circle a plant symbol'),
    ][$symbol];
  }

  public function stCircleSymbol()
  {
    return [];
  }

  public function actCircleSymbol()
  {
    $player = $this->getPlayer();
    $type = $this->getCtxArg('type');
    $scoresheet = $player->scoresheet();
    $scribbles = [];

    // Scribble in the quarter
    $slot = $this->getLinkedSlot($player);
    $scribbles[] = $scoresheet->addScribble($slot, SCRIBBLE_CIRCLE);

    // Scribble on the bottom of the scoresheet
    $section = [WATER => 'watermarkers', PLANT => 'plantmarkers'][$type];
    $scoreSlot = $scoresheet->getFirstUnscribbled($scoresheet->getSectionSlots($section));
    $scribbles[] = $scoresheet->addScribble($scoreSlot, SCRIBBLE);
    $msg = [
      WATER => clienttranslate('${player_name} circles a water tank'),
      PLANT => clienttranslate('${player_name} circles a plant')
    ][$type];

    // Any linked end-of-line bonus?
    $bonus = $this->linkedBonus[$scoreSlot] ?? null;
    if (!is_null($bonus)) {
      [$markerSlot, $bonusSlot, $bonusType] = $bonus;
      // Mark the score
      $scribbles[] = $scoresheet->addScribble($markerSlot);
      // Add the action
      $this->insertAsChild([
        'action' => $bonusType,
        'args' => ['bonusSlot' => $bonusSlot]
      ]);
      $msg = [
        WATER => clienttranslate('${player_name} circles a water tank, finishes a scoring line and gains an energy bonus'),
        PLANT => clienttranslate('${player_name} circles a plant, finishes a scoring line and gains a robot bonus')
      ][$type];
    }

    $virusType = $scoresheet->getVirusLinkedToPlantOrWater($scoreSlot);
    if (!is_null($virusType)) {
      $infos = Scoresheet6::getViruses()[$virusType];
      [$linkedVirusSlot, $virusSlot] = $infos;
      // Cross the slot
      $scribbles[] = $scoresheet->addScribble($linkedVirusSlot);
      // Activate the virus
      $scribbles[] = $scoresheet->addScribble($virusSlot, SCRIBBLE_CIRCLE);
      // Register for phase5
      $viruses = Globals::getActivatedViruses();
      $viruses[] = $virusType;
      Globals::setActivatedViruses($viruses);

      $msg = [
        VIRUS_BLUE => clienttranslate('${player_name} circles a water tank and activates the blue virus!'),
        VIRUS_GREEN => clienttranslate('${player_name} circles a plant and activates the green virus!')
      ][$virusType];
    }

    // Linked propagations
    $linkedPropagationSlot = [175 => 217, 178 => 218, 188 => 220, 190 => 221][$scoreSlot] ?? null;
    if (!is_null($linkedPropagationSlot)) {
      // Cross the slot
      $scribbles[] = $scoresheet->addScribble($linkedPropagationSlot, SCRIBBLE);
      // Register for phase5 (race slot)
      $scoresheet->prepareForPhaseFive(['slot' => $linkedPropagationSlot]);

      $msg = [
        WATER => clienttranslate('${player_name} circles a water tank and triggers a propagation for everyone else!'),
        PLANT => clienttranslate('${player_name} circles a plant  and triggers a propagation for everyone else!')
      ][$type];
    }

    Notifications::addScribbles($player, $scribbles, $msg);
  }
}
