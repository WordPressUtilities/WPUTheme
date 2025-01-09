<?php
include __DIR__ . '/../../z-protect.php';

/* ----------------------------------------------------------
  Common libraries
---------------------------------------------------------- */

add_action('wp_enqueue_scripts', 'wputh_common_libraries');
function wputh_common_libraries() {
    if (apply_filters('wputh_common_libraries__slickslider', false)) {
        $slick_version = '1.8.1';
        wp_enqueue_script('wputh-slickslider-js', get_theme_file_uri('/js/libs/slick-slider/slick/slick.min.js'), array('jquery'), $slick_version, true);
        wp_enqueue_script('wputh-slickslider-init-js', get_theme_file_uri('/js/libs/slick-slider-init.js'), array('jquery', 'wputh-slickslider-js'), $slick_version, true);
        wp_enqueue_style('wputh-slickslider-css', get_theme_file_uri('/js/libs/slick-slider/slick/slick.min.css'), array(), $slick_version);
    }
    if(apply_filters('wputh_common_libraries__swiper', false)){
        $swiper_version = '11.2.0';
        wp_enqueue_script('wputh-swiper-js', get_theme_file_uri('/js/libs/swiper-js/swiper-bundle.min.js'), array(), $swiper_version, true);
        wp_enqueue_script('wputh-swiper-init-js', get_theme_file_uri('/js/libs/swiper-init.js'), array('wputh-swiper-js'), $swiper_version, true);
        wp_enqueue_style('wputh-swiper-css', get_theme_file_uri('/js/libs/swiper-js/swiper-bundle.min.css'), array(), $swiper_version);
    }
    if (apply_filters('wputh_common_libraries__simplebar', false)) {
        wp_enqueue_script('wputh-simplebar-js', get_theme_file_uri('/js/libs/simplebar/simplebar.min.js'), array(), '5.3.6', true);
        wp_enqueue_style('wputh-simplebar-css', get_theme_file_uri('/js/libs/simplebar/simplebar.css'), array(), '5.3.6');
    }
    if (apply_filters('wputh_common_libraries__juxtapose', false)) {
        $juxtapose_version = '1.2.1';
        wp_enqueue_script('wputh-juxtapose-js', get_theme_file_uri('/js/libs/juxtapose/js/juxtapose.min.js'), array(), $juxtapose_version, true);
        wp_enqueue_script('wputh-juxtapose-init-js', get_theme_file_uri('/js/libs/juxtapose-init.js'), array('jquery', 'wputh-juxtapose-js'), $juxtapose_version, true);
        wp_enqueue_style('wputh-juxtapose-css', get_theme_file_uri('/js/libs/juxtapose/css/juxtapose.css'), array(), $juxtapose_version);
    }
    if (apply_filters('wputh_common_libraries__clipboard', false)) {
        $clipboard_version = '2.0.11';
        wp_enqueue_script('wputh-clipboard-js', get_theme_file_uri('/js/libs/clipboard/clipboard.min.js'), array(), $clipboard_version, true);
        wp_enqueue_script('wputh-clipboard-init-js', get_theme_file_uri('/js/libs/clipboard-init.js'), array('jquery', 'wputh-clipboard-js'), $clipboard_version, true);
        wp_localize_script('wputh-clipboard-init-js', 'wputh_clipboard_init_js', array(
            'txt_copied' => __('Copied !', 'wputh')
        ));
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
    $scripts['wputh-sharesheet'] = array(
        'url' => get_theme_file_uri('/js/functions/sharesheet.js'),
        'footer' => 1
    );
    $scripts['wputh-maps'] = array(
        'url' => get_theme_file_uri('/js/functions/maps.js'),
        'footer' => 1
    );
    $scripts['wputh-utilities'] = array(
        'url' => get_theme_file_uri('/js/functions/utilities.js'),
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
        if (isset($details['path'])) {
            $details['filemtime'] = filemtime($details['path']);
        }

        $_scripts[$id] = $details;
    }
    return $_scripts;
}

function wputh_merge_javascripts_compress($content) {
    /* Comments */
    $content = preg_replace('/\/\*([^\/]*)\*\//isU', '', $content);
    $content = preg_replace("/\/\/(.*)\n/", '', $content);

    /* Trim lines */
    $content = implode("\n", array_map('trim', explode("\n", $content)));

    /* Multiple spaces */
    $content = preg_replace("/([ ]+)/", " ", $content);

    /* Multiple line breaks */
    $content = preg_replace("/[\r\n]+/", "\n", $content);

    return $content;
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
                $footer_content .= "\n;\n" . trim(file_get_contents($details['path']));
            }
        } else {
            if (!$has_header) {
                $header_content .= "\n;\n" . trim(file_get_contents($details['path']));
            }
        }

        /* Remove from script list */
        unset($scripts[$id]);
    }

    /* Build cached files */
    if (!$has_header) {
        file_put_contents($header_js_path, wputh_merge_javascripts_compress($header_content));
    }
    if (!$has_footer) {
        file_put_contents($footer_js_path, wputh_merge_javascripts_compress($footer_content));
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
  JS Module version
---------------------------------------------------------- */

/**
 * Get a package.json version
 *
 * @param  string $package_path  path of plugin or path of the package.json file
 * @return string $version       version if available, package.json filemtime or current time
 */
function wputh_get_js_version($package_path) {
    if (is_dir($package_path)) {
        $package_path = $package_path . '/package.json';
    }
    if (!file_exists($package_path)) {
        return time();
    }
    $json_content = json_decode(file_get_contents($package_path), true);
    if (!is_array($json_content) || !isset($json_content['version'])) {
        return filemtime($package_path);
    }
    return $json_content['version'];
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
