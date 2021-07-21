


(function($) {
    'use strict';
    $(window).on( 'load', function(){

        $('#preloader').delay(300).fadeOut('slow',function(){
            $(this).remove();
        });

    });
    $(document).ready(function(){

        $(window).on('scroll', function () {
            var menu_area = $('.menu-area');
            if ($(window).scrollTop() > 50) {
                menu_area.addClass('sticky-menu');
            } else {
                menu_area.removeClass('sticky-menu');
            }
        }); // $(window).on('scroll' end

        $(document).on('click', '.navbar-collapse.in', function (e) {
            if ($(e.target).is('a') && $(e.target).attr('class') != 'dropdown-toggle') {
                $(this).collapse('hide');
            }
        });

        $('body').scrollspy({
            target: '.navbar-collapse',
            offset: 195
        });

    }); // $(document).ready end

})(jQuery);