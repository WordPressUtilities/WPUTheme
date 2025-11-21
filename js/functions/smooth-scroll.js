/* ----------------------------------------------------------
  Set Smooth scroll on anchor links
---------------------------------------------------------- */

var wpu_set_smooth_scroll = function() {

    var desktop_offset = 0;
    if (window.wputheme_smooth_scroll_offset !== undefined) {
        desktop_offset = parseInt(window.wputheme_smooth_scroll_offset, 10);
    }

    var mobile_offset = desktop_offset;
    if (window.wputheme_smooth_scroll_mobile_offset !== undefined) {
        mobile_offset = parseInt(window.wputheme_smooth_scroll_mobile_offset, 10);
    }

    var mobile_offset_breakpoint = 768;
    if (window.wputheme_smooth_scroll_mobile_offset_breakpoint !== undefined) {
        mobile_offset_breakpoint = parseInt(window.wputheme_smooth_scroll_mobile_offset_breakpoint, 10);
    }

    /* Scrollto */
    jQuery('body').on('click', '[href^="#"]:not(.no-smooth)', function(e) {
        var href = jQuery(this).attr('href');
        if (href == '#') {
            return;
        }
        var $href = jQuery(href);
        var _offset = (window.innerWidth <= mobile_offset_breakpoint) ? mobile_offset : desktop_offset;
        if ($href.length > 0) {
            e.preventDefault();
            jQuery('html,body').animate({
                scrollTop: $href.offset().top - _offset
            }, 500);
        }
    });
};
jQuery(document).ready(wpu_set_smooth_scroll);
