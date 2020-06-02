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
        ), '1.12.4');
    }
}

/* ----------------------------------------------------------
  Common libraries
---------------------------------------------------------- */

add_action('wp_enqueue_scripts', 'wputh_common_libraries');
function wputh_common_libraries() {
    if (apply_filters('wputh_common_libraries__slickslider', false)) {
        $slick_version = '1.8.1';
        wp_enqueue_script('wputh-slickslider-js', get_theme_file_uri('/js/libs/slick-slider/slick/slick.min.js'), array('jquery'), $slick_version, true);
        wp_enqueue_script('wputh-slickslider-init-js', get_theme_file_uri('/js/libs/slick-slider-init.js'), array('jquery'), $slick_version, true);
        wp_enqueue_style('wputh-slickslider-css', get_theme_file_uri('/js/libs/slick-slider/slick/slick.css'), array(), $slick_version);
    }
    if (apply_filters('wputh_common_libraries__simplebar', false)) {
        wp_enqueue_script('wputh-simplebar-js', get_theme_file_uri('/js/libs/simplebar/simplebar.min.js'), array(), '5.1.0', true);
        wp_enqueue_style('wputh-simplebar-css', get_theme_file_uri('/js/libs/simplebar/simplebar.css'), array(), '5.1.0');
    }
    if (apply_filters('wputh_common_libraries__photoswipe', false)) {
        wp_enqueue_style('wputh-photoswipe-css', get_theme_file_uri('/js/libs/photoswipe/dist/photoswipe.css'), array(), '4.1.3');
        wp_enqueue_style('wputh-photoswipe-default-skin-css', get_theme_file_uri('/js/libs/photoswipe/dist/default-skin/default-skin.css'), array(), '4.1.3');
        wp_enqueue_script('wputh-photoswipe-js', get_theme_file_uri('/js/libs/photoswipe/dist/photoswipe.min.js'), array(), '4.1.3', true);
        wp_enqueue_script('wputh-photoswipe-default-ui-js', get_theme_file_uri('/js/libs/photoswipe/dist/photoswipe-ui-default.min.js'), array(), '4.1.3', true);
    }
}

add_action('wp_footer', 'wputh_common_libraries__footer', 50);
function wputh_common_libraries__footer() {
    if (apply_filters('wputh_common_libraries__photoswipe', false)) {
        get_template_part('tpl/scripts/photoswipe');
    }
}

/* ----------------------------------------------------------
  Add JS
---------------------------------------------------------- */

add_filter('wputh_javascript_files', 'wputh_javascript_files__default', 1, 1);
function wputh_javascript_files__default($scripts = array()) {
    $scripts['jquery'] = array();
    $scripts['functions-faq-accordion'] = array(
        'url' => get_theme_file_uri('/js/functions/faq-accordion.js'),
        'footer' => 1
    );
    $scripts['functions-smooth-scroll'] = array(
        'url' => get_theme_file_uri('/js/functions/smooth-scroll.js'),
        'footer' => 1
    );
    $scripts['functions-menu-scroll'] = array(
        'url' => get_theme_file_uri('/js/functions/menu-scroll.js'),
        'footer' => 1
    );
    $scripts['functions-remove-utm-ga'] = array(
        'url' => get_theme_file_uri('/js/functions/remove-utm-ga.js'),
        'footer' => 1
    );
    $scripts['functions-search-form-check'] = array(
        'url' => get_theme_file_uri('/js/functions/search-form-check.js'),
        'footer' => 1
    );
    $scripts['wputh-maps'] = array(
        'url' => get_theme_file_uri('/js/functions/maps.js'),
        'footer' => 1
    );
    $scripts['events'] = array(
        'url' => get_theme_file_uri('/js/events.js'),
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

function wputh_build_javascripts() {
    $scripts = wputheme_get_javascripts();
    $_scripts = array();
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
            $url = $details['uri'];
            if (substr($details['uri'], 0, 4) != 'http') {
                $url = get_theme_file_uri($url);
            }
        }
        $details['url'] = $url;
        if (wputh_startsWith($details['url'], content_url())) {
            $details['path'] = str_replace(content_url(), ABSPATH . 'wp-content', $details['url']);
        }

        $details['deps'] = isset($details['deps']) ? $details['deps'] : false;
        $details['ver'] = isset($details['ver']) ? $details['ver'] : WPUTHEME_ASSETS_VERSION;
        $details['footer'] = isset($details['footer']) && $details['footer'] == true;
        $_scripts[$id] = $details;
    }
    return $_scripts;
}

function wputh_merge_javascripts($scripts) {

    $scripts_hash = md5(json_encode($scripts));

    /* Build cache folder */
    $upload_dir = wp_get_upload_dir();
    $cache_dir = $upload_dir['basedir'] . '/wputhmin';
    $cache_url = $upload_dir['baseurl'] . '/wputhmin';
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir);
    }

    /* Build files format */
    $footer_js_path = $cache_dir . '/footer-' . $scripts_hash . '.js';
    $footer_js_url = $cache_url . '/footer-' . $scripts_hash . '.js';
    $header_js_path = $cache_dir . '/header-' . $scripts_hash . '.js';
    $header_js_url = $cache_url . '/header-' . $scripts_hash . '.js';

    /* Check if file already exists */
    $has_footer = file_exists($footer_js_path);
    $has_header = file_exists($header_js_path);

    /* Build header & footer */
    $header_content = '';
    $footer_content = '';
    foreach ($scripts as $id => $details) {
        /* Ignore non local scripts */
        if (!isset($details['path'])) {
            continue;
        }
        if ($details['footer']) {
            if (!$has_footer) {
                $footer_content .= "\n;\n" . file_get_contents($details['path']);
            }
        } else {
            if (!$has_header) {
                $header_content .= "\n;\n" . file_get_contents($details['path']);
            }
        }

        /* Remove from script list */
        unset($scripts[$id]);
    }

    /* Build cached files */
    if (!$has_header) {
        file_put_contents($header_js_path, $header_content);
    }
    if (!$has_footer) {
        file_put_contents($footer_js_path, $footer_content);
    }

    /* Add cached files */
    $scripts['footer-' . $scripts_hash] = array(
        'url' => $footer_js_url,
        'deps' => array(),
        'ver' => '',
        'footer' => true
    );
    $scripts['header-' . $scripts_hash] = array(
        'url' => $header_js_url,
        'deps' => array(),
        'ver' => '',
        'footer' => false
    );

    return $scripts;
}

add_action('wp_enqueue_scripts', 'wputh_add_javascripts');
function wputh_add_javascripts() {
    $scripts = wputh_build_javascripts();
    if (defined('WPUTH_MERGE_JAVASCRIPTS') && WPUTH_MERGE_JAVASCRIPTS) {
        $scripts = wputh_merge_javascripts($scripts);
    }
    foreach ($scripts as $id => $details) {
        wp_register_script($id, $details['url'], $details['deps'], $details['ver'], $details['footer']);
        wp_enqueue_script($id);
    }
}

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
