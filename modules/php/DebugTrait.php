<?php

namespace Bga\Games\WelcomeToTheMoon;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\PGlobals;
use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Managers\Players;

trait DebugTrait
{
  function tp()
  {
    // Globals::setMode(MODE_APPLY);
    // Globals::setTurn(14);
    // Globals::setMode(MODE_PRIVATE);
    $player = Players::getCurrent();
    $scoresheet = $player->scoresheet();
    var_dump($scoresheet->hasScribbledSomeSlots($scoresheet->getSectionSlots('waters'), 6));
  }


  function getMode()
  {
    var_dump(Globals::getMode());
  }


  function resetEngine()
  {
    $pId = Players::getCurrentId();
    $tree = PGlobals::getEngine($pId);
    $tree = $this->resetEngineAux($tree);
    PGlobals::setEngine($pId, $tree);
    Engine::proceed($pId);
  }
  function resetEngineAux($t)
  {
    if (isset($t['action'])) {
      unset($t['actionResolved']);
      unset($t['actionResolutionArgs']);
      unset($t['childs']);
      $t['type'] = 'leaf';
    } else {
      for ($i = 0; $i < count($t['childs'] ?? []); $i++) {
        $t['childs'][$i] = $this->resetEngineAux($t['childs'][$i]);
      }
    }
    unset($t['choices']);

    return $t;
  }

  function engDisplay()
  {
    $pId = Players::getCurrentId();
    var_dump(PGlobals::getEngine($pId));
  }

  function engProceed()
  {
    $pId = Players::getCurrentId();
    Engine::proceed($pId);
  }
}
