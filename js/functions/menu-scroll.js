/* globals jQuery */

/* ----------------------------------------------------------
  Set Menu Scroll
---------------------------------------------------------- */

var wputhmenu = {
    CSSClassName: 'has-floating-menu',
    has_floating_menu: false,
    itemBody: false,
    itemWindow: false,
    checkScrollToTop: true,
    prevScroll: 0,
    scrollLimit: 100,
};

function set_wputh_menu_scroll() {
    'use strict';
    wputhmenu.itemWindow = jQuery(window);
    wputhmenu.itemBody = jQuery('body');
    wputhmenu.itemWindow.on('scroll', wputh_scroll_event);
    wputh_scroll_event();
}

var wputh_scroll_event = function() {
    'use strict';
    var scrollTop = wputhmenu.itemWindow.scrollTop(),
        scrollAmount = Math.abs(wputhmenu.prevScroll - scrollTop),
        scrollToTop = wputhmenu.prevScroll > scrollTop;

    function disable_floating() {
        wputhmenu.has_floating_menu = false;
        wputhmenu.itemBody.removeClass(wputhmenu.CSSClassName);
    }

    function enable_floating() {
        wputhmenu.has_floating_menu = true;
        wputhmenu.itemBody.addClass(wputhmenu.CSSClassName);
    }

    if (wputhmenu.checkScrollToTop) {
        if (scrollToTop && scrollTop > wputhmenu.scrollLimit && !wputhmenu.has_floating_menu) {
            enable_floating();
        }
        if ((!scrollToTop || scrollTop < wputhmenu.scrollLimit) && wputhmenu.has_floating_menu && scrollAmount > 0) {
            disable_floating();
        }
    }
    else {
        if (scrollTop > wputhmenu.scrollLimit && !wputhmenu.has_floating_menu) {
            enable_floating();
        }
        if (scrollTop < wputhmenu.scrollLimit && wputhmenu.has_floating_menu) {
            disable_floating();
        }
    }

    wputhmenu.prevScroll = scrollTop;
};

document.addEventListener("DOMContentLoaded", set_wputh_menu_scroll);
