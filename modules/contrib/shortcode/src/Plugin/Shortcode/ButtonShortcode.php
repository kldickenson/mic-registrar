<?php
/**
 * @file
 * Contains \Drupal\shortcode\Plugin\Shortcode\ButtonShortcode.
 */

namespace Drupal\shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * The image shortcode.
 *
 * @Shortcode(
 *   id = "button",
 *   title = @Translation("Button"),
 *   description = @Translation("Insert a link formatted like a button.")
 * )
 */
class ButtonShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

    // Merge with default attributes.
    $attributes = $this->getAttributes(array(
      'path' => '<front>',
      'url' => '',
      'title' => '',
      'class' => '',
      'id' => '',
      'style' => '',
    ),
      $attributes
    );

    $url = $this->getUrlFromPath($attributes['path'], $attributes['url']);
    $title = $this->getTitleFromAttributes($attributes['title'], $text);
    $class = $this->addClass($attributes['class'], 'button');

    // Build element attributes to be used in twig.
    $element_attributes = [
      'href' => $url,
      'class' => $class,
      'id' => $attributes['id'],
      'style' => $attributes['style'],
      'title' => $title,
    ];

    // Filter away empty attributes.
    $element_attributes = array_filter($element_attributes);

    $output = [
      '#theme' => 'shortcode_button',
      '#url' => $url, // Not required for rendering, just for extra context.
      '#attributes' => $element_attributes,
      '#text' => $text,
    ];

    return $this->render($output);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = array();
    $output[] = '<p><strong>' . $this->t('[button path="path" (class="additional class")]text[/button]') . '</strong> ';
    if ($long) {
      $output[] = $this->t('Inserts a link formatted like as a button. The <em>path</em> parameter provides the link target (the default is the front page).
    The <em>title</em> will be formatted as a link title (small tooltip over the link - helps for SEO).
    Additional class names can be added by the <em>class</em> parameter.') . '</p>';
    }
    else {
      $output[] = $this->t('Inserts a link formatted as a button. Use the url parameter for the link.') . '</p>';
    }
    return implode(' ', $output);
  }
}