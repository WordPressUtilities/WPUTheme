<?php

/* ----------------------------------------------------------
  Functions
---------------------------------------------------------- */

function get_pages_sitemap_child_of($post_type, $sitemap_pages = array(), $parent = 0) {
    $content = '';

    foreach ($sitemap_pages as $id => $sitemap_page) {
        if ($sitemap_page['parent'] == $parent) {
            $content .= '<li>';
            if (!$sitemap_page['title']) {
                $sitemap_page['title'] = $sitemap_page['permalink'];
            }
            $content .= '<a href="' . $sitemap_page['permalink'] . '">' . $sitemap_page['title'] . '</a>';
            $content .= get_pages_sitemap_child_of($post_type, $sitemap_pages, $id);
            $content .= '</li>';
        }
    }

    if (!empty($content)) {
        $content = '<ul class="level">' . $content . '</ul>';
    }
    return $content;
}

/* ----------------------------------------------------------
  Post Queries
---------------------------------------------------------- */

$sitemap_posts = array();

/* Set post types
-------------------------- */

$post_types = array(
    'page' => array()
);
$post_types = apply_filters('wputheme_sitemap_post_types', $post_types, get_the_ID());

/* Default args
-------------------------- */

$default_args = array(
    'posts_per_page' => 500,
    'post_status' => 'publish',
    'post__not_in' => array(get_the_ID()),
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'wpuseo_hide_search',
            'compare' => 'NOT EXISTS'
        ),
        array(
            'key' => 'wpuseo_hide_search',
            'value' => '0',
            'compare' => '='
        )
    )
);
$wputheme_homepage_id = -1;
if (get_option('show_on_front') == 'page') {
    $wputheme_homepage_id = get_option('page_on_front');
}

$default_args = apply_filters('wputheme_sitemap_default_args', $default_args);

/* Set posts
-------------------------- */

foreach ($post_types as $_post_type => $post_type_infos) {
    if (!get_post_type_object($_post_type)) {
        continue;
    }

    $args = array(
        'post_type' => $_post_type
    );

    if (!is_array($post_type_infos)) {
        $post_type_infos = array();
    }

    if (!isset($post_type_infos['title'])) {
        $post_type_infos['title'] = $post_type;
        $post_type_infos_raw = get_post_type_object($_post_type);
        if (is_object($post_type_infos_raw) && !is_wp_error($post_type_infos_raw)) {
            $post_type_infos['title'] = $post_type_infos_raw->label;
        }
    }

    $wpq_sitemap = get_posts(array_merge($args, $default_args));
    $sitemap_pages = array();

    if ($_post_type != 'post') {
        $post_type_object = get_post_type_object($_post_type);
        if ($post_type_object && !empty($post_type_object->has_archive) && !empty($post_type_object->public)) {
            $sitemap_pages['main'] = array(
                'permalink' => get_post_type_archive_link($_post_type),
                'title' => $post_type_infos['title'],
                'parent' => 0
            );
        }

    }

    foreach ($wpq_sitemap as $sitepost) {
        $sitemap_pages[$sitepost->ID] = array(
            'permalink' => get_permalink($sitepost),
            'title' => get_the_title($sitepost),
            'parent' => $sitepost->post_parent
        );
    }

    /* Move homepage to first position */
    if ($wputheme_homepage_id > 0 && isset($sitemap_pages[$wputheme_homepage_id])) {
        $home_page_item = $sitemap_pages[$wputheme_homepage_id];
        unset($sitemap_pages[$wputheme_homepage_id]);
        $sitemap_pages = array($wputheme_homepage_id => $home_page_item) + $sitemap_pages;
    }

    $sitemap_posts[] = array(
        'title' => $post_type_infos['title'],
        'post_type' => $_post_type,
        'posts' => $sitemap_pages
    );
}

/* ----------------------------------------------------------
  Tax queries
---------------------------------------------------------- */

/* Set taxonomies
-------------------------- */

$taxonomies = array(
    'category' => array(
        'title' => 'Categories'
    )
);
$taxonomies = apply_filters('wputheme_sitemap_taxonomies', $taxonomies, get_the_ID());

/* Default args
-------------------------- */

$default_args_tax = array(
    'hide_empty' => true
);

$default_args_tax = apply_filters('wputheme_sitemap_default_args_tax', $default_args_tax);

/* Set posts
-------------------------- */

foreach ($taxonomies as $_tax => $tax_infos) {
    if (!taxonomy_exists($_tax)) {
        continue;
    }
    $taxonomy_object = get_taxonomy($_tax);
    if (!$taxonomy_object || empty($taxonomy_object->rewrite)) {
        continue;
    }
    $args = array(
        'taxonomy' => $_tax
    );
    $terms = get_terms(array_merge($args, $default_args_tax));
    $sitemap_pages = array();
    foreach ($terms as $taxitem) {
        $sitemap_pages[$taxitem->term_id] = array(
            'type' => 'tax',
            'permalink' => get_term_link($taxitem),
            'title' => $taxitem->name,
            'parent' => $taxitem->parent
        );
    }

    $sitemap_posts[] = array(
        'title' => $tax_infos['title'],
        'post_type' => $_tax,
        'posts' => $sitemap_pages
    );
}
