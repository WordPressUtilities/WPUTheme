<?php

/* ----------------------------------------------------------
  Pages IDs
---------------------------------------------------------- */

if (!function_exists('wputh_set_pages_site')) {
    function wputh_set_pages_site($pages_site) {
        $pages_site['about__page_id'] = array(
            'constant' => 'ABOUT__PAGE_ID',
            'post_title' => 'A Propos',
            'post_content' => '<p>A Propos de ce site.</p>',
        );
        $pages_site['mentions__page_id'] = array(
            'constant' => 'MENTIONS__PAGE_ID',
            'post_title' => 'Mentions légales',
            'post_content' => '<p>Contenu des mentions légales</p>',
        );
        return $pages_site;
    }
}

add_filter('wputh_pages_site', 'wputh_set_pages_site');

$pages_site = apply_filters('wputh_pages_site', array());

foreach ($pages_site as $id => $option) {
    if (!isset($option['constant'])) {
        $option['constant'] = strtoupper($id);
    }
    $opt_id = get_option($id);
    define($option['constant'], $opt_id);
    $link = '#';
    if (is_numeric($opt_id)) {
        $link = get_page_link($opt_id);
    }
    define($option['constant'] . '__LINK', $link);
}
