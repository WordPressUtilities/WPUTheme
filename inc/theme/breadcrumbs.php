<?php

if (function_exists('wputh_get_breadcrumbs')) {
    return;
}

function wputh_get_breadcrumbs($elements_ariane = array()) {

    // Hide breadcrumbs if called on homepage
    if (is_home() || is_front_page()) {
        return array();
    }

    $home_ids = array();
    $home_id = get_option('page_on_front');
    if ($home_id) {
        $home_ids[] = $home_id;
        if (function_exists('pll_get_post_translations')) {
            $home_ids = pll_get_post_translations($home_id);
        }
    }

    $elements_ariane = array();
    $elements_ariane['home'] = array(
        'name' => __('Home', 'wputh'),
        'link' => home_url()
    );

    global $wp;
    $current_view_url = add_query_arg($wp->query_vars, home_url($wp->request));

    $elements_ariane = apply_filters('wputh_get_breadcrumbs__after_home', $elements_ariane);

    if (is_single() && !is_singular('post')) {
        $display_post_type_archive = apply_filters('wputh_get_breadcrumbs__display_post_type_archive', false);
        $display_post_type_archive = apply_filters('wputh_get_breadcrumbs__display_post_type_archive__' . get_post_type(), $display_post_type_archive);
        if ($display_post_type_archive) {
            $obj = get_post_type_object(get_post_type());
            $elements_ariane['archive-post-type-' . $obj->name] = array(
                'name' => $obj->label,
                'link' => get_post_type_archive_link($obj->name)
            );
        }
    }

    if (is_singular()) {
        $main_category = wputh_get_main_term(get_the_ID(), 'category');
        if ($main_category) {
            $elements_ariane = wputh_breadcrumbs_set_parent_categories($elements_ariane, $main_category);
            $elements_ariane['category'] = array(
                'name' => $main_category->name,
                'link' => get_category_link($main_category->term_id)
            );
        }
    }

    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if (is_object($term)) {

            // Adding parent
            $elements_ariane = wputh_breadcrumbs_set_parent_categories($elements_ariane, $term);

            // Adding category
            $elements_ariane[$term->taxonomy] = array(
                'name' => $term->name,
                'last' => 1
            );
        }
    } else if (is_post_type_archive()) {
        $obj = get_queried_object();
        $elements_ariane['archive-post-type-' . $obj->name] = array(
            'name' => $obj->label,
            'link' => get_post_type_archive_link($obj->name),
            'last' => 1
        );
    } else {
        if (is_archive() && class_exists('WPUSEO')) {
            $wpu_seo = new WPUSEO();
            $shown_title = $wpu_seo->get_displayed_title(false);

            // Adding category
            $elements_ariane['archive-page-name'] = array(
                'name' => $shown_title,
                'link' => $current_view_url,
                'last' => 1
            );
        }
    }

    $elements_ariane = apply_filters('wputh_get_breadcrumbs__before_singular', $elements_ariane);

    if (is_singular() || is_page()) {

        /* Parent tax */
        $parent_tax = apply_filters('wputh_get_breadcrumbs__before_singular__parent_tax', false, get_post_type());
        if ($parent_tax) {
            $parent_terms = get_the_terms(get_the_ID(), $parent_tax);
            if (is_array($parent_terms) && isset($parent_terms[0])) {
                $elements_ariane['parent-term--' . $parent_terms[0]->term_id] = array(
                    'name' => $parent_terms[0]->name,
                    'link' => get_term_link($parent_terms[0]),
                    'last' => false
                );
            }
        }

        /* Parent page */
        $page_id = get_the_ID();
        if (wp_get_post_parent_id($page_id)) {
            $parent_pages = array();
            while ($page_id = wp_get_post_parent_id($page_id)) {
                /* Dont use the parent page if it is an homepage */
                if (in_array($page_id, $home_ids)) {
                    break;
                }
                $parent_pages['parent-page--' . $page_id] = array(
                    'link' => get_permalink($page_id),
                    'name' => get_the_title($page_id),
                    'last' => false
                );
            }

            $parent_pages = array_reverse($parent_pages);
            $elements_ariane += $parent_pages;
        }

        /* Page */
        $elements_ariane['single-page'] = array(
            'name' => get_the_title(),
            'link' => get_permalink(),
            'last' => 1
        );
    }

    if (is_404()) {
        $elements_ariane['404-error'] = array(
            'name' => __('404 Error', 'wputh'),
            'link' => home_url('404'),
            'last' => 1
        );
    }

    if (is_search()) {
        $elements_ariane['search-results'] = array(
            'name' => sprintf(__('Search results for "%s"', 'wputh'), get_search_query()),
            'link' => $current_view_url,
            'last' => 1
        );
    }

    $elements_ariane = apply_filters('wputh_get_breadcrumbs__after_all', $elements_ariane);

    return $elements_ariane;
}

function wputh_breadcrumbs_set_parent_categories($elements_ariane, $term) {

    // Checking for parent categories
    $cat_tmp = $term->parent;
    $parents_categories = array();
    while ($cat_tmp != 0) {
        $category_parent = get_terms(array(
            'include' => $cat_tmp,
            'taxonomy' => $term->taxonomy,
        ));
        if (isset($category_parent[0])) {
            $parents_categories['parent-category-' . $cat_tmp] = array(
                'name' => $category_parent[0]->name,
                'link' => get_term_link($category_parent[0])
            );
            $cat_tmp = $category_parent[0]->parent;
        } else {
            $cat_tmp = 0;
        }
    }

    // Reordering & merging parents
    if (!empty($parents_categories)) {
        $parents_categories = array_reverse($parents_categories);
        $elements_ariane = array_merge($elements_ariane, $parents_categories);
    }

    return $elements_ariane;
}

function wputh_get_breadcrumbs_html($elements_ariane) {
    if(!is_array($elements_ariane) || empty($elements_ariane)) {
        return '';
    }
    $html = '';
    $html .= '<ul class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">';
    $i = 0;
    foreach ($elements_ariane as $id => $element) {
        $last = (isset($element['last']) && $element['last'] == 1);
        $element = apply_filters('wputh_get_breadcrumbs_html__element', $element, $last);
        $itemAttributes = ($last ? '' : 'itemprop="item"') . ' class="element-ariane element-ariane--' . $id . ' ' . ($last ? 'is-last' : '') . '"';
        $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        $element_name = '<span itemprop="name">' . $element['name'] . '</span>';
        if (isset($element['link'])) {
            $html .= '<a ' . $itemAttributes . ' href="' . $element['link'] . '">' . $element_name . '</a>';
        } else {
            $html .= '<strong ' . $itemAttributes . '>' . $element_name . '</strong>';
        }
        $html .= '<meta itemprop="position" content="' . (++$i) . '" />';
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
