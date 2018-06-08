<?php

/**
 * @file
 * Contains \Drupal\views_ical\Plugin\views\style\Ical.
 */

namespace Drupal\views_ical\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render an iCal feed.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "ical",
 *   title = @Translation("iCal Feed"),
 *   help = @Translation("Display the results as an iCal feed."),
 *   theme = "views_view_ical",
 *   display_types = {"feed"}
 * )
 */
class Ical extends StylePluginBase {
  protected $usesFields = TRUE;
  protected $usesGrouping = FALSE;
  protected $usesRowPlugin = TRUE;
  protected $usesOptions = FALSE;
}
