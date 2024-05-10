<?php
include __DIR__ . '/../../z-protect.php';

/* ----------------------------------------------------------
  Supported features
---------------------------------------------------------- */

add_action('after_setup_theme', 'wputh_custom_theme_setup');

if (!function_exists('wputh_custom_theme_setup') && function_exists('add_theme_support')) {

    function wputh_custom_theme_setup() {

        // WooCommerce
        add_theme_support('woocommerce');

        // Theme style
        add_theme_support('custom-background');
        add_theme_support('custom-header');
        add_theme_support('custom-logo');

        // Supporting HTML5
        add_theme_support('html5', array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'style',
            'script'
        ));

        // Supporting thumbnails
        add_theme_support('post-thumbnails');

        // Supporting RSS Links
        add_theme_support('automatic-feed-links');

        // Supporting Title
        add_theme_support('title-tag');
    }
}

/* ----------------------------------------------------------
  Excerpt
---------------------------------------------------------- */

add_filter('excerpt_length', 'wputh_excerpt_length', 999);
if (!function_exists('wputh_excerpt_length')) {
    function wputh_excerpt_length() {
        return 15;
    }
}

add_filter('excerpt_more', 'wputh_excerpt_more');
if (!function_exists('wputh_excerpt_more')) {
    function wputh_excerpt_more() {
        return ' &hellip; ';
    }
}

/* ----------------------------------------------------------
  New hooks
---------------------------------------------------------- */

if (!function_exists('wp_body_open')) {
    function wp_body_open() {
        do_action('wp_body_open');
    }
}

/* ----------------------------------------------------------
  Gutemberg
---------------------------------------------------------- */

if (apply_filters('wputheme_disable_gutemberg', true)) {
    add_action('wp_print_styles', function () {
        wp_dequeue_style('wp-block-library');
    }, 100);

    remove_action( 'init', 'register_block_core_template_part' );
    add_action('after_setup_theme', function () {
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_filter('render_block', 'wp_render_duotone_support');
        remove_filter('render_block', 'wp_render_layout_support_flag');
        remove_filter('render_block', 'wp_restore_group_inner_container');
        remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    }, 100);
}
