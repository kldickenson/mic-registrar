<?php

namespace Drupal\linkchecker\Tests;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\filter\Entity\FilterFormat;
use Drupal\linkchecker\Entity\LinkCheckerLink;
use Drupal\linkchecker\LinkCheckerLinkInterface;
use Drupal\node\Entity\NodeType;
use Drupal\simpletest\WebTestBase;

/**
 * Test case for interface tests.
 *
 * @group linkchecker
 */
class LinkCheckerInterfaceTest extends WebTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'comment',
    'filter',
    'linkchecker',
    'node',
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $full_html_format = FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
    ]);
    $full_html_format->save();

    // Create Basic page and Article node types.
    $node_type = NodeType::create([
      'type' => 'page',
      'name' => 'Basic page',
      'format' => 'full_html',
    ]);
    $node_type->save();

    $permissions = [
      // Block permissions.
      'administer blocks',
      // Comment permissions.
      'administer comments',
      'access comments',
      'post comments',
      'skip comment approval',
      'edit own comments',
      // Node permissions.
      'create page content',
      'edit own page content',
      // Path aliase permissions.
      'administer url aliases',
      'create url aliases',
      // Content filter permissions.
      $full_html_format->getPermissionName(),
    ];

    // User to set up linkchecker.
    $this->admin_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Test node with link.
   */
  public function testLinkCheckerCreateNodeWithBrokenLinks() {

    // Configure basic settings.
    $this->config('linkchecker.settings')->set('default_url_scheme', 'http://')->save();
    $this->config('linkchecker.settings')->set('base_path', 'example.org/')->save();

    // Enable all node type page for link extraction.
    // @FIXME
    // $this->config('linkchecker.settings')->set('scan_blocks', ['filter_url' => 'filter_url'])->save();
    $this->config('linkchecker.settings')->set('check_links_types', LinkCheckerLinkInterface::TYPE_ALL)->save();

    // Core enables the URL filter for "Full HTML" by default.
    // -> Blacklist / Disable URL filter for testing.
    $this->config('linkchecker.settings')->set('extract.filter_blacklist', ['filter_url' => 'filter_url'])->save();

    // Extract from all link checker supported HTML tags.
    $this->config('linkchecker.settings')->set('extract.from_a', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_audio', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_embed', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_iframe', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_img', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_object', 1)->save();
    $this->config('linkchecker.settings')->set('extract.from_video', 1)->save();

    $url1 = 'http://example.com/node/broken/link';
    $body = 'Lorem ipsum dolor sit amet <a href="' . $url1 . '">broken link</a> sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat';

    // Save folder names in variables for reuse.
    $random = new \Drupal\Component\Utility\Random();
    $folder1 = $random->name(10);
    $folder2 = $random->name(5);

    // Fill node array.
    $edit = [];
    $edit["title[0][value]"] = $random->name(32);
    $edit["body[0][value]"] = $body;
    //$edit["body[0][format]"] = 'full_html';
    $edit['path[0][alias]'] = '/' . $folder1 . '/' . $folder2;

    // Extract only full qualified URLs.
    $this->config('linkchecker.settings')->set('check_links_types', LinkCheckerLinkInterface::TYPE_EXTERNAL)->save();

    // Verify path input field appears on add "Basic page" form.
    $this->drupalGet('node/add/page');
    // Verify path input is present.
    $this->assertFieldByName('path[0][alias]', '', 'Path input field present on add Basic page form.');

    // Save node.
    $this->drupalPostForm('node/add/page', $edit, $this->t('Save'));
    $this->assertText($this->t('@type @title has been created.', ['@type' => 'Basic page', '@title' => $edit["title[0][value]"]]), 'Node was created.');

    $node = $this->drupalGetNodeByTitle($edit[0]['title']);
    $this->assertTrue($node, 'Node found in database.');

    // Verify if the content link is extracted properly.
    $link = \Drupal::entityTypeManager()
      ->getStorage('linkcheckerlink')
      ->loadByProperties([
        'urlhash' => LinkCheckerLink::generateHash($url1)
      ]);

    if ($link) {
      $this->assertIdentical($link->url, $url1, new FormattableMarkup('URL %url found.', ['%url' => $url1]));
    }
    else {
      $this->fail(new FormattableMarkup('URL %url not found.', ['%url' => $url1]));
    }

    // Set link as failed once.
    $fail_count = 1;
    $status = 301;
    $this->setLinkAsBroken($url1, $status, $fail_count);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertRaw(\Drupal::translation()->formatPlural($fail_count, 'Link check of <a href="@url">@url</a> failed once (status code: @code).', 'Link check of <a href="@url">@url</a> failed @count times (status code: @code).', ['@url' => $url1, '@code' => $status]), 'Link check failed once found.');

    // Set link as failed multiple times.
    $fail_count = 4;
    $status = 404;
    $this->setLinkAsBroken($url1, $status, $fail_count);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertRaw(\Drupal::translation()->formatPlural($fail_count, 'Link check of <a href="@url">@url</a> failed once (status code: @code).', 'Link check of <a href="@url">@url</a> failed @count times (status code: @code).', ['@url' => $url1, '@code' => $status]), 'Link check failed multiple times found.');
  }

  /**
   * Test block with link.
   */
  public function testLinkCheckerCreateBlockWithBrokenLinks() {
    // Enable all blocks for link extraction.
    // @FIXME
    //variable_set('linkchecker_scan_blocks', 1);

    // Confirm that the add block link appears on block overview pages.
    $this->drupalGet('admin/structure/block');
    $this->assertRaw(l($this->t('Add block'), 'admin/structure/block/add'), 'Add block link is present on block overview page for default theme.');

    $url1 = 'http://example.com/block/broken/link';
    $body = 'Lorem ipsum dolor sit amet <a href="' . $url1 . '">broken link</a> sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat';

    // Add a new custom block by filling out the input form on the
    // admin/structure/block/add page.
    $random = new \Drupal\Component\Utility\Random();

    $custom_block = [];
    $custom_block['info'] = $random->name(8);
    $custom_block['title'] = $random->name(8);
    $custom_block['body[value]'] = $body;
    $custom_block['body[format]'] = 'full_html';
    $this->drupalPostForm('admin/structure/block/add', $custom_block, $this->t('Save block'));

    // Confirm that the custom block has been created, and then query the
    // created bid.
    $this->assertText($this->t('The block has been created.'), 'Custom block successfully created.');
    $bid = db_query("SELECT bid FROM {block_custom} WHERE info = :info", [':info' => $custom_block['info']])->fetchField();

    // Check to see if the custom block was created by checking that it's in the
    // database.
    $this->assertNotNull($bid, 'Custom block found in database');

    // Verify if the content link is extracted properly.
    $link = \Drupal::entityTypeManager()
      ->getStorage('linkcheckerlink')
      ->loadByProperties([
        'urlhash' => LinkCheckerLink::generateHash($url1),
      ]);

    if ($link) {
      $this->assertIdentical($link->url, $url1, new FormattableMarkup('URL %url found.', ['%url' => $url1]));
    }
    else {
      $this->fail(new FormattableMarkup('URL %url not found.', ['%url' => $url1]));
    }

    // Set link as failed once.
    $fail_count = 1;
    $status = 301;
    $this->setLinkAsBroken($url1, $status, $fail_count);
    $this->drupalGet('admin/structure/block/manage/block/' . $bid . '/configure');
    $this->assertRaw(\Drupal::translation()->formatPlural($fail_count, 'Link check of <a href="@url">@url</a> failed once (status code: @code).', 'Link check of <a href="@url">@url</a> failed @count times (status code: @code).', ['@url' => $url1, '@code' => $status]), 'Link check failed once found.');

    // Set link as failed multiple times.
    $fail_count = 4;
    $status = 404;
    $this->setLinkAsBroken($url1, $status, $fail_count);
    $this->drupalGet('admin/structure/block/manage/block/' . $bid . '/configure');
    $this->assertRaw(\Drupal::translation()->formatPlural($fail_count, 'Link check of <a href="@url">@url</a> failed once (status code: @code).', 'Link check of <a href="@url">@url</a> failed @count times (status code: @code).', ['@url' => $url1, '@code' => $status]), 'Link check failed multiple times found.');
  }

  /**
   * Set an URL as broken.
   *
   * @param string $url
   *   URL of the link to find.
   * @param int $status
   *   A fake HTTP code for testing.
   * @param int $fail_count
   *   A fake fail count for testing.
   */
  private function setLinkAsBroken($url = NULL, $status = 404, $fail_count = 0) {
    db_update('linkchecker_link')
      ->condition('urlhash', drupal_hash_base64($url))
      ->fields([
        'code' => $status,
        'error' => 'Not available (test running)',
        'fail_count' => $fail_count,
        'last_checked' => time(),
        'status' => 1,
      ])
      ->execute();
  }

}
