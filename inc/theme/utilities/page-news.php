<?php

/**
 * Get a term switcher HTML
 * @param array $args Arguments to customize the switcher (taxonomy, display of "view all" link, classnames...)
 * @return string HTML of the term switcher
 */
function wputh_get_term_switcher($args = array()) {
    $filter_html = '';

    if (!is_array($args)) {
        $args = array();
    }
    $args = wp_parse_args($args, array(
        'taxonomy' => 'category',
        'view_all_always' => true,
        'view_all_text' => __('View all', 'wputh'),
        'view_all_url' => site_url(),
        'classname_current' => 'current',
        'classname_item_current' => 'item-current',
        'classname_wrapper' => 'term-switcher__wrapper',
        'classname_list' => 'term-switcher',
        'classname_item' => 'term-switcher__item',
        'classname_link' => 'term-switcher__link'
    ));
    $args = apply_filters('wputh_term_switcher_args', $args);
    $terms = get_terms(apply_filters('wputh_term_switcher_terms_query', array(
        'taxonomy' => $args['taxonomy'],
        'hide_empty' => true
    ), $args));
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    $can_show_view_all = is_tax($args['taxonomy']) || (is_category() && $args['taxonomy'] == 'category') || (is_tag() && $args['taxonomy'] == 'post_tag');

    /* View all */
    if ($can_show_view_all || $args['view_all_always']) {
        $filter_html .= '<li class="' . esc_attr($args['classname_item']) . ' ' . (!$can_show_view_all ? esc_attr($args['classname_item_current']) : '') . '">';
        $filter_html .= '<a class="' . esc_attr($args['classname_link']) . ' ' . (!$can_show_view_all ? esc_attr($args['classname_current']) : '') . '" href="' . esc_url($args['view_all_url']) . '">' . esc_html($args['view_all_text']) . '</a>';
        $filter_html .= '</li>';
    }

    foreach ($terms as $term) {
        /* Exclude terms hidden from search */
        if (get_term_meta($term->term_id, 'wpuseo_hide_search', true)) {
            continue;
        }
        if (apply_filters('wputh_term_switcher_exclude_term', false, $term, $args)) {
            continue;
        }

        $is_active = is_tax($args['taxonomy'], $term->term_id);
        if ($args['taxonomy'] == 'category' && is_category($term->term_id)) {
            $is_active = true;
        }
        if ($args['taxonomy'] == 'post_tag' && is_tag($term->term_id)) {
            $is_active = true;
        }

        $filter_html .= '<li class="' . esc_attr($args['classname_item']) . ' ' . ($is_active ? esc_attr($args['classname_item_current']) : '') . '">';
        $filter_html .= '<a class="' . esc_attr($args['classname_link']) . '" href="' . get_term_link($term) . '"><span>' . esc_html($term->name) . '</span></a>';
        $filter_html .= '</li>';
    }

    $return_string = '';
    if (!empty($filter_html)) {
        $return_string = '<div class="' . esc_attr($args['classname_wrapper']) . '">';
        $return_string .= '<ul class="' . esc_attr($args['classname_list']) . '">' . $filter_html . '</ul>';
        $return_string .= '</div>';
    }

    return $return_string;
}

/**
 * Get the query args for news page (post type + taxonomy if on a taxonomy archive)
 * @param string $post_type Post type to query
 * @param string $tax Taxonomy to filter by if on a taxonomy archive
 * @return array Query args
 */
function wputh_pagenews_get_query($post_type = 'post', $tax = 'category') {
    $q = array(
        'post_type' => $post_type,
        'ignore_sticky_posts' => 1,
        'paged' => get_query_var('paged'),
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
    if (($tax == 'category' && is_category()) || ($tax == 'post_tag' && is_tag()) || is_tax($tax)) {
        $q['tax_query'] = array(
            array(
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => get_queried_object()->slug
            )
        );
    }

    $q = apply_filters('wputh_pagenews_query_args', $q, $post_type, $tax);

    return $q;
}
