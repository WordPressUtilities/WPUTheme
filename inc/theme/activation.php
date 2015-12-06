<?php

/**
 * Configuration after Theme activation
 *
 * @package default
 */

add_action('after_switch_theme', 'wputh_setup_theme');
function wputh_setup_theme() {

    // Options
    $wputh_setup_options = array(
        'date_format' => 'j F Y',
        'permalink_structure' => '/%postname%/',
        'timezone_string' => 'Europe/Paris',
        'time_format' => 'H:i',

        // Default values to avoid unnecessary queries
        'widget_calendar' => '',
        'widget_nav_menu' => '',
        'widget_pages' => '',
        'widget_post_categories' => '',
        'widget_tag_cloud' => '',
        'wpu_home_meta_description' => '',
    );

    $wputh_setup_options = apply_filters('wputh_setup_options', $wputh_setup_options);

    // Setting options
    foreach ($wputh_setup_options as $name => $value) {
        update_option($name, $value);
    }

    // Creating pages
    $pages_site = apply_filters('wputh_pages_site', array());
    foreach ($pages_site as $id => $page) {
        $option = get_option($id);

        // If page doesn't exists
        if (!is_numeric($option)) {

            if (!isset($page['post_status'])) {
                $page['post_status'] = 'publish';
            }
            if (!isset($page['post_type'])) {
                $page['post_type'] = 'page';
            }

            // Create page
            $option_page = wp_insert_post($page);
            if (is_numeric($option_page)) {
                update_option($id, $option_page);
            }
        }
    }

    // Updating permalinks
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
