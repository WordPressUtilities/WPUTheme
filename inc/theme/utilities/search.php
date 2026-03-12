<?php

/* ----------------------------------------------------------
  Search
---------------------------------------------------------- */

/* Get number of results for current search
-------------------------- */

function wputheme_search_get_total_results() {
    if (!get_search_query()) {
        return 0;
    }
    $p = get_posts(array(
        'fields' => 'ids',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => true,
        'post_type' => array_keys(apply_filters('wputheme_search_post_types', array())),
        's' => get_search_query()
    ));
    return count($p);
}

/* Get number of results for current search and post type
-------------------------- */

function wputheme_search_get_results_string($post_type) {
    $nb_results = get_posts(array(
        'fields' => 'ids',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => true,
        'post_type' => array($post_type),
        's' => get_search_query()
    ));
    $count = count($nb_results);
    $post_type_obj = get_post_type_object($post_type);
    $singular_name = strtolower($post_type_obj->labels->singular_name);
    $plural_name = strtolower($post_type_obj->labels->name);

    $no_results_str = apply_filters('wputheme_search_no_results_label', __('No %s', 'wputh'), $singular_name);

    if ($nb_results) {
        $html = sprintf('%s %s', number_format_i18n($count), $count == 1 ? $singular_name : $plural_name);
    } else {
        $html = sprintf($no_results_str, $singular_name);
    }
    return $html;
}

/* Quick ajax load more
-------------------------- */

add_action('wp_footer', function () {
    if (!apply_filters('wputheme_search__enabled', false)) {
        return;
    }

    if (!isset($_GET['s']) && !is_search()) {
        return;
    }
    ?>
<script>jQuery('body').on('click', '[data-search-pt][data-search-paged]', function(e) {
    e.preventDefault();
    var $button = jQuery(this),
        $wrapper = $button.closest('.search-load-more-wrapper');
    $wrapper.addClass('is-loading');
    $button.prop('disabled', 1);
    jQuery.ajax({
        url: "/",
        data: {
            search_ajax_pt: $button.attr('data-search-pt'),
            search_ajax_paged: $button.attr('data-search-paged'),
            s: $button.attr('data-s'),
        }
    })
    .done(function(data) {
        /* Inject content */
        $wrapper.after(jQuery(data));
        /* Delete button */
        $wrapper.remove();
    });
});</script>
<?php
});

/* ----------------------------------------------------------
  Display search
---------------------------------------------------------- */

add_action('wp', function () {
    if (!isset($_GET['s'], $_GET['search_ajax_pt'], $_GET['search_ajax_paged'])) {
        return;
    }
    $post_types = apply_filters('wputheme_search_post_types', array());
    if (!is_numeric($_GET['search_ajax_paged']) || !array_key_exists($_GET['search_ajax_pt'], $post_types)) {
        return;
    }
    echo wputheme_search_get_post_type_content($_GET['search_ajax_pt'], $post_types[$_GET['search_ajax_pt']], array(
        'paged' => $_GET['search_ajax_paged']
    ));
    die;
});

/* Add content
-------------------------- */

function wputheme_search_get_post_type_content($post_type_key, $pt_settings, $args = array()) {
    if (!is_array($args)) {
        $args = array();
    }

    $load_more_label = apply_filters('wputheme_search_load_more_label', __('Load more', 'wputh'));
    $load_more_classname = apply_filters('wputheme_search_load_more_classname', 'wputheme-button');

    /* Per page */
    if (!isset($args['per_page'])) {
        $args['per_page'] = 3;
    }
    if (isset($pt_settings['per_page'])) {
        $args['per_page'] = $pt_settings['per_page'];
    }

    /* Paged */
    if (!isset($args['paged']) || $args['paged'] == 0) {
        $args['paged'] = 1;
    }

    /* Classname */
    if (!isset($pt_settings['classname'])) {
        $pt_settings['classname'] = 'loops-list';
    }
    if (!isset($pt_settings['item_classname'])) {
        $pt_settings['item_classname'] = 'loops-item';
    }
    $start = $args['per_page'] * $args['paged'] - $args['per_page'];
    $max = $start + $args['per_page'];
    $wpq_search = new WP_Query(array(
        'posts_per_page' => $args['per_page'],
        'offset' => $start,
        'ignore_sticky_posts' => true,
        'post_type' => $post_type_key,
        's' => get_search_query()
    ));
    $html = '';
    if ($wpq_search->have_posts()) {
        $html .= '<ul class="' . esc_attr($pt_settings['classname']) . '" data-perpage="' . esc_attr($args['per_page']) . '" data-max="' . esc_attr($max) . '" data-found="' . esc_attr($wpq_search->found_posts) . '">';
        while ($wpq_search->have_posts()) {
            $wpq_search->the_post();
            $html .= '<li class="' . esc_attr($pt_settings['item_classname']) . '">';
            ob_start();
            include get_stylesheet_directory() . '/tpl/loops/' . $pt_settings['tpl'];
            $html .= ob_get_clean();
            $html .= '</li>';
        }
        $html .= '</ul>';
        if ($wpq_search->found_posts > $max) {
            $html .= '<div class="search-load-more-wrapper">';
            $html .= '<button type="button" class="' . esc_attr($load_more_classname) . '" data-s="' . esc_attr(get_search_query()) . '" data-search-pt="' . esc_attr($post_type_key) . '" data-search-paged="' . esc_attr($args['paged'] + 1) . '"><span>' . $load_more_label . '</span></button>';
            $html .= '</div>';
        }
    }
    wp_reset_postdata();

    return $html;
}

/* ----------------------------------------------------------
  Fix : Allow search in custom post types
---------------------------------------------------------- */

add_filter('pre_get_posts', function ($query) {
    if (!apply_filters('wputheme_search__enabled', false)) {
        return $query;
    }
    if ($query->is_search && !is_admin() && isset($query->query['post_type'])) {
        $query->set('post_type', $query->query['post_type']);
    }
    return $query;
});
