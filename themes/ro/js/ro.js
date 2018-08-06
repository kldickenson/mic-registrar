/**
 * @file
 * Placeholder file for custom sub-theme behaviors.
 *
 */
(function ($, Drupal) {

  /**
   * Use this behavior as a template for custom Javascript.
   */
  Drupal.behaviors.roTheme = {
    attach: function (context, settings) {
      var $feesBlock = $('.block-views-block-tuition-fees-relationship-block-sort');

      // Hide fees block if there are no results.
      if ($feesBlock.length && $feesBlock.find('.empty').length) {
        $('#block-views-block-tuition-fees-relationship-block-2').hide();
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
