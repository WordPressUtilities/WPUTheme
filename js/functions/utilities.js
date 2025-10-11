/* ----------------------------------------------------------
  Cookies
---------------------------------------------------------- */

/* Set Cookie
-------------------------- */

function wputheme_setcookie(cookie_name, cookie_value, expiration_days, path) {
    if (!expiration_days) {
        expiration_days = 30;
    }
    if (!path) {
        path = '/';
    }
    var d = new Date();
    d.setTime(d.getTime() + (expiration_days * 24 * 60 * 60 * 1000));
    document.cookie = cookie_name + '=' +
        cookie_value + ';' +
        'expires=' + d.toUTCString() + ';' +
        'path=' + path;
}

/* Get Cookie
-------------------------- */

function wputheme_getcookie(cookie_name) {
    var name = cookie_name + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}

/* ----------------------------------------------------------
  Language
---------------------------------------------------------- */

function wputheme_getuserlanguage() {
    var _lang = navigator.language || navigator.userLanguage;
    _lang = _lang.toLowerCase();
    _lang = _lang.replace('_', '-');
    return _lang;
}

/* ----------------------------------------------------------
  Debounce
---------------------------------------------------------- */

/* Thanks to https://davidwalsh.name/javascript-debounce-function */
function wputheme_debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this,
            args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/* ----------------------------------------------------------
  JS Loader
---------------------------------------------------------- */

function wputheme_loadscript_async(src, callback) {
    var script = document.createElement('script');
    if (callback) {
        script.onload = callback;
    }
    script.async = true;
    script.src = src;
    document.head.append(script);
}

/* ----------------------------------------------------------
  CSS Loader
---------------------------------------------------------- */

function wputheme_loadstyle_async(src, callback) {
    var style = document.createElement("link");
    if (callback) {
        style.onload = callback;
    }
    style.rel = "stylesheet";
    style.href = src;
    document.head.append(style);
}

/* ----------------------------------------------------------
  Get quadrant in page
---------------------------------------------------------- */

function wputheme_get_quadrant(x, y) {
    var width = document.documentElement.clientWidth / 2;
    var height = document.documentElement.clientHeight / 2;

    var quadrant;
    if (x < width && y < height) {
        quadrant = "top-left";
    } else if (x >= width && y < height) {
        quadrant = "top-right";
    } else if (x < width && y >= height) {
        quadrant = "bottom-left";
    } else {
        quadrant = "bottom-right";
    }
    return quadrant;
}

/* ----------------------------------------------------------
  Check if an element is visible
---------------------------------------------------------- */

function wputheme_is_element_visible($el) {
    return ($el.offsetWidth || $el.offsetHeight || $el.getClientRects().length);
}
