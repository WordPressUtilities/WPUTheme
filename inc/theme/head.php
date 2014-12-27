<?php
include dirname(__FILE__) . '/../../z-protect.php';

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
  Google Fonts
---------------------------------------------------------- */

add_action('wp_enqueue_scripts', 'wputh_head_enqueue_google_fonts');
function wputh_head_enqueue_google_fonts() {
    $query_args = apply_filters('wputh_google_fonts', array());
    if (!empty($query_args)) {
        wp_register_style('google-fonts', add_query_arg($query_args, "//fonts.googleapis.com/css") , array() , null);
        wp_enqueue_style('google-fonts');
    }
}

/* ----------------------------------------------------------
  Page title
---------------------------------------------------------- */

if (!function_exists('_wp_render_title_tag')) {
    add_action('wp_head', 'wputh_head_add_title', 1);
    if (!function_exists('wputh_head_add_title')) {
        function wputh_head_add_title() {
            echo '<title>';
            wp_title();
            echo '</title>';
        }
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
