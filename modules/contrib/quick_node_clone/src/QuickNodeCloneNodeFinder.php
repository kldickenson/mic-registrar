<?php

namespace Drupal\quick_node_clone;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class QuickNodeCloneNodeFinder {

  /**
   * Derive node data from the current path.
   *
   * @return $entity|NULL
   */
  public function findNodeFromCurrentPath() {
    $path = \Drupal::request()->getRequestUri();
    $path_data = explode('/', $path);

    if ($this->currentPathIsValidClonePath()) {
      // By this point, we should be on a quick node clone path.
      $node_path = '/node/' . $path_data[2];

      return $this->findNodeFromPath($node_path);
    }
    return NULL;
  }

  /**
   * Derive node data from a given path.
   *
   * @param $path
   *   The drupal path, e.g. /node/2.
   * @param $options array
   *   The options passed to the path processor.
   *
   * @return $entity|NULL
   */
  public function findNodeFromPath($path, $options = NULL) {
    $entity = NULL;

    $type = 'node';
    $links = $this->getLinksByType($type);

    // Check that the route pattern is an entity template.

    $parts = explode('/', $path);
    $i = 0;
    foreach ($parts as $part) {
      if (!empty($part)) {
        $i++;
      }
      if ($part == $type) {
        break;
    exit;
      }
    }
    $i++;
    // Get entity path if alias.
    $entity_path = \Drupal::service('path.alias_manager')->getPathByAlias($path);

    // Look! We're using arg() in Drupal 8 because we have to.
    $args = explode('/', $entity_path);

    if (isset($args[$i])) {
      $entity = \Drupal::entityTypeManager()->getStorage($type)->load($args[$i]);
    }
    if(isset($args[$i - 1]) && $args[$i - 1] != 'node') {
      $entity = \Drupal::entityTypeManager()->getStorage($type)->load($args[$i - 1]);
    }
    return $entity;
  }

  /**
   * Get entity links, given an entity type.
   *
   * @param $type
   *   The drupal path, e.g. 'taxonomy_term'.
   *
   * @return $entity_links|NULL
   */
  public function getLinksByType($type) {
    $entity_manager = \Drupal::entityTypeManager();
    $entity_type = $entity_manager->getDefinition($type);
    return $entity_type->getLinkTemplates();
  }

  /**
   * Determine if the current page path is a valid quick node clone path.
   *
   * @return bool
   */
  public function currentPathIsValidClonePath() {
    $path = \Drupal::request()->getRequestUri();
    $path_data = explode('/', $path);

    if (!isset($path_data[1]) || $path_data[1] != 'clone') {
      return FALSE;
    }
    if (!isset($path_data[2])) {
      return FALSE;
    }

    if (!isset($path_data[3]) || $path_data[3] != 'quick_clone') {
      return FALSE;
    }
    return TRUE;
  }

}