<?php

/* ----------------------------------------------------------
  Loop
---------------------------------------------------------- */

/**
 * Get the loop : returns a main loop
 *
 * @param unknown $params (optional)
 * @return unknown
 */
function get_the_loop($params = array()) {
    global $post, $wp_query, $wpdb;

    /* Get params */
    $default_params = array(
        'no_results_content' => '<p>' . __('Sorry, no search results for this query.', 'wputh') . '</p>',
        'loop_item_modifiers' => array(),
        'loop_container_classes' => 'list-loops',
        'loop' => 'loop-small'
    );

    if (!is_array($params)) {
        $params = array($params);
    }

    $parameters = array_merge($default_params, $params);

    /* Start the loop */
    ob_start();
    if (have_posts()) {
        echo '<div class="' . $parameters['loop_container_classes'] . '">';
        while (have_posts()) {
            the_post();
            set_query_var('get_the_loop__parameters', $parameters);
            get_template_part($parameters['loop']);
            /* Retrieve with : $parameters = get_query_var('get_the_loop__parameters'); */
        }
        echo '</div>';
        echo wputh_paginate();
    } else {
        echo $parameters['no_results_content'];
    }
    wp_reset_query();

    /* Returns captured content */
    $content = ob_get_clean();
    return apply_filters('wputh__get_the_loop__content', $content);
}

/* ----------------------------------------------------------
  Get loops
---------------------------------------------------------- */

function wputheme_get_loop_from_ids($wpq_posts = array(), $classname = 'loop-list', $loopfile = 'loop-post.php', $args = array()) {
    if (!$wpq_posts) {
        return '';
    }
    $html = '<ul class="' . $classname . '">';
    foreach ($wpq_posts as $wpq_post) {
        $html .= '<li>';
        $html .= wputheme_get_loop_item_from_id($wpq_post, $loopfile, $args);
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

function wputheme_get_loop_item_from_id($post_obj, $loopfile = 'loop-post.php', $args = array()) {
    global $post;
    if (is_numeric($post_obj)) {
        $post_obj = get_post($post_obj);
    }
    $loop_file = get_stylesheet_directory() . '/tpl/loops/' . $loopfile;
    if (!file_exists($loop_file)) {
        return '<div>Error : ' . $loopfile . ' does not exists</div>';
    }
    $old_post = $post;
    $post = $post_obj;
    ob_start();
    include $loop_file;
    $html = ob_get_clean();
    $post = $old_post;
    return $html;
}

/* ----------------------------------------------------------
  Complete post ids
---------------------------------------------------------- */

/**
 * Complete furnished post ids with latest post ids, to get to a desired number
 * @param  array  $settings  Settings for this function
 * @return array             Args ready to be inserted in a query
 */
function wputh_complete_post_ids($settings = array()) {
    if (!is_array($settings)) {
        $settings = array();
    }
    if (!isset($settings['post_type'])) {
        $settings['post_type'] = 'post';
    }
    if (!isset($settings['posts_per_page']) || !is_numeric($settings['posts_per_page'])) {
        $settings['posts_per_page'] = 3;
    }
    if (!isset($settings['post_ids']) || !is_array($settings['post_ids'])) {
        $settings['post_ids'] = array();
    }

    $posts = array();
    if (!empty($settings['post_ids'])) {
        $posts = wputh_get_posts(array(
            'post_type' => $settings['post_type'],
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => 'post__in',
            'fields' => 'ids',
            'post__in' => $settings['post_ids']
        ));
    }

    $nb_posts = count($posts);
    if ($nb_posts < $settings['posts_per_page']) {
        $extra_posts_nb = $settings['posts_per_page'] - $nb_posts;
        $extra_posts = wputh_get_posts(array(
            'post_type' => $settings['post_type'],
            'posts_per_page' => $extra_posts_nb,
            'orderby' => 'post__in',
            'fields' => 'ids',
            'post__not_in' => $posts
        ));
        $posts = array_merge($posts, $extra_posts);
    }
    return array(
        'post_type' => $settings['post_type'],
        'posts_per_page' => $settings['posts_per_page'],
        'orderby' => 'post__in',
        'fields' => 'ids',
        'post__in' => $posts
    );
}

/* ----------------------------------------------------------
  Post have a "has more" tag
---------------------------------------------------------- */

function wputh_has_more($post = false) {
    if (!$post) {
        global $post;
    }
    if (!is_object($post)) {
        return '';
    }
    return preg_match('/<!--more(.*?)?-->/', $post->post_content);
}

/* ----------------------------------------------------------
  Update without revisions
---------------------------------------------------------- */

/**
 * Update without creating post revisions
 * @param  array $args  post arguments
 * @return void
 */
function wputh_update_without_revision($args = array()) {
    remove_action('post_updated', 'wp_save_post_revision');
    remove_action('pre_post_update', 'wp_save_post_revision');
    $update_action = wp_update_post($args, true);
    add_action('post_updated', 'wp_save_post_revision');
    add_action('pre_post_update', 'wp_save_post_revision');
    return $update_action;
}
