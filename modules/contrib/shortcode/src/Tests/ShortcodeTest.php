<?php

namespace Drupal\shortcode\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\shortcode\Shortcode\ShortcodeService;

/**
 * Tests the Drupal 8 shortcode module functionality
 *
 * @group shortcode
 */
class ShortcodeTest extends WebTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('filter', 'shortcode');

  /**
   * The shortcode service.
   *
   * @var ShortcodeService $shortcodeService
   */
  private $shortcodeService;

  /**
   * Perform any initial set up tasks that run before every test method
   */
  public function setUp() {
    parent::setUp();
    $this->shortcodeService = \Drupal::service('shortcode');
  }

  /**
   * WIP
   */
  //public function testTextFormat() {
  //  $this->drupalLogin($this->user);
  //  $this->drupalGet($path);
  //  $this->assertResponse(200);
  //}

  /**
   * Tests that the Button shortcode returns the right content.
   */
  public function testButtonShortcode() {

    $sets = array(
      array(
        'input' => '[button]Label[/button]',
        'output' => '<a href="/" class="button" title="Label"><span>Label</span></a>',
        'message' => 'Button shortcode output matches.',
      ),
      array(
        'input' => '[button path="<front>" class="custom-class"]Label[/button]',
        'output' => '<a href="/" class="button custom-class" title="Label"><span>Label</span></a>',
        'message' => 'Button shortcode with custom class output matches.',
      ),
      array(
        'input' => '[button path="http://www.google.com" class="custom-class" title="Title" id="theLabel" style="border-radius:5px;"]Label[/button]',
        'output' => '<a href="/http://www.google.com" class="button custom-class" id="theLabel" style="border-radius:5px;" title="Title"><span>Label</span></a>',
        'message' => 'Button shortcode with custom attributes and absolute output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Clear shortcode returns the right content.
   */
  public function testClearShortcode() {

    $sets = array(
      array(
        'input' => '[clear]<div>Other elements</div>[/clear]',
        'output' => '<div class="clearfix"><div>Other elements</div></div>',
        'message' => 'Clear shortcode output matches.',
      ),
      array(
        'input' => '[clear type="s"]<div>Other elements</div>[/clear]',
        'output' => '<span class="clearfix"><div>Other elements</div></span>',
        'message' => 'Clear shortcode with custom type "s" output matches.',
      ),
      array(
        'input' => '[clear type="span"]<div>Other elements</div>[/clear]',
        'output' => '<span class="clearfix"><div>Other elements</div></span>',
        'message' => 'Clear shortcode with custom type "span" output matches.',
      ),
      array(
        'input' => '[clear type="d"]<div>Other elements</div>[/clear]',
        'output' => '<div class="clearfix"><div>Other elements</div></div>',
        'message' => 'Clear shortcode with custom type "d" output matches.',
      ),
      array(
        'input' => '[clear type="d" class="custom-class" id="theLabel" style="background-color: #F00;"]<div>Other elements</div>[/clear]',
        'output' => '<div class="clearfix custom-class" id="theLabel" style="background-color: #F00;"><div>Other elements</div></div>',
        'message' => 'Clear shortcode with custom attributes output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Dropcap shortcode returns the right content.
   */
  public function testDropcapShortcode() {

    $sets = array(
      array(
        'input' => '[dropcap]text[/dropcap]',
        'output' => '<span class="dropcap">text</span>',
        'message' => 'Dropcap shortcode output matches.',
      ),
      array(
        'input' => '[dropcap class="custom-class"]text[/dropcap]',
        'output' => '<span class="dropcap custom-class">text</span>',
        'message' => 'Dropcap shortcode with custom class output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the highlight shortcode returns the right content.
   */
  public function testHighlightShortcode() {

    $test_input = '[highlight]highlighted text[/highlight]';
    $expected_output = '<span class="highlight">highlighted text</span>';
    $output = $this->shortcodeService->process($test_input);
    $this->assertEqual($output, $expected_output, 'Highlight shortcode output matches.');

    $test_input = '[highlight class="custom-class"]highlighted text[/highlight]';
    $expected_output = '<span class="highlight custom-class">highlighted text</span>';
    $output = $this->shortcodeService->process($test_input);
    $this->assertEqual($output, $expected_output, 'Highlight shortcode with custom class output matches.');
  }

  /**
   * Tests that the Image shortcode returns the right content.
   */
  public function testImgShortcode() {

    $sets = array(
      array(
        'input' => '[img src="/abc.jpg" alt="Test image" /]',
        'output' => '<img src="/abc.jpg" class="img" alt="Test image"/>',
        'message' => 'Image shortcode output matches.',
      ),
      array(
        'input' => '[img src="/abc.jpg" class="custom-class" alt="Test image" /]',
        'output' => '<img src="/abc.jpg" class="img custom-class" alt="Test image"/>',
        'message' => 'Image shortcode with custom class output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Item shortcode returns the right content.
   */
  public function testItemShortcode() {


    $sets = array(
      array(
        'input' => '[item]Item body here[/item]',
        'output' => '<div>Item body here</div>',
        'message' => 'Item shortcode output matches.',
      ),
      array(
        'input' => '[item type="s"]Item body here[/item]',
        'output' => '<span>Item body here</span>',
        'message' => 'Item shortcode with custom type "s" output matches.',
      ),
      array(
        'input' => '[item type="d" class="item-class-here" style="background-color:#F00"]Item body here[/item]',
        'output' => '<div class="item-class-here" style="background-color:#F00">Item body here</div>',
        'message' => 'Item shortcode with custom type "d" and class and styles output matches.',
      ),
      array(
        'input' => '[item type="s" class="item-class-here" style="background-color:#F00"]Item body here[/item]',
        'output' => '<span class="item-class-here" style="background-color:#F00">Item body here</span>',
        'message' => 'Item shortcode with custom type "s" and class and styles output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Link shortcode returns the right content.
   */
  public function testLinkShortcode() {

    $sets = array(
      array(
        'input' => '[link path="node/1"]Label[/link]',
        'output' => '<a href="/node/1" title="Label">Label</a>',
        'message' => 'Link shortcode output matches.',
      ),
      array(
        'input' => '[link path="node/23" title="Google" class="link-class" style="background-color:#0FF;"] Label [/link]',
        'output' => '<a href="/node/23" class="link-class" style="background-color:#0FF;" title="Google"> Label </a>',
        'message' => 'Link shortcode with title and attributes output matches.',
      ),
      array(
        'input' => '[link url="http://google.com" title="Google" class="link-class" style="background-color:#0FF;"] Label [/link]',
        'output' => '<a href="http://google.com" class="link-class" style="background-color:#0FF;" title="Google"> Label </a>',
        'message' => 'Link shortcode with url, title and attributes output matches.',
      ),
      array(
        'input' => '[link path="node/23" url="http://google.com" title="Google" class="link-class" style="background-color:#0FF;"] Label [/link]',
        'output' => '<a href="http://google.com" class="link-class" style="background-color:#0FF;" title="Google"> Label </a>',
        'message' => 'Link shortcode with both path and url, title and attributes output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Quote shortcode returns the right content.
   */
  public function testQuoteShortcode() {

    $sets = array(
      array(
        'input' => '[quote]This is by no one[/quote]',
        'output' => '<span class="quote"> This is by no one </span>',
        'message' => 'Quote shortcode output matches.',
      ),
      array(
        'input' => '[quote class="test-quote"]This is by no one[/quote]',
        'output' => '<span class="quote test-quote"> This is by no one </span>',
        'message' => 'Quote shortcode with class output matches.',
      ),
      array(
        'input' => '[quote class="test-quote" author="ryan"]This is by ryan[/quote]',
        'output' => '<span class="quote test-quote"> <span class="quote-author">ryan wrote: </span> This is by ryan </span>',
        'message' => 'Quote shortcode with class and author output matches.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $output = preg_replace('/\s+/', ' ', $output);
      $this->assertEqual($output, $set['output'], $set['message']);
    }
  }

  /**
   * Tests that the Random shortcode returns the right length.
   */
  public function testRandomShortcode() {

    $sets = array(
      array(
        'input' => '[random/]',
        'output' => 8,
        'message' => 'Random shortcode self-closed, output length is correct.',
      ),
      array(
        'input' => '[random][/random]',
        'output' => 8,
        'message' => 'Random shortcode output, length is correct.',
      ),
      array(
        'input' => '[random length=10][/random]',
        'output' => 10,
        'message' => 'Random shortcode with custom length, output length is correct.',
      ),
    );

    foreach ($sets as $set) {
      $output = $this->shortcodeService->process($set['input']);
      $this->assertEqual(strlen($output), $set['output'], $set['message']);
    }
  }
}