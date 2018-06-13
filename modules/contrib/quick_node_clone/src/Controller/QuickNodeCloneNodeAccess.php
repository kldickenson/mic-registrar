<?php

namespace Drupal\quick_node_clone\Controller;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;

class QuickNodeCloneNodeAccess {
  /**
   * Limit access to the clone according to their restricted state.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   * @param \Drupal\node\Entity\Node $node
   */
  public function cloneNode(AccountInterface $account, $node) {
    $node = Node::load($node);
    $node_type = $node->bundle();

    if (_quick_node_clone_has_clone_permission($node_type)) {
      $result = AccessResult::allowed();
    } else {
      $result = AccessResult::forbidden();
    }

    $result->addCacheableDependency($node);

    return $result;
  }
}
