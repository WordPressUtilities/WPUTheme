<?php
/**
 * Utilities
 *
 * @package default
 */


/**
 * Get the loop : returns a main loop
 *
 * @param unknown $params (optional)
 * @return unknown
 */
function get_the_loop( $params = array() ) {
    global $post, $wp_query, $wpdb;

    /* Get params */
    $default_params = array(
        'loop' => 'loop-small'
    );

    if ( !is_array( $params ) ) {
        $params = array( $params );
    };

    $parameters = array_merge( $default_params, $params );

    /* Start the loop */
    ob_start();
    if ( have_posts() ) {
        echo '<div class="list-loops">';
        while ( have_posts() ) {
            the_post();
            get_template_part( $parameters['loop'] );
        }
        echo '</div>';
        include get_template_directory() . '/tpl/paginate.php';
    }
    else {
        echo '<p>' . __( 'Sorry, no search results for this query.', 'wputh' ) . '</p>';
    }
    wp_reset_query();

    /* Returns captured content */
    $content = ob_get_clean();
    return $content;
}


/**
 * Get comments title
 *
 * @param unknown $count_comments
 * @param unknown $zero           (optional)
 * @param unknown $one            (optional)
 * @param unknown $more           (optional)
 * @param unknown $closed         (optional)
 * @return unknown
 */
function wputh_get_comments_title( $count_comments, $zero = false, $one = false, $more = false, $closed = false ) {
    global $post;
    $return = '';
    if ( is_array( $count_comments ) ) {
        $count_comments = count( $count_comments );
    }
    if( !is_numeric($count_comments) ){
        $count_comments = $post->comment_count;
    }
    if ( $zero === false ) {
        $zero = __( '<strong>no</strong> comments', 'wputh' );
    }
    if ( $one === false ) {
        $one = __( '<strong>1</strong> comment', 'wputh' );
    }
    if ( $more === false ) {
        $more = __( '<strong>%s</strong> comments', 'wputh' );
    }
    if ( $closed === false ) {
        $closed = __( 'Comments are closed', 'wputh' );
    }
    if ( !comments_open() ) {
        $return = $closed;
    }
    else {
        switch ( $count_comments ) {
        case 0:
            $return = $zero;
            break;
        case 1:
            $return = $one;
            break;
        default :
            $return = sprintf( $more, $count_comments );
        }
    }

    return $return;
}


/**
 * Get comment author name with link
 *
 * @param unknown $comment
 * @return unknown
 */
function wputh_get_comment_author_name_link( $comment ) {
    $return = '';
    $comment_author_url = '';
    if ( !empty( $comment->comment_author_url ) ) {
        $comment_author_url = $comment->comment_author_url;
    }
    if ( empty( $comment_author_url ) && $comment->user_id != 0 ) {
        $user_info = get_user_by( 'id', $comment->user_id );
        $comment_author_url = $user_info->user_url;
    }

    $return = $comment->comment_author;

    if ( !empty( $comment_author_url ) ) {
        $return = '<a href="' . $comment_author_url . '" target="_blank">' . $return . '</a>';
    }

    return '<strong class="comment_author_url">' . $return . '</strong>';
}


/**
 * Get Thumbnail URL
 *
 * @param string  $format
 * @return string
 */
function wputh_get_thumbnail_url( $format ) {
    global $post;
    $returnUrl = get_template_directory_uri().'/images/thumbnails/' . $format . '.jpg';
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $format );
    if ( isset( $image[0] ) ) {
        $returnUrl = $image[0];
    }
    return $returnUrl;
}


/**
 * Get attachments - images
 *
 * @param int     $postID
 * @param string  $format (optional)
 * @return array
 */
function wputh_get_attachments_images($postID = false, $format = 'medium', $settings = array()) {
    global $post;
    if ($postID === false) {
        if (isset($post->ID)) {
            $postID = $post->ID;
        }
        else {
            return array();
        }
    }

    $default_settings = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => - 1,
        'post_status' => 'any',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_parent' => $postID
    );

    $args = array_merge($default_settings,$settings);

    $images = array();
    $attachments = get_posts($args);
    foreach ($attachments as $attachment) {
        $image = wp_get_attachment_image_src($attachment->ID, $format);
        if (isset($image[0])) {
            $images[$attachment->ID] = $image;
        }
    }
    return $images;
}

/**
 * Get attachment detail
 *
 * via http://wordpress.org/ideas/topic/functions-to-get-an-attachments-caption-title-alt-description
 *
 * @param int     $postID
 * @return array
 */
function wputh_get_attachment( $attachment_id ) {
    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}


/**
 * Send a preformated mail
 *
 * @param string  $address
 * @param string  $subject
 * @param string  $content
 */
function wputh_sendmail( $address, $subject, $content, $more = array() ) {

    // Set "more" default values values
    if ( !is_array( $more ) ) {
        $more = array();
    }
    $ids = array( 'headers', 'attachments', 'vars' );
    foreach ( $ids as $id ) {
        if ( !isset( $more[$id] ) || !is_array( $more[$id] ) ) {
            $more[$id] = array();
        }
    }
    if ( !isset( $more['model'] ) ) {
        $more['model'] = '';
    }

    // Include headers
    $tpl_mail = get_template_directory() . '/tpl/mails/';
    $mail_content = '';
    if ( file_exists( $tpl_mail.'header.php' ) ) {
        ob_start();
        include $tpl_mail.'header.php';
        $mail_content .= ob_get_clean();
    }

    $model = $tpl_mail.'model-'.$more['model'].'.php';
    if ( !empty( $more['model'] ) && file_exists( $model ) ) {
        ob_start();
        include $model;
        $mail_content .= ob_get_clean();
    }
    else {
        $mail_content .= $content;
    }

    if ( file_exists( $tpl_mail.'footer.php' ) ) {
        ob_start();
        include $tpl_mail.'footer.php';
        $mail_content .= ob_get_clean();
    }

    add_filter( 'wp_mail_content_type', 'wputh_sendmail_set_html_content_type' );
    wp_mail( $address, '[' . get_bloginfo( 'name' ) . '] ' . $subject, $mail_content, $more['headers'], $more['attachments'] );
    // reset content-type to to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    remove_filter( 'wp_mail_content_type', 'wputh_sendmail_set_html_content_type' );
}

function wputh_sendmail_set_html_content_type() {
    return 'text/html';
}


/* ----------------------------------------------------------
  Pagination
---------------------------------------------------------- */

if (!function_exists('wputh_paginate')) {
    function wputh_paginate($prev_text='', $next_text='') {
        ob_start();
        include get_template_directory() . '/tpl/paginate.php';
        return ob_get_clean();
    }
}

/* ----------------------------------------------------------
  Get HTML page link
---------------------------------------------------------- */

if (!function_exists('wputh_link')) {
    function wputh_link($page_id) {
        return '<a class="' . (is_page($page_id) ? 'current' : '') . '" href="' . get_permalink($page_id) . '">' . get_the_title($page_id) . '</a>';
    }
}

/* ----------------------------------------------------------
  Truncate
---------------------------------------------------------- */

function wputh_truncate($string, $length, $more = '...') {
    $_new_string = '';
    $_maxlen = $length - strlen($more);
    $_words = explode(' ', $string);

    /* Add word to word */
    foreach ($_words as $_word) {
        if (strlen($_word) + strlen($_new_string) >= $_maxlen) {
            break;
        }

        /* Separate by spaces */
        if (!empty($_new_string)) {
            $_new_string.= ' ';
        }
        $_new_string.= $_word;
    }

    /* If new string is shorter than original */
    if (strlen($_new_string) < strlen($string)) {

        /* Add the after text */
        $_new_string.= $more;
    }

    return $_new_string;
}

/* ----------------------------------------------------------
  Share methods
---------------------------------------------------------- */

function wputh_get_share_methods($post, $title = false) {

    if (!is_object($post)) {
        if (!is_numeric($post)) {
            return array();
        }
        $post = get_post($post);
    }

    $_title = apply_filters('the_title', $post->post_title);
    if ($title !== false) {
        $_title = $title;
    }
    $_permalink = get_permalink($post);
    $_image = '';
    if (has_post_thumbnail($post->ID)) {
        if (function_exists('wputhumb_get_thumbnail_url')) {
            $_image = urlencode(wputhumb_get_thumbnail_url('thumbnail', $post->ID));
        }
        else {
            $_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
        }
    }

    $_methods = array(
        'email' => array(
            'name' => 'Email',
            'url' => str_replace('+', '%20', 'mailto:mail@mail.com?subject=' . urlencode($_title) . '&body=' . urlencode($_title) . '+' . urlencode($_permalink))
        ) ,
        'facebook' => array(
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($_permalink)
        ) ,
        'googleplus' => array(
            'name' => 'Google Plus',
            'url' => 'https://plus.google.com/share?url=' . urlencode($_permalink)
        ) ,
        'linkedin' => array(
            'name' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($_permalink) . '&title=' . urlencode($_title) . '&summary=&source='
        ) ,
        'pinterest' => array(
            'name' => 'Pinterest',
            'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($_permalink) . (!empty($_image) ? '&media=' . $_image : '') . '&description=' . urlencode($_title)
        ) ,
        'twitter' => array(
            'name' => 'Twitter',
            'url' => 'https://twitter.com/home?status=' . urlencode(wputh_truncate($_title, 100)) . '+' . urlencode($_permalink)
        ) ,
        'viadeo' => array(
            'name' => 'Viadeo',
            'url' => 'https://www.viadeo.com/shareit/share/?url' . urlencode($_permalink) . '&title=' . urlencode($_title) . ''
        ) ,
    );

    return apply_filters('wputheme_share_methods', $_methods, $_title, $_permalink, $_image);
}

/* ----------------------------------------------------------
  Social Links
---------------------------------------------------------- */

function wputh_get_social_links_ids() {
    return apply_filters('wputheme_social_links', array(
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
    ));
}
function wputh_get_social_links() {
    $wpu_social_links = wputh_get_social_links_ids();
    $links = array();
    foreach ($wpu_social_links as $id => $name) {
        $social_link = trim(get_option('social_' . $id . '_url'));
        if (!empty($social_link)) {
            $links[$id] = array(
                'name' => $name,
                'url' => $social_link
            );
        }
    }
    return $links;
}
