<?php
include __DIR__ . '/../../z-protect.php';

/* ----------------------------------------------------------
  Insert all available CSS files
---------------------------------------------------------- */

if (!is_admin()) {
    add_action('wp_enqueue_scripts', 'wputh_add_stylesheets');
    if (function_exists('wputh_control_stylesheets')) {
        add_action('wp_enqueue_scripts', 'wputh_control_stylesheets');
    }
}

// Base values
if (!defined('WPU_CSS_DIR')) {
    define('WPU_CSS_DIR', get_template_directory() . '/css/');
}
if (!defined('WPU_CSS_URL')) {
    define('WPU_CSS_URL', get_template_directory_uri() . '/css/');
}

if (!function_exists('wputh_add_stylesheets')) {
    function wputh_add_stylesheets() {
        $css_files = wputh_parse_path(WPU_CSS_DIR);
        foreach ($css_files as $file) {
            wpu_add_css_file($file, WPU_CSS_DIR, WPU_CSS_URL);
        }
    }
}

function wputh_parse_path($dir) {
    $css_files = array();

    // Retrieving files
    $files = glob($dir . '*', GLOB_MARK);

    // Ordering by name
    asort($files);

    foreach ($files as $file) {
        // Searching for files inside a folder
        if (is_dir($file)) {
            $css_files = array_merge(wputh_parse_path($file), $css_files);
        } elseif (substr($file, -4, 4) == '.css') {
            $css_files[] = $file;
        }
    }

    return $css_files;
}

function wpu_add_css_file($file, $dir, $url) {
    // Adding a file to the WordPress stylesheet queue
    $css_file_url = str_replace($dir, $url, $file);
    $css_file_slug = 'wputh' . ((WPU_CSS_DIR != $dir) ? 'child' : '') . strtolower(str_replace(array($dir, '.css'), '', $file));
    wp_register_style($css_file_slug, $css_file_url, NULL, apply_filters('wputh_style_version', WPUTHEME_ASSETS_VERSION));
    wp_enqueue_style($css_file_slug);
}

/* ----------------------------------------------------------
  Add editor stylesheet
---------------------------------------------------------- */

add_action('init', 'wputh_add_editor_styles');
if (!function_exists('wputh_add_editor_styles')) {
    function wputh_add_editor_styles() {
        add_editor_style(WPU_CSS_URL . 'editor.css');
    }
}

/* ----------------------------------------------------------
  Helper : load a CSS file asynchronously
---------------------------------------------------------- */

/* Thanks to https://www.filamentgroup.com/lab/load-css-simpler/ */

$wputheme_wp_styles_async_css = array();

function wputheme_wp_styles_async_css($handle) {
    global $wp_styles, $wputheme_wp_styles_async_css;
    if (!is_object($wp_styles)) {
        return false;
    }
    if (!isset($wp_styles->registered[$handle])) {
        return false;
    }
    $wp_styles->registered[$handle]->args = 'print';
    $wputheme_wp_styles_async_css[] = $handle;
    return true;
}

add_filter('style_loader_tag', function ($html, $handle) {
    global $wputheme_wp_styles_async_css;
    if (!is_array($wputheme_wp_styles_async_css)) {
        $wputheme_wp_styles_async_css = array();
    }
    if (in_array($handle, $wputheme_wp_styles_async_css)) {
        $html = str_replace("media=", 'onload="this.media=\'all\'" media=', $html);
    }
    return $html;
}, 10, 2);

/* ----------------------------------------------------------
  Preload fonts
---------------------------------------------------------- */

/* Helper to extract all available fonts
-------------------------- */

function wputheme_preload_font_find() {
    $fonts = wputheme_rsearch(get_stylesheet_directory() . '/assets/', '/.*\.woff2/');
    return array_map(function ($a) {
        return str_replace(get_stylesheet_directory(), '', $a);
    }, $fonts);
}

// var_export(wputheme_preload_font_find());die;

/* Helper to preload a font
-------------------------- */

function wputheme_preload_font($font_file, $version_file = false) {
    $theme_path = str_replace(site_url(), '', get_stylesheet_directory_uri());
    if (!is_readable(ABSPATH . $theme_path . $font_file)) {
        return false;
    }
    if ($version_file && !is_readable(ABSPATH . $theme_path . $version_file)) {
        $version_file = false;
    }
    $href = $theme_path . $font_file . ($version_file ? '?' . file_get_contents(ABSPATH . $theme_path . $version_file) : '');
    return array(
        'href' => $href,
        'as' => 'font',
        'crossorigin' => 'anonymous',
        'type' => 'font/woff2'
    );
}

/* Load all preloaded fonts
-------------------------- */

add_filter('wp_preload_resources', function ($preload_resources = array()) {
    $fonts = apply_filters('wputheme_preload_fonts', array());
    foreach ($fonts as $font) {
        if ($font) {
            $preload_resources[] = $font;
        }
    }
    if (has_header_image()) {
        $preload_resources[] = array(
            'href' => get_header_image(),
            'as' => 'image'
        );
    }
    return $preload_resources;
}, 10, 1);
