<?php
include dirname(__FILE__) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Supported features
---------------------------------------------------------- */

add_action('after_setup_theme', 'wputh_custom_theme_setup');

if (function_exists('add_theme_support')) {

    function wputh_custom_theme_setup() {

        // Supporting thumbnails
        add_theme_support('post-thumbnails');

        // Supporting RSS Links
        add_theme_support('automatic-feed-links');

        // Title
        add_theme_support('title-tag');
    }
}

/* ----------------------------------------------------------
  Excerpt
---------------------------------------------------------- */

add_filter('excerpt_length', 'wputh_excerpt_length', 999);
function wputh_excerpt_length() {
    return 15;
}

add_filter('excerpt_more', 'wputh_excerpt_more');
function wputh_excerpt_more() {
    return ' &hellip; ';
}
