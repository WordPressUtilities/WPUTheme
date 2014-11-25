<?php

/* ----------------------------------------------------------
  Charset
---------------------------------------------------------- */

add_action('wp_head', 'wputh_head_add_charset', 0);
if (!function_exists('wputh_head_add_charset')) {
    function wputh_head_add_charset() {
        echo '<meta charset="' . get_bloginfo('charset') . '" />';
    }
}

/* ----------------------------------------------------------
  Page title
---------------------------------------------------------- */

add_action('wp_head', 'wputh_head_add_title', 1);
if (!function_exists('wputh_head_add_title')) {
    function wputh_head_add_title() {
        echo '<title>';
        wp_title();
        echo '</title>';
    }
}

/* ----------------------------------------------------------
  Viewport
---------------------------------------------------------- */

add_action('wp_head', 'wputh_head_add_viewport', 10);
if (!function_exists('wputh_head_add_viewport')) {
    function wputh_head_add_viewport() {
        echo '<meta name="viewport" content="width=device-width" />';
    }
}

/* ----------------------------------------------------------
  Favicon
---------------------------------------------------------- */

add_action('wp_head', 'wputh_head_add_favicon', 10);
if (!function_exists('wputh_head_add_favicon')) {
    function wputh_head_add_favicon() {
        echo '<link rel="shortcut icon" href="' . get_stylesheet_directory_uri() . '/images/favicon.ico" />';
    }
}

/* ----------------------------------------------------------
  IE Compatibility
---------------------------------------------------------- */

add_action('wp_head', 'wputh_head_add_iecompatibility', 10);
if (!function_exists('wputh_head_add_iecompatibility')) {
    function wputh_head_add_iecompatibility() {
        $script_src = get_template_directory_uri() . '/js/ie/';
        echo '<!--[if lt IE 9]><script type="text/javascript" src="' . $script_src . 'html5.js"></script><script type="text/javascript" src="' . $script_src . 'selectivizr-min.js"></script><![endif]-->';
    }
}
