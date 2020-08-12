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
