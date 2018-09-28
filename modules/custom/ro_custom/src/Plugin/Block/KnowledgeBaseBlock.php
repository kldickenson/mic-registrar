<?php

namespace Drupal\ro_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Utility\Html;

/**
 * Provides a knowledge base block that adjusts to specific questions.
 *
 * @Block(
 *     id = "knowledge_base_block",
 *     admin_label = @Translation("Knowledge Base"),
 *     category = @Translation("Registrars Office Custom"),
 * )
 */
class KnowledgeBaseBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $answer = \Drupal::request()->get('ansid');
    $category = \Drupal::request()->get('category');

    if ($answer) {
      $url = 'https://umich-regoff.custhelp.com/app/answers/detail/a_id/' . HTML::escape($answer);
    }
    elseif ($category) {
      $url = 'https://umich-regoff.custhelp.com/app/answers/list/c/' . HTML::escape($category);
    }
    else {
      $url = 'https://umich-regoff.custhelp.com/app/answers/list';
    }

    return [
      '#cache' => ['contexts' => [
        'url.query_args:ansid',
        'url.query_args:category',
      ]],
      '#type' => 'inline_template',
      '#template' => '<iframe frameborder="0" height="1500" id="rnFrame" name="FRAME1" scrolling="no" src="' . $url . '" width="100%"></iframe>',
    ];
  }
}
