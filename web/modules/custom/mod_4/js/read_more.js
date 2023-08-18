(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mobileValidation = {
    attach: function (context, settings) {
      const body = drupalSettings.mod_4.body;
      const cleanTextArr = body.replace(/(<([^>]+)>)/gi, "").split(' ');
      const firstFifty = cleanTextArr.splice(0, 50).join(' ');
      $('.read_less').prop('disabled', true);
      $('.body_field').html(firstFifty);

      $('.read_more', context).click(function () {
        $('.body_field').html(body);
        $(this).prop('disabled', true);
        $('.read_less').prop('disabled', false);
      });

      $('.read_less', context).click(function () {
        $('.body_field').html(firstFifty);
        $(this).prop('disabled', true);
        $('.read_more').prop('disabled', false);
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
