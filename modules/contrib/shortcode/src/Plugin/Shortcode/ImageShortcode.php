<?php
/**
 * @file
 * Contains \Drupal\shortcode\Plugin\Shortcode\ImageShortcode.
 */

namespace Drupal\shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * The image shortcode.
 *
 * @Shortcode(
 *   id = "img",
 *   title = @Translation("Image"),
 *   description = @Translation("Show an image.")
 * )
 */
class ImageShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

    // Merge with default attributes.
    $attributes = $this->getAttributes(array(
      'class' => '',
      'alt' => '',
      'src' => '',
    ),
      $attributes
    );

    $class = $this->addClass($attributes['class'], 'img');

    $output = array(
      '#theme' => 'shortcode_img',
      '#src' => $attributes['src'],
      '#class' => $class,
      '#alt' => $attributes['alt'],
    );

    return $this->render($output);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = array();
    $output[] = '<p><strong>' . $this->t('[img scr="image.jpg" (class="additional class"|alt="alt text")/]') . '</strong> ';
    $output[] = $this->t('Inserts an image based on the given image url.') . '</p>';
    return implode(' ', $output);
  }
}