<?php

/* ----------------------------------------------------------
  Social Links
---------------------------------------------------------- */

function wputh_get_social_links_ids() {
    return apply_filters('wputheme_social_links', array(
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram'
    ));
}

function wputh_get_social_links($wpu_social_links_ids = array()) {
    if (!$wpu_social_links_ids || !is_array($wpu_social_links_ids)) {
        $wpu_social_links_ids = wputh_get_social_links_ids();
    }
    $links = array();
    foreach ($wpu_social_links_ids as $id => $name) {
        $opt_id = 'social_' . $id . '_url';
        $opt = function_exists('wputh_l18n_get_option') ? wputh_l18n_get_option($opt_id) : get_option($opt_id);
        $social_link = trim($opt);
        if (!empty($social_link)) {
            $links[$id] = array(
                'name' => $name,
                'url' => $social_link
            );
        }
    }
    return $links;
}

function wputh_get_social_links_html($wrapper_classname = 'header__social', $display_type = false, $template = '', $wpu_social_links = array()) {
    if (!$wpu_social_links || !is_array($wpu_social_links)) {
        $wpu_social_links = wputh_get_social_links();
    }
    if (!$wpu_social_links) {
        return '';
    }
    $html = '<ul class="wputh-social-links ' . $wrapper_classname . '">';
    foreach ($wpu_social_links as $id => $link) {
        $html .= '<li><a rel="me noreferrer noopener" href="' . $link['url'] . '" class="' . $id . '" title="' . sprintf(__('%s: Follow %s (open in new window)', 'wputh'), $link['name'], get_bloginfo('name')) . '" target="_blank">';
        switch ($display_type) {
        case 'custom':
            $html .= str_replace('%s', $id, $template);
            break;
            break;
        case 'icon':
            $html .= '<i aria-hidden="true" class="icon icon_' . $id . '"><span class="screen-reader-text">' . $link['name'] . '</span></i>';
            break;
        default:
            $html .= $link['name'];
        }
        $html .= '</a></li>';
    }
    $html .= '</ul>';
    return $html;
}
