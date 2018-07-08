(function (Drupal, drupalSettings) {
  Drupal.behaviors.RoSearch = {
    attach: function (context, settings) {
      (function() {
        var cx = drupalSettings.google_cse_id;
        var gcse = document.createElement('script');
        gcse.type = 'text/javascript';
        gcse.async = true;
        gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(gcse, s);
      })();
    }
  };
})(Drupal, drupalSettings);

