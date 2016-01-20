/* globals jQuery */

'use strict';

/* ----------------------------------------------------------
  Set Menu Scroll
---------------------------------------------------------- */

var wputhmenu = {
    CSSClassName: 'has-floating-menu',
    has_floating_menu: false,
    itemBody: false,
    itemWindow: false,
    prevScroll: 0,
    scrollLimit: 100,
};

function set_wputh_menu_scroll() {
    wputhmenu.itemWindow = jQuery(window);
    wputhmenu.itemBody = jQuery('body');
    wputhmenu.scrollLimit = 100;
    wputhmenu.itemWindow.on('scroll', wputh_scroll_event);
    wputh_scroll_event();
}

var wputh_scroll_event = function() {
    var scrollTop = wputhmenu.itemWindow.scrollTop(),
        scrollToTop = wputhmenu.prevScroll > scrollTop;
    if (scrollToTop && scrollTop > wputhmenu.scrollLimit && !wputhmenu.has_floating_menu) {
        wputhmenu.has_floating_menu = true;
        wputhmenu.itemBody.addClass(wputhmenu.CSSClassName);
    }
    if ((!scrollToTop || scrollTop < wputhmenu.scrollLimit) && wputhmenu.has_floating_menu) {
        wputhmenu.has_floating_menu = false;
        wputhmenu.itemBody.removeClass(wputhmenu.CSSClassName);
    }
    wputhmenu.prevScroll = scrollTop;
};

jQuery(document).ready(function() {
    set_wputh_menu_scroll();
});
