/* ----------------------------------------------------------
  Remove UTM arguments from URL
---------------------------------------------------------- */

var wpu_remove_utm_ga = function() {
    'use strict';
    // Only if pushstate available & utm_ arguments
    if (!('pushState' in history) || !(/utm_/.test(location.search))) {
        return;
    }
    // Get url & details
    var oldUrl = location.href,
        newUrl = oldUrl.replace(location.search, ''),
        search = location.search.replace('?', ''),
        searchParams = search.split('&'),
        newQuery = [];
    // Test if each argument begins with "utm_"
    for (var i = 0, len = searchParams.length; i < len; i++) {
        if (searchParams[i].substr(0, 4) != 'utm_') {
            newQuery.push(searchParams[i]);
        }
    }
    // Recompose URL
    var parser = document.createElement('a');
    parser.href = newUrl;
    parser.search = '?' + newQuery.join('&');
    history.replaceState({}, false, parser.href);
};

document.addEventListener("DOMContentLoaded", wpu_remove_utm_ga);
