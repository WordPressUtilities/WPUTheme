<?php

/* ----------------------------------------------------------
  Functions
---------------------------------------------------------- */

function get_pages_sitemap_child_of($post_type, $sitemap_pages = array(), $parent = 0) {
    $content = '';

    if ($parent == 0 && $post_type == 'page') {
        $content .= '<li><a href="' . home_url() . '">' . __('Home page', 'wputh') . '</a></li>';
    }

    foreach ($sitemap_pages as $id => $sitemap_page) {
        if ($sitemap_page['parent'] == $parent) {
            $content .= '<li>';
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

/* Set post types
-------------------------- */

$post_types = array(
    'page' => array(
        'title' => 'Pages'
    )
);
$post_types = apply_filters('wputheme_sitemap_post_types', $post_types, get_the_ID());

/* Default args
-------------------------- */

$default_args = array(
    'posts_per_page' => 500,
    'post_status' => 'publish',
    'post__not_in' => array(get_the_ID())
);

$default_args = apply_filters('wputheme_sitemap_default_args', $default_args);

/* Set posts
-------------------------- */

foreach ($post_types as $_post_type => $post_type_infos) {
    $args = array(
        'post_type' => $_post_type
    );
    $wpq_sitemap = get_posts(array_merge($args, $default_args));
    $sitemap_pages = array();
    foreach ($wpq_sitemap as $sitepost) {
        $sitemap_pages[$sitepost->ID] = array(
            'permalink' => get_permalink($sitepost),
            'title' => get_the_title($sitepost),
            'parent' => $sitepost->post_parent
        );
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
