<?php

namespace Drupal\ro_ediploma\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class EDiplomaController.
 */
class EDiplomaController extends ControllerBase {

  /**
   * Ediploma.
   *
   * @return array
   *   Returns empty array.
   */
  public function ediploma() {
    return [
      '#cache' => ['max-age' => 0],
    ];
  }

}
