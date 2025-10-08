<?php

/* ----------------------------------------------------------
  Get current URL
---------------------------------------------------------- */

/* Thanks to http://kovshenin.com/2012/current-url-in-wordpress/ */
function wputh_get_current_url() {
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));
    if (is_singular() || is_single() || is_page()) {
        $current_url = get_permalink();
    }
    return $current_url;
}

/* ----------------------------------------------------------
  Check if internal link
---------------------------------------------------------- */

function wpu_is_internal_link($external_url) {
    $url_host = parse_url($external_url, PHP_URL_HOST);
    $base_url_host = parse_url(get_site_url(), PHP_URL_HOST);
    return ($url_host == $base_url_host || empty($url_host));
}

/* ----------------------------------------------------------
  Remove query arg from URL
---------------------------------------------------------- */

function wputh_remove_query_arg($url, $arg) {
    $parsed_url = parse_url($url);
    if (!isset($parsed_url['query'])) {
        return $url;
    }
    parse_str($parsed_url['query'], $query_array);
    $new_query_array = array();
    foreach ($query_array as $key => $value) {
        $new_key = str_replace(array('&amp;', 'amp;', ';'), '', $key);
        if ($new_key == $arg) {
            continue;
        }
        $new_query_array[$new_key] = $value;
    }

    $parsed_url['query'] = http_build_query($new_query_array);

    return wputh_rebuild_parsed_url($parsed_url);

}

/* ----------------------------------------------------------
  Rebuild a parsed URL
---------------------------------------------------------- */

function wputh_rebuild_parsed_url($url_parts) {
    return (isset($url_parts['scheme']) ? $url_parts['scheme'] . '://' : '') .
        (isset($url_parts['user']) ? $url_parts['user'] . (isset($url_parts['pass']) ? ':' . $url_parts['pass'] : '') . '@' : '') .
        (isset($url_parts['host']) ? $url_parts['host'] : '') .
        (isset($url_parts['port']) ? ':' . $url_parts['port'] : '') .
        (isset($url_parts['path']) ? $url_parts['path'] : '') .
        (isset($url_parts['query']) ? '?' . $url_parts['query'] : '') .
        (isset($url_parts['fragment']) ? '#' . $url_parts['fragment'] : '');
}
