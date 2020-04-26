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
    return $image ? $image : $returnUrl;
}

/**
 * Get Thumbnail URL with specific parameters
 *
 * @param  int $id                       Post ID
 * @param  mixed (string|array) $format  Thumb format
 * @param  boolean $crop                 Crop to format
 * @return string                        Image URL
 */
function wputh_get_thumb_url($id = false, $format = 'thumbnail', $crop = false) {
    if (!$id) {
        $id = get_the_ID();
    }
    $format_txt = $format;
    if (is_array($format)) {
        $format_txt = implode('x', $format);
    }
    $returnUrl = get_stylesheet_directory_uri() . '/assets/images/thumbnails/' . $format_txt . '.jpg';
    $thumb_id = get_post_thumbnail_id($id);
    if (!$thumb_id) {
        return $returnUrl;
    }
    $image = wputh_get_attachment_image_src($thumb_id, $format, $crop);
    return $image ? $image : $returnUrl;
}

/**
 * Get an attachment URL and dynamically create intermediate sizes if needed
 * @param  int $id        ID of the attachment
 * @param  mixed $format  ID of an image format, or array of dimensions.
 * @return mixed          URL if success, false if not found
 */
function wputh_get_attachment_image_src($id, $format = 'thumbnail', $crop = false, $image_quality = false) {
    $cache_duration = 60 * 60;
    $cache_id = 'wputhattimgsrc_' . $id;
    $cache_id .= '_' . (is_array($format) ? md5(json_encode($format)) : $format);
    $cache_id .= '_' . ($crop ? 1 : 0);
    $cache_id .= '_' . ($image_quality ? $image_quality : 0);

    // GET CACHED VALUE
    $result = wp_cache_get($cache_id);
    if ($result !== false) {
        return $result;
    }
    $image = wp_get_attachment_image_src($id, $format);

    /* If format is an array of sizes : generate an intermediate size */
    if (is_array($format) && isset($image[0])) {
        $upload_dir = wp_get_upload_dir();
        $base_image_path = wp_get_attachment_image_src($id, 'thumbnail');

        /* Get thumbnail path */
        $thumbnail_dimensions = $base_image_path[1] . 'x' . $base_image_path[2];
        $new_dimensions = $format[0] . 'x' . $format[1];
        $source_image = $base_image_path[0];
        $new_image = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $source_image);
        $new_image = str_replace($thumbnail_dimensions, $new_dimensions, $new_image);
        $new_image_source = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_image);

        /* If file exists : return URL */
        if ($new_image_source != $source_image && file_exists($new_image)) {
            wp_cache_set($cache_id, $new_image_source, '', $cache_duration);
            return $new_image_source;
        }

        /* Resize image and return */
        $image = wp_get_image_editor(get_attached_file($id));
        $new_img_path = false;
        if (!is_wp_error($image)) {
            if (isset($image_quality)) {
                $image->set_quality($image_quality);
            }
            $image->resize($format[0], $format[1], $crop);
            $new_img_path = $image->generate_filename();
            $image->save($new_img_path);
        }
        if (file_exists($new_img_path)) {
            $image_src = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_img_path);
            wp_cache_set($cache_id, $image_src, '', $cache_duration);
            return $image_src;
        }
    }

    if (!is_wp_error($image) && is_array($image) && isset($image[0])) {
        wp_cache_set($cache_id, $image[0], '', $cache_duration);
        return $image[0];
    }

    wp_cache_set($cache_id, '', '', $cache_duration);

    return false;
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
        'whatsapp' => array(
            'name' => 'Whatsapp',
            'url' => 'whatsapp://send?text=' . urlencode($_permalink)
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
  Remove AJAX parameter from URL
---------------------------------------------------------- */

function wputh_clean_ajax_parameter_from_url($url = '') {
    $ajax_param = 'ajax=1';
    /* First parameter of multiple parameters */
    $url = str_replace('?' . $ajax_param . '&', '?', $url);
    /* One parameter of multiple parameters */
    $url = str_replace('&' . $ajax_param, '', $url);
    /* Only parameter */
    $url = str_replace('?' . $ajax_param, '', $url);
    return $url;
}

/* Clean from pagenum */
add_filter('get_pagenum_link', 'wputh_clean_get_pagenum_link', 10, 1);
function wputh_clean_get_pagenum_link($url) {
    return wputh_clean_ajax_parameter_from_url($url);
}

/* ----------------------------------------------------------
  Get translated URL
---------------------------------------------------------- */

/**
 * Translate current URL with Qtranslate-slug
 * @param  text $lang   textual id for language (ex:fr)
 * @return mixed        translated URL or FALSE if error.
 */
function wputh_qtranslate_slug_get_current_url($lang) {
    global $qtranslate_slug;
    if (!isset($qtranslate_slug) || !is_object($qtranslate_slug)) {
        return false;
    }
    $url = $qtranslate_slug->get_current_url($lang);

    /* Base prefix for URL : http://example.com/fr/ */
    $base_url = get_site_url() . '/';
    $base_lang_url = $base_url . $lang . '/';

    /* If url does not start with base lang url */
    $url_root = substr($url, 0, strlen($base_lang_url));
    if (strlen($url) > strlen($url_root) && $url_root != $base_lang_url) {
        $url = str_replace(get_site_url() . '/', $base_lang_url, $url);
    }
    return wputh_clean_ajax_parameter_from_url($url);
}

function wputh_translated_url($use_full_lang_name = false) {
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
        return $display_languages;
    }

    // Obtaining from Qtranslate X
    if (function_exists('qtranxf_getLanguage') && function_exists('qtranxf_getSortedLanguages') && function_exists('qtranxf_convertURL')) {
        global $q_config;
        $current_lang = qtranxf_getLanguage();
        $languages = qtranxf_getSortedLanguages();

        foreach ($languages as $lang) {
            if (class_exists('QtranslateSlug')) {
                /* Qtranslate slug needs a fix to force URL lang */
                $url = wputh_qtranslate_slug_get_current_url($lang);
            } else {
                $url = qtranxf_convertURL($current_url, $lang, 0, 1);
            }
            $full_name = $lang;
            if ($use_full_lang_name && isset($q_config['language_name'][$lang])) {
                $full_name = $q_config['language_name'][$lang];
            }
            $display_languages[$lang] = array(
                'name' => $full_name,
                'current' => $lang == $current_lang,
                'url' => $url
            );
        }
        return $display_languages;
    }

    // Obtaining from Polylang
    if (function_exists('pll_current_language')) {
        global $polylang;
        $current_lang = pll_current_language();
        $poly_langs = pll_the_languages(array(
            'raw' => 1,
            'echo' => 0
        ));

        if (is_array($poly_langs)) {
            foreach ($poly_langs as $lang) {
                $full_name = $lang['slug'];
                if ($use_full_lang_name && isset($lang['name'])) {
                    $full_name = $lang['name'];
                }
                $display_languages[$lang['slug']] = array(
                    'name' => $full_name,
                    'current' => $lang['slug'] == $current_lang,
                    'url' => $lang['url']
                );
            }
        }
    }
    return $display_languages;
}

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
    foreach ($items as $item) {
        if ($item->menu_item_parent && $args['depth'] == 1) {
            continue;
        }
        $menu_items[] = '<a target="' . $item->target . '" href="' . $item->url . '"><span>' . $item->title . '</span></a>';
    }
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

/* ----------------------------------------------------------
  Check if internal link
---------------------------------------------------------- */

function wpu_is_internal_link($external_url) {
    $url_host = parse_url($external_url, PHP_URL_HOST);
    $base_url_host = parse_url(get_site_url(), PHP_URL_HOST);
    return ($url_host == $base_url_host || empty($url_host));
}

/* ----------------------------------------------------------
  Gallery shortcode : add thickbox
---------------------------------------------------------- */

add_action('wp_head', 'wputh_gallery_filter_the_content', 10);
function wputh_gallery_filter_the_content() {
    if (!apply_filters('wputh_gallery_filter_the_content', false)) {
        return;
    }
    add_thickbox();
    echo <<<EOT
<script>
function setup_wputh_gallery_filter() {
    jQuery(".gallery-item").find("a[href$='jpg'], a[href$='png'], a[href$='jpeg'], a[href$='gif']").each(function(){
        jQuery(this).attr("rel","gallery");
    });
}
function wputh_gallery_filter() {
    tb_init(".gallery-item a[rel='gallery']");
}
jQuery(document).ready(function(){
    setup_wputh_gallery_filter();
    wputh_gallery_filter();
});
jQuery(window).on('vanilla-pjax-ready', function(e){
    setup_wputh_gallery_filter();
})
</script>
EOT;
}

/* ----------------------------------------------------------
  Tools
---------------------------------------------------------- */

function wputh_startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function wputh_endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

/* ----------------------------------------------------------
  Time
---------------------------------------------------------- */

/**
 * Get an HTML <time> tag for a post
 * @param  string  $date_format   Native PHP date format.
 * @param  boolean $post_id       (optional) Post ID
 * @return string                 <time> tag for the
 */
function wputh_get_time_tag($date_format = 'd/m/Y', $post_id = false) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return '<time datetime="' . get_the_time(DATE_W3C, $post_id) . '">' . get_the_time($date_format, $post_id) . '</time>';
}
