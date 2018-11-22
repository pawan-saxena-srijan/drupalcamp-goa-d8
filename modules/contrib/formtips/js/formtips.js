(function ($) {

  Drupal.behaviors.formtips = {
    attach: function (context, settings) {
      var formtip_settings = settings.formtips;
      var selectors = formtip_settings.selectors;
      if ($.isArray(selectors)) {
        selectors = selectors.join(', ');
      }

      var $descriptions = $('.form-item .description')
        .not(selectors)
        .not('.formtips-processed')
        .addClass('formtips-processed');

      if (formtip_settings.max_width.length) {
        $descriptions.css('max-width', formtip_settings.max_width);
      }

      // Hide descriptions when escaped is hit.
      $(document).on('keyup', function (e) {
        if (e.which === 27) {
          $descriptions.removeClass('formtips-show');
        }
      });

      $descriptions.each(function() {
        var $description = $(this),
          $item = $(this).closest('.form-item'),
          $label = $item.find('label,.fieldset-legend').first();
        $description.toggleClass('formtips-show', false);
        $item.css('position', 'relative');
        var $formtip = $('<a class="formtip"></a>');
        $label.wrap('<div class="formtips-wrapper"></div>').append($formtip);

        if (formtip_settings.trigger_action === 'click') {
          $formtip.on('click', function() {
            $description.toggleClass('formtips-show');
            return false;
          });
          // Hide description when clicking elsewhere.
          $item.on('click', function(e) {
            var $target = $(e.target);
            if (!$target.hasClass('formtip') && !$target.hasClass('formtips-processed')) {
              $description.toggleClass('formtips-show', false);
            }
          });
        }
        else {
          $formtip.hoverIntent({
            sensitivity: formtip_settings.sensitivity,
            interval: formtip_settings.interval,
            over: function () {
              $description.toggleClass('formtips-show', true);
            },
            timeout: formtip_settings.timeout,
            out: function () {
              $description.toggleClass('formtips-show', false);
            }
          });
        }
      });
    }
  };

})(jQuery);
