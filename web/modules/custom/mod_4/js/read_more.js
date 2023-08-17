(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mobileValidation = {
    attach: function (context, settings) {
      var body = drupalSettings.mod_4.body;
      $('.read_less').prop('disabled', true);
      $('.body_field').html(body.substr(0, 50));

      $('.read_more', context).click(function () {
        $('.body_field').html(body);
        $(this).prop('disabled', true);
        $('.read_less').prop('disabled', false);
      });

      $('.read_less', context).click(function () {
        $('.body_field').html(body.substr(0, 50));
        $(this).prop('disabled', true);
        $('.read_more').prop('disabled', false);
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
