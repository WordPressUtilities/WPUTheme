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
