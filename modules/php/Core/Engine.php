<?php

namespace Bga\Games\WelcomeToTheMoon\Core;

use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Managers\Actions;
use Bga\Games\WelcomeToTheMoon\Helpers\Log;
use Bga\Games\WelcomeToTheMoon\Game;

/*
 * Engine: a class that allows to handle complex flow
 */

class Engine
{
  public static $trees = null;
  public static $replayed = false;

  public static function invalidate()
  {
    static::$replayed = false;
    static::boot();
  }

  public static function boot()
  {
    $cPId = Players::getCurrentId(true) ?? 0;

    $flows = PGlobals::getAll('engine');
    self::$trees = [];
    foreach ($flows as $pId => $t) {
      if (empty($t)) {
        continue;
      }

      $flowTree = self::buildTree($t);
      self::$trees[$pId] = $flowTree;


      // if ($cPId == $pId && !static::$replayed) {
      if ($cPId == $pId && !static::$replayed && Globals::getMode() != MODE_APPLY) {
        static::$replayed = true;
        Globals::setReplayMode();
        $flowTree->replay();
        Globals::unsetReplayMode();
      }
    }
  }

  public static function apply()
  {
    Log::clearCache(false);
    Globals::setMode(MODE_APPLY);
    foreach (self::$trees as $pId => $t) {
      $t->replay();
      PGlobals::setEngine($pId, []);
      //      Game::get()->sendNotifications(); TODO : put back to avoid too big notifs
    }
    Notifications::scores();
    Log::clearUndoableStepNotifications();
  }

  /**
   * Save current tree into Globals table
   */

  public static function save($pId)
  {
    $t = self::$trees[$pId]->toArray();
    PGlobals::setEngine($pId, $t);
  }

  /**
   * Setup the engine, given an array representing a tree
   * @param array $t
   */
  public static bool $multipleSetupCalled = false;
  public static function multipleSetup($aTrees, $callback, $descPrefix = '')
  {
    static::$multipleSetupCalled = true; // Useful for private args computaiton
    Globals::setEngineWaitingDescriptionSuffix($descPrefix);
    Globals::setCallbackEngineResolved($callback);
    $allPIds = Players::getAll()->getIds();
    $pIds = array_keys($aTrees);
    if (empty($pIds)) {
      self::callback();
      return;
    }

    // Clear existing engines
    foreach ($allPIds as $pId) {
      PGlobals::setEngine($pId, []);
      PGlobals::setEngineChoices($pId, 0);
    }

    $realPIds = [];
    self::$trees = [];
    foreach ($pIds as $pId) {
      // Build the tree while enforcing $pId at root
      $aTree = $aTrees[$pId];
      if (empty($aTree)) {
        continue;
      }
      $realPIds[] = $pId;
      $aTree['pId'] = $pId;
      $tree = self::buildTree($aTree);
      if (!$tree instanceof \Bga\Games\WelcomeToTheMoon\Core\Engine\SeqNode) {
        $tree = new \Bga\Games\WelcomeToTheMoon\Core\Engine\SeqNode(['pId' => $pId], [$tree]);
      }

      // Save it
      self::$trees[$pId] = $tree;
      PGlobals::setEngine($pId, $tree->toArray());
      PGlobals::setEngineChoices($pId, 0);
      Game::get()->giveExtraTime($pId);
    }
    if (empty($realPIds)) {
      self::callback();
      return;
    }

    $gm = Game::get()->gamestate;
    $gm->jumpToState(ST_GENERIC_NEXT_PLAYER);
    $gm->setPlayersMultiactive($realPIds, '', true);
    $gm->jumpToState(ST_SETUP_PRIVATE_ENGINE);
    $gm->initializePrivateStateForPlayers($realPIds);
    Globals::setMode(MODE_PRIVATE);
    self::multipleProceed($realPIds);
    Log::startEngine();
  }

  public static function setup($t, $callback, $descPrefix = '', $pIds = null)
  {
    $allPIds = Players::getAll()->getIds();
    $pIds = $pIds ?? $allPIds;
    $aTrees = [];
    foreach ($pIds as $pId) {
      $aTrees[$pId] = $t;
    }

    self::multipleSetup($aTrees, $callback, $descPrefix);
  }

  /**
   * Convert an array into a tree
   * @param array $t
   */
  public static function buildTree($t)
  {
    $t['childs'] = $t['childs'] ?? [];
    $type = $t['type'] ?? (empty($t['childs']) ? NODE_LEAF : NODE_SEQ);

    $childs = [];
    foreach ($t['childs'] as $child) {
      $childs[] = self::buildTree($child);
    }

    $className = '\Bga\Games\WelcomeToTheMoon\Core\Engine\\' . ucfirst($type) . 'Node';
    unset($t['childs']);
    return new $className($t, $childs);
  }

  /**
   * Recursively compute the next unresolved node we are going to address
   */
  public static function getNextUnresolved($pId)
  {
    return isset(self::$trees[$pId]) ? self::$trees[$pId]->getNextUnresolved() : null;
  }

  /**
   * Change state
   */
  protected static function setState($pId, $newState, $globalOnly = false)
  {
    PGlobals::setState($pId, $newState);
    if (!$globalOnly) {
      Game::get()->gamestate->setPrivateState($pId, $newState);
    }
  }

  /**
   * Proceed to next unresolved part of tree
   */
  public static function multipleProceed($pIds)
  {
    foreach ($pIds as $pId) {
      self::proceed($pId);
    }
  }

  public static function proceed($pId, $confirmedPartial = false, $isUndo = false)
  {
    $node = self::getNextUnresolved($pId);

    // Are we done ?
    if ($node == null) {
      if (PGlobals::getEngineChoices($pId) == 0) {
        self::confirm($pId); // No choices were made => auto confirm
      } else {
        // Confirm/restart
        self::setState($pId, ST_CONFIRM_TURN);
      }
      return;
    }

    $player = Players::get($pId);
    if ($confirmedPartial) {
      Log::checkpoint($pId);
      Globals::setEngineChoices(0);
    }

    // If node with choice, switch to choice state
    $choices = $node->getChoices($player);
    $allChoices = $node->getChoices($player, true);

    if (!empty($allChoices) && $node->getType() != NODE_LEAF) {
      // Only one choice : auto choose
      $id = array_keys($choices)[0] ?? null;
      if (
        count($choices) == 1 &&
        count($allChoices) == 1 &&
        array_keys($allChoices) == array_keys($choices) &&
        //count($node->getChilds()) == 1 && => THIS LINE TOGGLE WHETHER IT MANDATORY TO CLICK ON THE SINGLE EFFECT OR NOT
        !$choices[$id]['irreversibleAction']
      ) {
        self::chooseNode($player, $id, true);
      } else {
        // Otherwise, go in the RESOLVE_CHOICE state
        self::setState($pId, ST_RESOLVE_CHOICE);
      }
    } else {
      // No choice => proceed to do the action
      var_dump($allChoices);
      self::proceedToAction($pId, $node, $isUndo);
    }
  }

  public static function proceedToAction($pId, $node, $isUndo = false)
  {
    $actionId = $node->getAction();
    if (is_null($actionId)) {
      throw new \BgaVisibleSystemException('Trying to get action on a leaf without action');
    }

    $player = Players::get($pId);
    // Do some pre-action code if needed and if we are not undoing to an irreversible node
    if (!$isUndo || !$node->isIrreversible($player)) {
      Actions::stPreAction($actionId, $node);
    }

    $state = Actions::getState($actionId, $node);
    if (is_null($state)) {
      die('Action without state. Engine.php');
    }
    self::setState($pId, $state);
  }

  /**
   * Get the list of choices of current node
   */
  public static function getNextChoice($player, $displayAllChoices = false)
  {
    $node = self::getNextUnresolved($player->getId());
    return $node->getChoices($player, $displayAllChoices);
  }

  /**
   * Choose one option
   */
  public static function chooseNode($player, $nodeId, $auto = false)
  {
    $pId = $player->getId();
    $node = self::getNextUnresolved($pId);
    $args = $node->getChoices($player);
    if (!isset($args[$nodeId])) {
      throw new \BgaVisibleSystemException('This choice is not possible');
    }

    if (!$auto) {
      PGlobals::incEngineChoices($pId);
      Log::step();
    }

    if ($nodeId == PASS) {
      $node->resolve(PASS);
      self::save($pId);
      self::proceed($pId);
      return;
    }

    if ($node->getChilds()[$nodeId]->isResolved()) {
      throw new \BgaVisibleSystemException('Node is already resolved');
    }
    $node->choose($nodeId, $auto);
    self::save($pId);
    self::proceed($pId);
  }

  /**
   * Resolve action : resolve the action of a leaf action node
   */
  public static function resolveAction($args = [], $checkpoint = false, &$node = null, $automatic = false)
  {
    if (is_null($node)) {
      die('Not possible');
    }

    // Resolve node
    $node->resolveAction($args);
    if ($node->isResolvingParent()) {
      $node->getParent()->resolve([]);
    }

    // Save
    $pId = $node->getRoot()->getPId();
    self::save($pId);

    if (!$automatic) {
      PGlobals::incEngineChoices($pId);
    }
    if ($checkpoint) {
      self::checkpoint($pId);
    }
  }

  public static function checkpoint($pId)
  {
    PGlobals::setEngineChoices($pId, 0);
    Log::checkpoint($pId);
  }

  /**
   * Insert a new node right before current pending node
   */
  public static function insertBeforeCurrent($pId, $t)
  {
    $node = self::getNextUnresolved($pId);

    // NULL => insert at root
    if (is_null($node)) {
      $node = self::$trees[$pId];
      $node->pushChild(self::buildTree($t));
    }
    // Parallel/Or node => just append and call chooseNode
    elseif ($node instanceof \Bga\Games\WelcomeToTheMoon\Core\Engine\ParallelNode || $node instanceof \Bga\Games\WelcomeToTheMoon\Core\Engine\OrNode) {
      $node->pushChild(self::buildTree($t));
      $node->choose(count($node->getChilds()) - 1, true);
    }
    // Otherwise => check parent
    else {
      $parent = $node->getParent();

      // Seq node => just insert before current node
      if ($parent instanceof \Bga\Games\WelcomeToTheMoon\Core\Engine\SeqNode) {
        $index = $node->getIndex() - 1;
        $parent->insertChildAtPos(self::buildTree($t), $index);
      }
      // Other case : try to insert a SEQ node on top of it
      else {
        $node2 = self::buildTree([
          'type' => NODE_SEQ,
          'childs' => [$node->toArray()],
        ]);
        $parent = $node->replace($node2);
        $index = -1;
        $parent->insertChildAtPos(self::buildTree($t), $index);
      }
    }

    self::save($pId);
  }

  /**
   * insertAsChild: turn the node into a SEQ if needed, then insert the flow tree as a child
   */
  public static function insertAsChild($t, &$node)
  {
    if (is_null($t)) {
      return;
    }

    // If the node is an action leaf, turn it into a SEQ node first
    if ($node->getType() == NODE_LEAF) {
      $newNode = $node->toArray();
      $newNode['type'] = NODE_SEQ;
      $node = $node->replace(self::buildTree($newNode));
    }

    // Push child
    $pId = $node->getRoot()->getPId();
    $node->pushChild(self::buildTree($t));
    self::save($pId);
  }

  /**
   * insertOrUpdateParallelChilds:
   *  - if the node is a parallel node => insert all the nodes as childs
   *  - if one of the child is a parallel node => insert as their childs instead
   *  - otherwise, make the action a parallel node
   */

  public static function insertOrUpdateParallelChilds($childs, &$node)
  {
    if (empty($childs)) {
      return;
    }

    $pId = $node->getRoot()->getPId();
    if ($node->getType() == NODE_SEQ) {
      // search if we have children and if so if we have a parallel node
      foreach ($node->getChilds() as $child) {
        if ($child->getType() == NODE_PARALLEL) {
          foreach ($childs as $newChild) {
            $child->pushChild(self::buildTree($newChild));
          }
          self::save($pId);
          return;
        }
      }

      $node->pushChild(
        self::buildTree([
          'type' => \NODE_PARALLEL,
          'childs' => $childs,
        ])
      );
    }
    // Otherwise, turn the node into a PARALLEL node if needed, and then insert the childs
    else {
      // If the node is an action leaf, turn it into a Parallel node first
      if ($node->getType() == NODE_LEAF) {
        $newNode = $node->toArray();
        $newNode['type'] = NODE_PARALLEL;
        $node = $node->replace(self::buildTree($newNode));
      }

      // Push childs
      foreach ($childs as $newChild) {
        $node->pushChild(self::buildTree($newChild));
      }
      self::save($pId);
    }
  }

  /**
   * Confirm the full resolution of current flow
   */
  public static function confirm($pId)
  {
    $node = self::getNextUnresolved($pId);
    // Are we done ?
    if ($node != null) {
      var_dump($node);
      throw new \feException("You can't confirm an ongoing turn");
    }

    // Make him inactive
    Game::get()->gamestate->setPlayerNonMultiactive($pId, 'done');
  }

  public static function callback()
  {
    // Callback
    $callback = Globals::getCallbackEngineResolved();
    if (isset($callback['state'])) {
      Game::get()->gamestate->jumpToState($callback['state']);
    } elseif (isset($callback['order'])) {
      Game::get()->nextPlayerCustomOrder($callback['order']);
    } elseif (isset($callback['method'])) {
      $name = $callback['method'];
      Game::get()->$name();
    }
  }

  /**
   * Restart the whole flow
   */
  public static function restart($pId)
  {
    Log::undoTurn($pId);

    $flow = PGlobals::getEngine($pId);
    self::$trees[$pId] = self::buildTree($flow);

    self::proceed($pId, false, true);
    Notifications::flush();
  }

  /**
   * Restart at a given step
   */
  public static function undoToStep($pId, $stepId)
  {
    Log::undoToStep($pId, $stepId);

    // Force to clear cached informations
    self::proceed($pId, false, true);
    Notifications::flush();
  }

  /**
   * Clear all nodes related to the current active zombie player
   */
  public static function clearZombieNodes($pId)
  {
    Game::get()->gamestate->setPlayerNonMultiactive($pId, 'done');
  }
}
