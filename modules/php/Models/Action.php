<?php

namespace Bga\Games\WelcomeToTheMoon\Models;

use Bga\Games\WelcomeToTheMoon\Core\Engine;
use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Managers\Players;

/*
 * Action: base class to handle atomic action
 */

class Action
{
  protected object $ctx; // Contain ctx information : current node of flow tree
  protected string $description = '';

  public function __construct(&$ctx)
  {
    $this->ctx = $ctx;
  }

  protected ?array $args = null;

  public function getArgs()
  {
    if (is_null($this->args)) {
      $methodName = 'args' . $this->getClassName();
      $this->args = \method_exists($this, $methodName) ? $this->$methodName() : [];
    }
    return $this->args;
  }

  public function getCtx()
  {
    return $this->ctx;
  }

  public function isDoable(Player $player): bool
  {
    return true;
  }

  public function isOptional(): bool
  {
    return !$this->isDoable($this->getPlayer());
  }

  public function isIndependent(?Player $player = null): bool
  {
    return false;
  }

  public function isAutomatic(?Player $player = null): bool
  {
    return false;
  }

  public function isIrreversible(?Player $player = null): bool
  {
    return false;
  }

  public function getDescription(): string|array
  {
    return $this->description;
  }

  public function getPlayer(): Player
  {
    $root = $this->ctx->getRoot();
    $pId = $root->getPId();
    if (is_null($pId)) {
      die("No PID at root for following tree : " . var_export($root->toArray(), true));
    }
    return Players::get($pId);
  }

  public function getState(): int
  {
    return 0;
  }

  /**
   * Syntaxic sugar
   */
  public function getCtxArgs(): array
  {
    if ($this->ctx == null) {
      return [];
    } elseif (is_array($this->ctx)) {
      return $this->ctx;
    } else {
      return $this->ctx->getArgs() ?? [];
    }
  }

  public function getCtxArg(string $v): mixed
  {
    return $this->getCtxArgs()[$v] ?? null;
  }

  /**
   * Insert flow as child of current node
   */
  public function insertAsChild(array $flow): void
  {
    if (empty($flow)) return;

    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertAsChild($flow, $this->ctx);
    }
  }

  /**
   * Insert childs as parallel node childs
   */
  public function pushParallelChild($node): void
  {
    $this->pushParallelChilds([$node]);
  }

  public function pushParallelChilds(array $childs): void
  {
    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertOrUpdateParallelChilds($childs, $this->ctx);
    }
  }

  public function getClassName(): string
  {
    $classname = get_class($this);
    if ($pos = strrpos($classname, '\\')) {
      return substr($classname, $pos + 1);
    }
    return $classname;
  }
}
