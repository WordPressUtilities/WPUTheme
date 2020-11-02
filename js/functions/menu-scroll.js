/* globals jQuery */

/* ----------------------------------------------------------
  Set Menu Scroll
---------------------------------------------------------- */

var wputhmenu = {
    CSSClassName: 'has-floating-menu',
    CSSClassNameM: 'has-floating-menu-mobile',
    CSSClassNameB: 'will-have-floating-menu',
    has_floating_menu: false,
    has_bfloating_menu: false,
    has_mfloating_menu: false,
    scrollLimit: 100,
    scrollLimitB: false,
    scrollLimitMobile: false,
    itemBody: false,
    checkScrollToTop: true,
    prevScroll: 0
};

function set_wputh_menu_scroll() {
    'use strict';
    wputhmenu.itemBody = jQuery('body');
    window.addEventListener('scroll', wputh_scroll_event);
    wputh_scroll_event();
}

var wputh_scroll_event = function() {
    'use strict';
    var scrollTop = window.scrollY,
        scrollAmount = Math.abs(wputhmenu.prevScroll - scrollTop),
        scrollToTop = wputhmenu.prevScroll > scrollTop;

    /* ----------------------------------------------------------
      Check menu scroll
    ---------------------------------------------------------- */

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

    /* ----------------------------------------------------------
      Check before menu scroll
    ---------------------------------------------------------- */

    function disable_bfloating() {
        wputhmenu.has_bfloating_menu = false;
        wputhmenu.itemBody.removeClass(wputhmenu.CSSClassNameB);
    }

    function enable_bfloating() {
        wputhmenu.has_bfloating_menu = true;
        wputhmenu.itemBody.addClass(wputhmenu.CSSClassNameB);
    }

    if (wputhmenu.scrollLimitB) {
        if (scrollTop > wputhmenu.scrollLimitB && !wputhmenu.has_bfloating_menu) {
            enable_bfloating();
        }
        if (scrollTop < wputhmenu.scrollLimitB && wputhmenu.has_bfloating_menu) {
            disable_bfloating();
        }
    }

    /* ----------------------------------------------------------
      Check mobile menu scroll
    ---------------------------------------------------------- */

    function disable_mfloating() {
        wputhmenu.has_mfloating_menu = false;
        wputhmenu.itemBody.removeClass(wputhmenu.CSSClassNameM);
    }

    function enable_mfloating() {
        wputhmenu.has_mfloating_menu = true;
        wputhmenu.itemBody.addClass(wputhmenu.CSSClassNameM);
    }

    if (wputhmenu.scrollLimitMobile) {
        if (scrollTop > wputhmenu.scrollLimitMobile && !wputhmenu.has_mfloating_menu) {
            enable_mfloating();
        }
        if (scrollTop < wputhmenu.scrollLimitMobile && wputhmenu.has_mfloating_menu) {
            disable_mfloating();
        }
    }

    wputhmenu.prevScroll = scrollTop;
};

document.addEventListener("DOMContentLoaded", set_wputh_menu_scroll);
