<?php
/**
 * @file
 * Contains \Drupal\shortcode\Plugin\Shortcode\LinkShortcode.
 */

namespace Drupal\shortcode\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * Insert div or span around the text with some css classes.
 *
 * @Shortcode(
 *   id = "link",
 *   title = @Translation("Link"),
 *   description = @Translation("Makes an aliased link to the given path.")
 * )
 */
class LinkShortcode extends ShortcodeBase {

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

    if ($text) {
      $title = $this->getTitleFromAttributes($attributes['title'], $text);

      // Build element attributes to be used in twig.
      $element_attributes = [
        'href' => $url,
        'class' => $attributes['class'],
        'id' => $attributes['id'],
        'style' => $attributes['style'],
        'title' => $title,
      ];

      // Filter away empty attributes.
      $element_attributes = array_filter($element_attributes);

      $output = [
        '#theme' => 'shortcode_link',
        '#url' => $url, // Not required for rendering, just for extra context.
        '#attributes' => $element_attributes,
        '#text' => $text,
      ];
      return $this->render($output);
    }
    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = array();
    $output[] = '<p><strong>' . $this->t('[link path="the drupal path" (title="link title"|class="additional class"|id="item id"|style="css style rules")]text[/link]') . '</strong>';
    if ($long) {
      $output[] = $this->t('Inserts an aliased drupal path around the text. You can omit the text and the closing [/link], you get back the url only.') . '</p>';
      $output[] = '<p>' . $this->t('Additional class names can be added by the <em>class</em> parameter. The id parameter gives the html an unique css id. In the <em>style</em> parameter you can use your own css definition.') . '</p>';
    }
    else {
      $output[] = $this->t('Inserts an aliased drupal path around the text. You can omit the text and the closing [/link], you get back the url only.') . '</p>';
    }

    return implode(' ', $output);
  }
}