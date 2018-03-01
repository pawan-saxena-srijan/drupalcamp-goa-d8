(function ($) {
    Drupal.behaviors.exampleModule = {
        attach: function (context, settings) {
            if ($(window).width() < 767) {
                var main_menu = '.menu--main .menu';
                var login_menu = '.menu--account ul.menu';
                var login_menu_html = $(login_menu).html();
                $('.menu--main > ul.menu').append(login_menu_html);
                $('.menu--main .menu').hide();
                $('.menu--main').click(function() {
                    if($(main_menu).css('display') == 'none') {
                        $(main_menu).show();
                    } else {
                        $(main_menu).hide();
                    }
                });
            }
            $(".day1-image").show();
            $(".day1").addClass("active");
            $(".day1").click(function() {
                $(".day1-image").show();
                $(this).addClass("active");
                $(".day2-image").hide();
                $(".day2").removeClass("active");
            });
            $(".day2").click(function() {
                $(".day2-image").show();
                $(this).addClass("active");
                $(".day1-image").hide();
                $(".day1").removeClass("active");
            });
        }
    };
}(jQuery));
