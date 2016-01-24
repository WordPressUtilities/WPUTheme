<?php
include dirname(__FILE__) . '/../../z-protect.php';

/* ----------------------------------------------------------
   Options for the plugin "WPU Options"
------------------------------------------------------- */

add_filter('wpu_options_boxes', 'set_wpu_options_boxes', 10, 3);

if (!function_exists('set_wpu_options_boxes')) {
    function set_wpu_options_boxes($boxes) {
        $boxes['virtual_contacts'] = array(
            'name' => __('Contacts', 'wputh')
        );
        $boxes['social_networks'] = array(
            'name' => __('Social networks', 'wputh')
        );
        $boxes['pages_id'] = array(
            'name' => __('Pages IDs', 'wputh')
        );
        return $boxes;
    }
}

add_filter('wpu_options_fields', 'set_wputh_options_fields_default', 10, 3);
add_filter('wpu_options_fields', 'set_wputh_options_fields', 10, 3);

if (!function_exists('set_wputh_options_fields')) {
    function set_wputh_options_fields($options) {

        // Virtual contacts
        $options['wpu_opt_email'] = array(
            'label' => __('Email address', 'wputh') ,
            'box' => 'virtual_contacts',
            'type' => 'email',
            'test' => 'email'
        );

        return $options;
    }
}

function set_wputh_options_fields_default($options) {

    // Social networks
    $wpu_social_links = wputh_get_social_links_ids();
    foreach ($wpu_social_links as $id => $name) {
        $options['social_' . $id . '_url'] = array(
            'label' => $name . ' URL',
            'box' => 'social_networks'
        );
        if ($id == 'twitter') {
            $options['social_' . $id . '_username'] = array(
                'label' => $name . ' username',
                'box' => 'social_networks'
            );
        }
    }

    // Create pages IDs from list defined in functions.php
    $pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));
    foreach ($pages_site as $id => $page) {
        $options[$id] = array(
            'label' => $page['post_title'],
            'box' => 'pages_id',
            'type' => 'page'
        );
    }

    return $options;
}

