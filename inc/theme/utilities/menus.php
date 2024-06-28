<?php

/* ----------------------------------------------------------
  Cached nav menu
---------------------------------------------------------- */

function wputh_cached_nav_menu($args = array()) {
    global $wputheme_wpubasefilecache;
    $cache_duration = WEEK_IN_SECONDS;
    $cache_id = 'wputh_cached_menu_' . md5(wputh_get_current_url()) . md5(serialize($args));
    if (isset($args['cache_id'])) {
        $cache_id = $args['cache_id'];
        unset($args['cache_id']);
    }

    /* Force return */
    $args['echo'] = false;

    /* Cache menu if not cached */
    $menu = $wputheme_wpubasefilecache->get_cache($cache_id, $cache_duration);
    if ($menu === false) {
        $menu = wp_nav_menu($args);
        $wputheme_wpubasefilecache->set_cache($cache_id, $menu);
    }

    $menu = apply_filters('wputh_cached_nav_menu__menu', $menu, $args);

    return $menu;
}

add_action('wp_update_nav_menu_item', 'wputh_cached_nav_menu__clear_cache');
add_action('wp_update_nav_menu', 'wputh_cached_nav_menu__clear_cache');
function wputh_cached_nav_menu__clear_cache() {

    global $wputheme_wpubasefilecache;

    $cache_dir = $wputheme_wpubasefilecache->get_cache_dir();
    $cached_menu_files = glob($cache_dir . 'wputh_cached_menu_*');

    foreach ($cached_menu_files as $cached_menu_file) {
        unlink($cached_menu_file);
    }
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
    global $wputheme_wpubasefilecache;

    $cache_id = 'wputh_get_menu_items__' . $menu_id . '__' . get_locale();
    $cache_duration = 60;
    if (isset($args['cache_duration'])) {
        $cache_duration = $args['cache_duration'];
        unset($args['cache_duration']);
    }

    $menu_items = $wputheme_wpubasefilecache->get_cache($cache_id, $cache_duration);
    if (is_array($menu_items)) {
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

    $wputheme_wpubasefilecache->set_cache($cache_id, $menu_items);

    return $menu_items;
}

/* ----------------------------------------------------------
  Cached posts
---------------------------------------------------------- */

function wputh_get_posts($args = array(), $expires = 60) {

    global $wputheme_wpubasefilecache;

    $ignore_cache = false;
    if (isset($args['wputh_ignore_cache'])) {
        unset($args['wputh_ignore_cache']);
        $ignore_cache = true;
    }

    $cache_id = 'wputh_get_posts_' . md5(json_encode($args));

    $posts = $wputheme_wpubasefilecache->get_cache($cache_id, $expires);
    if ($posts === false || $ignore_cache) {
        $posts = get_posts($args);
        $wputheme_wpubasefilecache->set_cache($cache_id, $posts);
    }

    return $posts;
}
