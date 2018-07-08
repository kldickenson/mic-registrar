<?php

namespace Drupal\ro_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides Google CSE Form Block.
 *
 * @Block(
 *   id = "google_search_form_block",
 *   admin_label = @Translation("Google search form block"),
 *   category = @Translation("Search"),
 * )
 */
class SearchFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // Grab RO search configuration.
    $config = \Drupal::config('ro_search.settings');

    if ($config) {
      // Pass the CSE ID to JavaScript.
      $build['#attached']['drupalSettings']['google_cse_id'] = $config->get('google_cse_id');

      // Add the Google CSE JavaScript.
      $build['#attached']['library'][] = 'ro_search/ro_search';

      // Render out the search form.
      $build['search_form'] = [
        '#type' => 'inline_template',
        '#template' => '<gcse:searchbox-only resultsUrl="' . $config->get('results_page') . '"></gcse:searchbox-only>',
      ];
    }

    return $build;
  }

}
