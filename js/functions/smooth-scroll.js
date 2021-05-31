/* ----------------------------------------------------------
  Set Smooth scroll on anchor links
---------------------------------------------------------- */

var wpu_set_smooth_scroll = function() {
    /* Scrollto */
    jQuery('body').on('click', '[href^="#"]:not(.no-smooth)', function(e) {
        var href = jQuery(this).attr('href');
        if (href == '#') {
            return;
        }
        var $href = jQuery(href);
        if ($href.length > 0) {
            e.preventDefault();
            jQuery('html,body').animate({
                scrollTop: $href.offset().top
            }, 500);
        }
    });
};
jQuery(document).ready(wpu_set_smooth_scroll);
