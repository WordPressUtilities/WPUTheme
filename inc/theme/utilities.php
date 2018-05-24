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
function get_the_loop($params = array()) {
    global $post, $wp_query, $wpdb;

    /* Get params */
    $default_params = array(
        'loop' => 'loop-small'
    );

    if (!is_array($params)) {
        $params = array($params);
    }

    $parameters = array_merge($default_params, $params);

    /* Start the loop */
    ob_start();
    if (have_posts()) {
        echo '<div class="list-loops">';
        while (have_posts()) {
            the_post();
            get_template_part($parameters['loop']);
        }
        echo '</div>';
        echo wputh_paginate();
    } else {
        echo '<p>' . __('Sorry, no search results for this query.', 'wputh') . '</p>';
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
function wputh_get_comments_title($count_comments, $zero = false, $one = false, $more = false, $closed = false) {
    global $post;
    $return = '';
    if (is_array($count_comments)) {
        $count_comments = count($count_comments);
    }
    if (!is_numeric($count_comments)) {
        $count_comments = $post->comment_count;
    }
    if ($zero === false) {
        $zero = __('<strong>no</strong> comments', 'wputh');
    }
    if ($one === false) {
        $one = __('<strong>1</strong> comment', 'wputh');
    }
    if ($more === false) {
        $more = __('<strong>%s</strong> comments', 'wputh');
    }
    if ($closed === false) {
        $closed = __('Comments are closed', 'wputh');
    }
    if (!comments_open()) {
        $return = $closed;
    } else {
        switch ($count_comments) {
        case 0:
            $return = $zero;
            break;
        case 1:
            $return = $one;
            break;
        default:
            $return = sprintf($more, $count_comments);
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
function wputh_get_comment_author_name_link($comment) {
    $return = '';
    $comment_author_url = '';
    if (!empty($comment->comment_author_url)) {
        $comment_author_url = $comment->comment_author_url;
    }
    if (empty($comment_author_url) && $comment->user_id != 0) {
        $user_info = get_user_by('id', $comment->user_id);
        $comment_author_url = $user_info->user_url;
    }

    $return = $comment->comment_author;

    if (!empty($comment_author_url)) {
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
function wputh_get_thumbnail_url($format) {
    global $post;
    $returnUrl = get_template_directory_uri() . '/images/thumbnails/' . $format . '.jpg';
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $format);
    if (isset($image[0])) {
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
        } else {
            return array();
        }
    }

    $default_settings = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_parent' => $postID
    );

    $args = array_merge($default_settings, $settings);

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
function wputh_get_attachment($attachment_id) {
    $attachment = get_post($attachment_id);
    return array(
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink($attachment->ID),
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
function wputh_sendmail($address, $subject, $content, $more = array()) {

    // Set "more" default values values
    if (!is_array($more)) {
        $more = array();
    }
    $ids = array('headers', 'attachments', 'vars');
    foreach ($ids as $id) {
        if (!isset($more[$id]) || !is_array($more[$id])) {
            $more[$id] = array();
        }
    }
    if (!isset($more['model'])) {
        $more['model'] = '';
    }

    // Include headers
    $tpl_mail = get_template_directory() . '/tpl/mails/';
    $mail_content = '';
    if (file_exists($tpl_mail . 'header.php')) {
        ob_start();
        include $tpl_mail . 'header.php';
        $mail_content .= ob_get_clean();
    }

    $model = $tpl_mail . 'model-' . $more['model'] . '.php';
    if (!empty($more['model']) && file_exists($model)) {
        ob_start();
        include $model;
        $mail_content .= ob_get_clean();
    } else {
        $mail_content .= $content;
    }

    if (file_exists($tpl_mail . 'footer.php')) {
        ob_start();
        include $tpl_mail . 'footer.php';
        $mail_content .= ob_get_clean();
    }

    add_filter('wp_mail_content_type', 'wputh_sendmail_set_html_content_type');
    wp_mail($address, '[' . get_bloginfo('name') . '] ' . $subject, $mail_content, $more['headers'], $more['attachments']);
    // reset content-type to to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    remove_filter('wp_mail_content_type', 'wputh_sendmail_set_html_content_type');
}

function wputh_sendmail_set_html_content_type() {
    return 'text/html';
}

/* ----------------------------------------------------------
  Pagination
---------------------------------------------------------- */

if (!function_exists('wputh_paginate')) {
    function wputh_paginate($prev_text = false, $next_text = false) {
        ob_start();
        locate_template(array('tpl/paginate.php'), 1);
        return ob_get_clean();
    }
}

/* ----------------------------------------------------------
  Get HTML page link
---------------------------------------------------------- */

if (!function_exists('wputh_link')) {
    function wputh_link($page_id) {
        $wputh_link_classname = apply_filters('wputh_link_classname', (is_page($page_id) ? 'current' : ''));
        return '<a class="' . $wputh_link_classname . '" href="' . get_permalink($page_id) . '">' . get_the_title($page_id) . '</a>';
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
            $_new_string .= ' ';
        }
        $_new_string .= $_word;
    }

    /* If new string is shorter than original */
    if (strlen($_new_string) < strlen($string)) {

        /* Add the after text */
        $_new_string .= $more;
    }

    return $_new_string;
}

/* ----------------------------------------------------------
  Share methods
---------------------------------------------------------- */

function wputh_get_share_methods($post, $title = false, $permalink = false, $image = false) {

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
    $_title = trim(strip_tags(html_entity_decode($_title)));
    $_permalink = get_permalink($post);
    if ($permalink !== false) {
        $_permalink = $permalink;
    }
    $_image = '';
    if (has_post_thumbnail($post->ID)) {
        if (function_exists('wputhumb_get_thumbnail_url')) {
            $_image = urlencode(wputhumb_get_thumbnail_url('thumbnail', $post->ID));
        } else {
            $_image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
        }
    }
    if ($image !== false) {
        $_image = $image;
    }

    $_via_user = get_option('social_twitter_username');
    $_via = !empty($_via_user) ? ' via @' . $_via_user : '';
    $_twitter_text = wputh_truncate($_title, 100 - strlen($_via));

    $_methods = array(
        'email' => array(
            'name' => 'Email',
            'url' => str_replace('+', '%20', 'mailto:?subject=' . urlencode($_title) . '&body=' . urlencode($_title) . '+' . urlencode($_permalink))
        ),
        'facebook' => array(
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($_permalink)
        ),
        'googleplus' => array(
            'name' => 'Google Plus',
            'url' => 'https://plus.google.com/share?url=' . urlencode($_permalink)
        ),
        'linkedin' => array(
            'name' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode($_permalink) . '&title=' . urlencode($_title) . '&summary=&source='
        ),
        'pinterest' => array(
            'name' => 'Pinterest',
            'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($_permalink) . (!empty($_image) ? '&media=' . $_image : '') . '&description=' . urlencode($_title)
        ),
        'twitter' => array(
            'name' => 'Twitter',
            'url' => 'https://twitter.com/intent/tweet?text=' . urlencode($_twitter_text) . '+' . urlencode($_permalink) . urlencode($_via),
            'datas' => array(
                'via' => $_via_user
            )
        ),
        'viadeo' => array(
            'name' => 'Viadeo',
            'url' => 'https://www.viadeo.com/shareit/share/?url' . urlencode($_permalink) . '&title=' . urlencode($_title) . ''
        )
    );

    foreach ($_methods as $_id => $_method) {
        if (!isset($_methods[$_id]['datas'])) {
            $_methods[$_id]['datas'] = array();
        }
        $_methods[$_id]['datas']['permalink'] = $_permalink;
        $_methods[$_id]['datas']['title'] = $_title;
        $_methods[$_id]['datas']['image'] = $_image;
    }

    return apply_filters('wputheme_share_methods', $_methods, $_title, $_permalink, $_image, $_via_user);
}

/* ----------------------------------------------------------
  Social Links
---------------------------------------------------------- */

function wputh_get_social_links_ids() {
    return apply_filters('wputheme_social_links', array(
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram'
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

function wputh_get_social_links_html($wrapper_classname = 'header__social', $display_type = false, $template = '') {
    $wpu_social_links = wputh_get_social_links();
    $html = '<ul class="' . $wrapper_classname . '">';
    foreach ($wpu_social_links as $id => $link) {
        $html .= '<li><a rel="me" href="' . $link['url'] . '" class="' . $id . '" title="' . sprintf(__('%s: Follow %s (open in new window)', 'wputh'), $link['name'], get_bloginfo('name')) . '" target="_blank">';
        switch ($display_type) {
        case 'custom':
            $html .= str_replace('%s', $id, $template);
            break;
            break;
        case 'icon':
            $html .= '<i class="icon icon_' . $id . '"></i>';
            break;
        default:
            $html .= $link['name'];
        }
        $html .= '</a></li>';
    }
    $html .= '</ul>';
    return $html;
}

/* ----------------------------------------------------------
  Content helpers
---------------------------------------------------------- */

/**
 * Convert an array to an HTML table
 * @param  array  $array    Two dimensional array to be displayed
 * @param  array  $columns  Column names.
 * @return string           HTML Table
 */
function array_to_html_table($array = array(), $columns = array()) {
    $thead = '';
    $tfoot = '';
    $tbody = '';

    if (!is_array($array)) {
        return '';
    }

    if (is_array($columns) && !empty($columns)) {
        if (isset($columns['thead'], $columns['tfoot'])) {
            if (!empty($columns['thead'])) {
                $thead = '<thead><tr><th>' . implode('</th><th>', $columns['thead']) . '</th></tr></thead>';
            }
            if (!empty($columns['tfoot'])) {
                $tfoot = '<tfoot><tr><th>' . implode('</th><th>', $columns['tfoot']) . '</th></tr></tfoot>';
            }
        } else {
            $tr = '<tr><th>' . implode('</th><th>', $columns) . '</th></tr>';
            $thead = '<thead>' . $tr . '</thead>';
            $tfoot = '<tfoot>' . $tr . '</tfoot>';
        }
    }

    $html = '<table data-sortable>' . $thead . '<tbody>';
    foreach ($array as $line) {
        if (!is_array($line) || empty($line)) {
            continue;
        }
        $html .= '<tr>';
        foreach ($line as $id => $cell) {

            $content = $cell;
            $data = '';
            if (is_array($cell)) {
                $content = isset($cell['content']) ? $cell['content'] : implode('', $cell);
                $data = isset($cell['value']) ? 'data-value="' . $cell['value'] . '"' : '';
            }

            $html .= '<td class="col-' . $id . '" ' . $data . '>' . $content . '</td>';
        }
        $html .= '</tr>';

    }
    $html .= '</tbody>' . $tfoot . '</table>';
    return $html;
}

/**
 * Convert an array to an HTML List
 * @param  array  $array  Array to be displayed
 * @return string         HTML List
 */
function array_to_html_list($array = array()) {
    return '<ul>' . implode('</li><li>', $array) . '</ul>';
}

/**
 * Convert an URL to a HTML link
 * @param  string $url
 * @param  array  $options
 * @return string
 */
function wputh_url_to_link($url, $options = array()) {
    $link_text = $url;
    if (!is_array($options)) {
        $options = array();
    }

    /* Empty or invalid URL */
    if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
        return '';
    }

    /* Link text is by default the original link */
    $url_parts = parse_url($url);
    if (isset($url_parts['host'], $options['display_only_domain']) && $options['display_only_domain']) {
        $link_text = $url_parts['host'];
    }

    /* Target blank */
    $target = '';
    if (isset($options['target_blank']) && $options['target_blank']) {
        $target = ' target="_blank"';
    }

    return '<a' . $target . ' href="' . $url . '">' . esc_html($link_text) . '</a>';

}

/* ----------------------------------------------------------
  Fix locale
---------------------------------------------------------- */
/* Thx https://openclassrooms.com/forum/sujet/avoir-la-date-en-francais-grace-a-un-datetime-29453#message-5364823 */
if (class_exists('DateTime')) {
    class DateTimeFrench extends DateTime {
        public function format($format) {
            $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
            $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
            $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
            return str_replace($english_months, $french_months, str_replace($english_days, $french_days, parent::format($format)));
        }
    }
}

/* ----------------------------------------------------------
  Get current URL
---------------------------------------------------------- */

/* Thanks to http://kovshenin.com/2012/current-url-in-wordpress/ */
function wputh_get_current_url() {
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));
    if (is_singular() || is_single() || is_page()) {
        $current_url = get_permalink();
    }
    return $current_url;
}

/* ----------------------------------------------------------
  Get translated URL
---------------------------------------------------------- */

function wputh_translated_url() {
    $display_languages = array();
    $current_lang = '';
    $current_url = wputh_get_current_url();

    // Obtaining from Qtranslate
    if (function_exists('qtrans_getSortedLanguages') && function_exists('qtrans_getLanguage') && function_exists('qtrans_convertURL')) {
        $current_lang = qtrans_getLanguage();
        $languages = qtrans_getSortedLanguages();
        foreach ($languages as $lang) {
            $display_languages[$lang] = array(
                'name' => $lang,
                'current' => $lang == $current_lang,
                'url' => qtrans_convertURL($current_url, $lang, 0, 1)
            );
        }
    }

    // Obtaining from Qtranslate X
    if (function_exists('qtranxf_getSortedLanguages')) {
        $current_lang = qtranxf_getLanguage();
        $languages = qtranxf_getSortedLanguages();
        foreach ($languages as $lang) {
            $display_languages[$lang] = array(
                'name' => $lang,
                'current' => $lang == $current_lang,
                'url' => qtranxf_convertURL($current_url, $lang, 0, 1)
            );
        }
    }

    // Obtaining from Polylang
    if (function_exists('pll_current_language')) {
        global $polylang;
        $current_lang = pll_current_language();
        $poly_langs = pll_the_languages(array(
            'raw' => 1,
            'echo' => 0
        ));

        foreach ($poly_langs as $lang) {
            $display_languages[$lang['slug']] = array(
                'name' => $lang['slug'],
                'current' => $lang['slug'] == $current_lang,
                'url' => $lang['url']
            );
        }
    }
    return $display_languages;
}

/* ----------------------------------------------------------
  Cached nav menu
---------------------------------------------------------- */

function wputh_cached_nav_menu($args = array()) {
    $cache_duration = 7 * 24 * 60 * 60;
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

/* ----------------------------------------------------------
  Get all user values for a meta
---------------------------------------------------------- */

function wputh_get_all_users_values_for($meta_key = '') {
    global $wpdb;
    $_users = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s", $meta_key));
    $users = array();
    foreach ($_users as $_user) {
        $users[$_user->user_id] = $_user->meta_value;
    }
    return $users;
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
