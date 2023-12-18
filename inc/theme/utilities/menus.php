<?php

/* ----------------------------------------------------------
  Cached nav menu
---------------------------------------------------------- */

function wputh_cached_nav_menu($args = array()) {
    $cache_duration = WEEK_IN_SECONDS;
    $cache_id = 'wputh_cached_menu_' . md5(wputh_get_current_url()) . md5(serialize($args));
    if (isset($args['cache_id'])) {
        $cache_id = $args['cache_id'];
        unset($args['cache_id']);
    }

    /* Keep URL keys */
    $cached_urls = wp_cache_get('wputh_cached_menu_urls');
    if (!is_array($cached_urls)) {
        $cached_urls = array();
    }
    if (!in_array($cache_id, $cached_urls)) {
        $cached_urls[] = $cache_id;
        wp_cache_set('wputh_cached_menu_urls', $cached_urls, '', $cache_duration);
    }

    /* Force return */
    $args['echo'] = false;

    /* Cache menu if not cached */
    $menu = wp_cache_get($cache_id);
    if ($menu === false) {
        $menu = wp_nav_menu($args);
        wp_cache_set($cache_id, $menu, '', $cache_duration);
    }

    $menu = apply_filters('wputh_cached_nav_menu__menu', $menu, $args);

    return $menu;
}

add_action('wp_update_nav_menu_item', 'wputh_cached_nav_menu__clear_cache');
add_action('wp_update_nav_menu', 'wputh_cached_nav_menu__clear_cache');
function wputh_cached_nav_menu__clear_cache() {
    $cached_urls = wp_cache_get('wputh_cached_menu_urls');
    if (!is_array($cached_urls)) {
        return;
    }
    foreach ($cached_urls as $cached_url) {
        wp_cache_delete($cached_url);
    }
    wp_cache_delete('wputh_cached_menu_urls');
}

/* ----------------------------------------------------------
  Default menu
---------------------------------------------------------- */

function wputh_default_menu($args = array()) {
    $defaults = array(
        'menu_id' => '',
        'menu_class' => 'menu',
        'container' => 'div',
        'container_class' => '',
        'echo' => true
    );
    $args = wp_parse_args($args, $defaults);

    $pages_site = wputh_get_posts(array(
        'post_type' => 'page',
        'orderby' => 'ID',
        'order' => 'ASC',
        'posts_per_page' => 5
    ));

    $menu = '<' . $args['container'] . ' class="' . $args['container_class'] . '">';
    $menu .= '<ul ' . ($args['menu_id'] ? 'id="' . $args['menu_id'] . '"' : '') . ' class="' . $args['menu_class'] . '">';
    foreach ($pages_site as $page) {
        $menu .= '<li class="menu-item"><a href="' . get_permalink($page) . '">' . get_the_title($page) . '</a></li>';
    }
    $menu .= '</ul>';
    $menu .= '</' . $args['container'] . '>';

    $menu = apply_filters('wputh_default_menu', $menu, $args);
    if ($args['echo']) {
        echo $menu;
    } else {
        return $menu;
    }
}

/* ----------------------------------------------------------
  Get menu items
---------------------------------------------------------- */

function wputh_get_menu_items($menu_id, $args = array()) {

    $cache_id = 'wputh_get_menu_items__' . $menu_id . '__' . get_locale();
    $cache_duration = 60;
    if (isset($args['cache_duration'])) {
        $cache_duration = $args['cache_duration'];
        unset($args['cache_duration']);
    }

    $menu_items = wp_cache_get($cache_id);
    if ($menu_items !== false) {
        return $menu_items;
    }

    $theme_locations = get_nav_menu_locations();
    if (!isset($theme_locations[$menu_id])) {
        return array();
    }
    $menu_obj = get_term($theme_locations[$menu_id]);
    if (!$menu_obj) {
        return array();
    }
    if (!isset($args['depth'])) {
        $args['depth'] = 1;
    }
    $items = wp_get_nav_menu_items($menu_obj, $args);

    $menu_items = array();
    if (is_array($items)) {
        foreach ($items as $item) {
            if ($item->menu_item_parent && $args['depth'] == 1) {
                continue;
            }

            $attributes = '';
            if ($item->xfn) {
                $attributes .= ' rel="' . esc_attr($item->xfn) . '"';
            }
            if ($item->target) {
                $attributes .= ' target="' . esc_attr($item->target) . '"';
            }

            if ($item->classes) {
                $item_classname = trim(implode(' ', $item->classes));
                if ($item_classname) {
                    $attributes .= ' class="' . esc_attr($item_classname) . '"';
                }
            }
            $menu_items[] = '<a ' . $attributes . ' href="' . $item->url . '"><span>' . $item->title . '</span></a>';
        }
    }

    wp_cache_set($cache_id, $menu_items, '', $cache_duration);

    return $menu_items;
}

/* ----------------------------------------------------------
  Cached posts
---------------------------------------------------------- */

function wputh_get_posts($args = array(), $expires = 60) {
    $ignore_cache = false;
    if (isset($args['wputh_ignore_cache'])) {
        unset($args['wputh_ignore_cache']);
        $ignore_cache = true;
    }

    $cache_id = 'get_posts_' . md5(json_encode($args));

    $posts = wp_cache_get($cache_id);
    if ($posts === false || $ignore_cache) {
        $posts = get_posts($args);
        wp_cache_set($cache_id, $posts, '', $expires);
    }

    return $posts;
}
