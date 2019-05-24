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
      '#markup' => '<p class="degree-verification-title">Degree Verification</p>',
      '#attached' => ['library' => ['ro_ediploma/ro-ediploma']],
      '#cache' => ['max-age' => 0],
    ];
  }

}
