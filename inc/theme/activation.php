<?php

/* ----------------------------------------------------------
  After theme activation
---------------------------------------------------------- */

add_action('after_switch_theme', 'wputh_setup_theme');
function wputh_setup_theme() {

    // Default values to avoid unnecessary queries
    $wputh_setup_options = array(
        'widget_calendar' => '',
        'widget_nav_menu' => '',
        'widget_pages' => '',
        'widget_post_categories' => '',
        'widget_tag_cloud' => ''
    );

    $wputh_setup_options = apply_filters('wputh_setup_options', $wputh_setup_options);

    // Setting options
    foreach ($wputh_setup_options as $name => $value) {
        update_option($name, $value);
    }

    wputh_pages_site_setup();

    wputh_pages_set_privacy_policy();

    // Updating permalinks
    flush_rewrite_rules();
}
