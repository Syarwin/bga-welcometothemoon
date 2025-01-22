<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

class Scoresheet
{
  protected ?Player $player;
  protected int $scenario;

  public function __construct(?Player $player)
  {
    $this->player = $player;

    // Extract info froms datas
    foreach ($this->datas['sections'] as $section) {
      $this->slotsBySection[$section['id']] = [];
      foreach ($section['elts'] as $elt) {
        $this->slotsBySection[$section['id']][] = $elt['id'];
      }
    }
  }

  public function getAvailableSlotsForNumber($number)
  {
    $allSlots = $this->slotsBySection['numbers'];

    return $allSlots;
  }

  // DATAS
  protected array $datas = [];
  protected array $slotsBySection = [];
  public function getUiData()
  {
    return $this->datas;
  }
}
