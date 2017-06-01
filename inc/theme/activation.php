<?php

/* ----------------------------------------------------------
  After theme activation
---------------------------------------------------------- */

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
        'wpu_home_meta_description' => ''
    );

    $wputh_setup_options = apply_filters('wputh_setup_options', $wputh_setup_options);

    // Setting options
    foreach ($wputh_setup_options as $name => $value) {
        update_option($name, $value);
    }

    wputh_pages_site_setup();

    // Updating permalinks
    flush_rewrite_rules();
}
