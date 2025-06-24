<?php

namespace Bga\Games\WelcomeToTheMoon\Actions\Scenario6;

use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Helpers\Utils;
use Bga\Games\WelcomeToTheMoon\Models\Action;
use Bga\Games\WelcomeToTheMoon\Models\Player;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet6;

class PropagateVirus extends Action
{
  public function getState(): int
  {
    return ST_S6_PROPAGATE_VIRUS;
  }

  public function getVirus(): int
  {
    $quarter = Scoresheet6::getQuarters()[$this->getCtxArg('quarter')];
    return Scoresheet6::getVirusOfQuarter($quarter[5]);
  }

  public function getVirusName(): string
  {
    $names = [
      VIRUS_BLUE => clienttranslate('the blue virus'),
      VIRUS_RED => clienttranslate('the red virus'),
      VIRUS_GREEN => clienttranslate('the green virus'),
      VIRUS_YELLOW => clienttranslate('the yellow virus'),
      VIRUS_PURPLE => clienttranslate('the purple virus'),
      VIRUS_GREY => clienttranslate('the grey virus'),
    ];

    return $names[$this->getVirus()];
  }

  public function argsPropagateVirus()
  {
    $quarters = Scoresheet6::getQuarters();
    $quarter = $quarters[$this->getCtxArg('quarter')];
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $slots = [];

    // TODO : handle quarter completely quarantined + make it automatic in this case

    // Evacuated quarter
    if ($scoresheet->hasScribbledSlot($quarter[0])) {
      // For each connected quarter
      foreach ($quarter[4] as $linkSlot => $linkedQuarter) {
        // If it's not quarantines
        if ($scoresheet->hasScribbledSlot($linkSlot)) continue;

        $quarterSlots = $quarters[$linkedQuarter][2];
        Utils::filter($quarterSlots, fn($slot) => !$scoresheet->hasScribbledSlot($slot));
        if (!empty($quarterSlots)) {
          $slots[] = $quarterSlots;
        }
      }
    }
    // Non-evacuated quarter
    else {
      $quarterSlots = $quarter[2];
      Utils::filter($quarterSlots, fn($slot) => !$scoresheet->hasScribbledSlot($slot));
      $slots[] = $quarterSlots;
    }

    return [
      'virus_name' => $this->getVirusName(),
      'i18n' => ['virus_name'],
      'slots' => $slots,
    ];
  }

  public function actPropagateVirus(array $slots)
  {
    $player = $this->getPlayer();
    $scoresheet = $player->scoresheet();
    $args = $this->getArgs();
    if (count($slots) != count($args['slots'])) {
      throw new \BgaUserException('Invalid numbers of slot. Should not happen. Action: act' . $this->getClassName());
    }

    foreach ($slots as $i => $slot) {
      if (!in_array($slot, $args['slots'][$i])) {
        throw new \BgaUserException('Invalid slot id. Should not happen. Action: act' . $this->getClassName());
      }
    }

    $scribbles = [];
    foreach ($slots as $slot) {
      $scribble = $scoresheet->addScribble($slot, SCRIBBLE);
      $scribbles[] = $scribble;
      $reactions = $player->scoresheet()->getScribbleReactions($scribble, 'actPropagateVirus');
      $this->insertAsChild($reactions);
    }

    Notifications::propagateVirus($player, $scribbles, $this->getVirusName());
  }
}
