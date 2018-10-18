<?php
include dirname(__FILE__) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Remove JQ Migrate
---------------------------------------------------------- */

add_filter('wp_default_scripts', 'wputh_disable_jqmigrate');

/* http://subinsb.com/remove-jquery-migrate-in-wp-blog */
function wputh_disable_jqmigrate(&$scripts) {
    if (!is_admin()) {
        $scripts->remove('jquery');
        $scripts->add('jquery', false, array(
            'jquery-core'
        ) , '1.12.4');
    }
}

/* ----------------------------------------------------------
  Add JS
---------------------------------------------------------- */

add_filter('wputh_javascript_files', 'wputh_javascript_files__default', 1, 1);
function wputh_javascript_files__default($scripts = array()) {
    $scripts['jquery'] = array();
    $scripts['functions-faq-accordion'] = array(
        'url' => get_template_directory_uri().'/js/functions/faq-accordion.js',
        'footer' => 1
    );
    $scripts['functions-smooth-scroll'] = array(
        'url' => get_template_directory_uri().'/js/functions/smooth-scroll.js',
        'footer' => 1
    );
    $scripts['functions-menu-scroll'] = array(
        'url' => get_template_directory_uri().'/js/functions/menu-scroll.js',
        'footer' => 1
    );
    $scripts['functions-remove-utm-ga'] = array(
        'url' => get_template_directory_uri().'/js/functions/remove-utm-ga.js',
        'footer' => 1
    );
    $scripts['functions-search-form-check'] = array(
        'url' => get_template_directory_uri().'/js/functions/search-form-check.js',
        'footer' => 1
    );
    $scripts['events'] = array(
        'url' => get_template_directory_uri().'/js/events.js',
        'footer' => 1
    );
    return $scripts;
}

function wputheme_get_javascripts() {
    $scripts = apply_filters('wputh_javascript_files', array());
    global $WPUJavaScripts;
    if (isset($WPUJavaScripts) && is_array($WPUJavaScripts)) {
        $scripts = $WPUJavaScripts;
    }
    return $scripts;
}

function wputh_add_javascripts() {
    $scripts = wputheme_get_javascripts();
    foreach ($scripts as $id => $details) {
        $url = '';
        if (!isset($details['uri']) && !isset($details['url'])) {
            wp_enqueue_script($id);
            continue;
        }

        if (isset($details['url'])) {
            $url = $details['url'];
        }
        if (isset($details['uri'])) {
            $url = get_stylesheet_directory_uri() . $details['uri'];
        }
        $deps = isset($details['deps']) ? $details['deps'] : false;
        $ver = isset($details['ver']) ? $details['ver'] : WPUTHEME_ASSETS_VERSION;
        $in_footer = isset($details['footer']) && $details['footer'] == true;
        wp_register_script($id, $url, $deps, $ver, $in_footer);
        wp_enqueue_script($id);
    }
}
add_action('wp_enqueue_scripts', 'wputh_add_javascripts');

/* ----------------------------------------------------------
  Add attributes
---------------------------------------------------------- */

add_filter('script_loader_tag', 'wputh_javascript_attributes', 10, 2);
function wputh_javascript_attributes($tag, $handle) {
    $scripts = wputheme_get_javascripts();

    foreach ($scripts as $id => $script) {
        if ($id != $handle) {
            continue;
        }
        if (isset($script['defer']) && $script['defer']) {
            return str_replace(" src", " defer='defer' src", $tag);
        }
        if (isset($script['async']) && $script['async']) {
            return str_replace(" src", " async='async' src", $tag);
        }
    }
    return $tag;
}
