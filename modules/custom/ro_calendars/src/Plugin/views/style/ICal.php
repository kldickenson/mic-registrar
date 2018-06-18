<?php

namespace Drupal\ro_calendars\Plugin\views\style;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render an iCal feed.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "ro_ical",
 *   title = @Translation("RO iCal"),
 *   help = @Translation("Display the results as an iCal feed."),
 *   theme = "views_view_ro_ical",
 *   display_types = {"feed"}
 * )
 */
class ICal extends StylePluginBase {
  
  protected $usesFields = TRUE;

  /**
   * {@inheritdoc}
   */
  public function attachTo(array &$build, $display_id, Url $feed_url, $title) {
    $url_options = [];
    $input = $this->view->getExposedInput();

    if ($input) {
      $url_options['query'] = $input;
    }
    
    $url_options['absolute'] = TRUE;

    $url = $feed_url->setOptions($url_options)->toString();

    // Add the link to the view.
    $this->view->feedIcons[] = [
      '#markup' => '<a class="add-to-calendar" href="' . $url . '">' 
        . t('Add to Google Calendar') . '</a>',
    ];
  }
}