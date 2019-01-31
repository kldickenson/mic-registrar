/**
 * @file
 * Placeholder file for custom sub-theme behaviors.
 *
 */
(function ($, Drupal) {

  /**
   * Custom JS.
   */
  Drupal.behaviors.roTheme = {
    attach: function (context, settings) {
      var $feesBlock = $('.block-views-block-tuition-fees-relationship-block-sort');

      // Hide fees block if there are no results.
      if ($feesBlock.length && $feesBlock.find('.empty').length) {
        $('#block-views-block-tuition-fees-relationship-block-2').hide();
      }

      // Handle front page layout changes whether or not there are announcements.
      var $announcements = $('#block-views-block-homepage-block-2');
      var $homepage_block = $('#block-views-block-homepage-block-1');

      if (!$announcements.length && $homepage_block.length) {
        $homepage_block.parent().addClass('flex-center');
      }
    }
  };

})(jQuery, Drupal);

(function ($, Drupal) {

  /**
   * Initializes foundation's JavaScript for new content added to the page.
   */
  Drupal.behaviors.foundationInit = {
    attach: function (context, settings) {
      $(context).foundation();
    }
  };

})(jQuery, Drupal);
