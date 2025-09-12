<?php

/* ----------------------------------------------------------
  Cached nav menu
---------------------------------------------------------- */

function wputh_cached_nav_menu($args = array()) {
    $wputheme_wpubasefilecache = wputheme_get_wpubasefilecache();
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

    $wputheme_wpubasefilecache = wputheme_get_wpubasefilecache();

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
    $wputheme_wpubasefilecache = wputheme_get_wpubasefilecache();

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

    $wputheme_wpubasefilecache = wputheme_get_wpubasefilecache();

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

/* ----------------------------------------------------------
  UX fix : reduce opacity of menu items that exceed the max depth
---------------------------------------------------------- */

add_action('admin_head-nav-menus.php', function () {

    $max_possible_depth = apply_filters('wputh_menus_max_depth', 5);

    /* Get rules */
    $rules = apply_filters('wputh_default_menus_depth', array());
    if (empty($rules) || !is_array($rules)) {
        return;
    }

    /* Get locations */
    $locations = get_nav_menu_locations();
    if (empty($locations)) {
        return;
    }

    /* Retrieve current menu ID */
    $current_menu_id = isset($_GET['menu']) ? (int) $_GET['menu'] : 0;
    if (!$current_menu_id) {
        $current_menu_id = absint(get_user_option('nav_menu_recently_edited'));
        if (!$current_menu_id) {
            return;
        }
    }

    /* Check if current menu ID matches any rule */
    $matched_depth = null;
    foreach ($rules as $loc => $max_depth) {
        if (isset($locations[$loc]) && (int) $locations[$loc] === $current_menu_id) {
            $matched_depth = (int) $max_depth;
            break;
        }
    }
    if ($matched_depth === null || $matched_depth < 0 || $matched_depth > $max_possible_depth) {
        return;
    }

    /* Generate CSS selectors */
    $selectors = [];
    for ($i = $matched_depth + 1; $i <= $max_possible_depth; $i++) {
        $selectors[] = "#menu-to-edit .menu-item-depth-$i";
    }

    if (!empty($selectors)) {
        echo '<style>' . implode(',', $selectors) . '{opacity: 0.3;}' . '</style>';
    }

});
