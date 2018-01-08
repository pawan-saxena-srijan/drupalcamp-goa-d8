(function ($) {
    Drupal.behaviors.exampleModule = {
        attach: function (context, settings) {
            $(".node--type-session .field").slice(0,4).wrapAll("<div class='right-side-wrap'></div>");
            $(".node--type-session .text-formatted").slice(0,3).wrapAll("<div class='left-side-wrap'></div>");
        }
    };
}(jQuery));