<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Game;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\ZooCards;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;
use Bga\Games\WelcomeToTheMoon\Helpers\FlowConvertor;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;

/*
 * Action: base class to handle atomic action
 */

class Action
{
  protected $ctx = null; // Contain ctx information : current node of flow tree
  protected $description = '';
  public function __construct(&$ctx)
  {
    $this->ctx = $ctx;
  }

  public function getCtx()
  {
    return $this->ctx;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function isOptional()
  {
    return !$this->isDoable($this->getPlayer());
  }

  public function isIndependent($player = null)
  {
    return false;
  }

  public function isAutomatic($player = null)
  {
    return false;
  }

  public function isIrreversible($player = null)
  {
    return false;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getPlayer()
  {
    $pId = $this->ctx->getRoot()->getPId();
    return Players::get($pId);
  }

  public function getState()
  {
    return null;
  }

  /**
   * Syntaxic sugar
   */
  public function getCtxArgs()
  {
    if ($this->ctx == null) {
      return [];
    } elseif (is_array($this->ctx)) {
      return $this->ctx;
    } else {
      return $this->ctx->getArgs() ?? [];
    }
  }
  public function getCtxArg($v)
  {
    return $this->getCtxArgs()[$v] ?? null;
  }

  /**
   * Insert flow as child of current node
   */
  public function insertAsChild($flow)
  {
    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertAsChild($flow, $this->ctx);
    }
  }

  /**
   * Insert childs as parallel node childs
   */
  public function pushParallelChild($node)
  {
    $this->pushParallelChilds([$node]);
  }

  public function pushParallelChilds($childs)
  {
    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertOrUpdateParallelChilds($childs, $this->ctx);
    }
  }

  public function getClassName()
  {
    $classname = get_class($this);
    if ($pos = strrpos($classname, '\\')) {
      return substr($classname, $pos + 1);
    }
    return $classname;
  }
}
