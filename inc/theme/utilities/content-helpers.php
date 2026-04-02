<?php

/* ----------------------------------------------------------
  Lists & tables
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
 * Convert an array to an HTML list using a callback function
 * @param  array    $items     Array of items to be converted
 * @param  callable $callback  Callback function to process each item
 * @param  array    $args      Additional arguments to pass to the callback
 * @return string              HTML List
 */
function array_to_callback_list($items, $callback, $args = array()) {
    if (!$items || !is_array($items) || !is_callable($callback)) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $args = array_merge(array(
        'ul_classname' => '',
        'li_classname' => ''
    ), $args);

    $ul_classname = $args['ul_classname'] ? ' class="' . esc_attr($args['ul_classname']) . '"' : '';
    $li_classname = $args['li_classname'] ? ' class="' . esc_attr($args['li_classname']) . '"' : '';

    $output = '<ul' . $ul_classname . '>';
    foreach ($items as $item) {
        $output .= '<li' . $li_classname . '>' . call_user_func($callback, $item, $args) . '</li>';
    }
    $output .= '</ul>';
    return $output;
}

/* ----------------------------------------------------------
  Links
---------------------------------------------------------- */

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
  Time
---------------------------------------------------------- */

if (!function_exists('wputh_link')) {
    function wputh_link($page_id) {
        $wputh_link_classname = apply_filters('wputh_link_classname', (is_page($page_id) ? 'current' : ''));
        return '<a class="' . $wputh_link_classname . '" href="' . get_permalink($page_id) . '">' . get_the_title($page_id) . '</a>';
    }
}

/* ----------------------------------------------------------
  Display a time period
---------------------------------------------------------- */

function wputh_get_time_tag_html($raw_date, $date_format = '') {
    if (!$raw_date) {
        return '';
    }

    if (!$date_format) {
        $date_format = get_option('date_format');
    }
    $date = strtotime($raw_date);
    if (!$date) {
        return '';
    }
    return '<time datetime="' . date(DATE_W3C, $date) . '">' . date_i18n($date_format, $date) . '</time>';
}

/**
 * Get an HTML <time> tag for a post
 * @param  string  $date_format   Native PHP date format.
 * @param  boolean $post_id       (optional) Post ID
 * @return string                 <time> tag for the
 */
function wputh_get_time_tag($date_format = '', $post_id = false) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    return wputh_get_time_tag_html(get_the_date('c', $post_id), $date_format);
}

/* Same day */

/**
 * Display a time period
 * @param  string $start_date
 * @param  string $end_date
 * @param  array  $args
 * @return string
 */

function wputh_get_time_period_string($start_date, $end_date, $args = array()) {

    if (!$start_date) {
        return '';
    }

    if (!is_array($args)) {
        $args = array();
    }
    $args = array_merge(array(
        'ymd_format' => __('d F Y', 'wputh'),
        'md_format' => __('d F', 'wputh')
    ), $args);

    $from = strtotime($start_date);
    $to = strtotime($end_date);

    $from_str = '';
    $to_str = '';

    /* Same month year */
    if ($start_date == $end_date || !$end_date) {
        return '<div class="wputh-time-period">' . date_i18n($args['ymd_format'], $from) . '</div>';
    }

    /* Same year */
    else if (date('Ym', $from) == date('Ym', $to)) {
        $from_str = date_i18n(__('d', 'wputh'), $from);
        $to_str = date_i18n($args['ymd_format'], $to);
    } else if (date('Y', $from) == date('Y', $to)) {
        $from_str = date_i18n($args['md_format'], $from);
        $to_str = date_i18n($args['ymd_format'], $to);
    } else {
        $from_str = date_i18n($args['ymd_format'], $from);
        $to_str = date_i18n($args['ymd_format'], $to);
    }

    return '<div class="wputh-time-period">' . sprintf(
        __('From %s to %s', 'wputh'),
        $from_str,
        $to_str
    ) . '</div>';
}

/* ----------------------------------------------------------
  Tools
---------------------------------------------------------- */

/**
 * Truncate
 * @param  [type] $string [description]
 * @param  [type] $length [description]
 * @param  string $more   [description]
 * @return [type]         [description]
 */
function wputh_truncate($string, $length = 150, $more = '...', $args = array()) {

    if (!is_array($args)) {
        $args = array();
    }
    $args = array_merge(array(
        'strip_tags' => true
    ), $args);
    $_new_string = '';
    if ($args['strip_tags']) {
        $string = strip_tags($string);
    }
    $_maxlen = $length - strlen($more);
    $_words = explode(' ', $string);

    if (isset($_words[0]) && strlen($_words[0]) > $_maxlen) {
        $_new_string = substr($_words[0], 0, $_maxlen);
    }

    /* Separate by spaces */
    foreach ($_words as $_word) {
        if (strlen($_word) + strlen($_new_string) >= $_maxlen) {
            break;
        }

        /* If new string is shorter than original */
        if (!empty($_new_string)) {
            $_new_string .= ' ';
        }
        $_new_string .= $_word;
    }

    if (!$args['strip_tags']) {
        $_new_string = force_balance_tags($_new_string);
    }

    /* Add the after text */
    if (strlen($_new_string) < strlen($string)) {
        $_new_string .= $more;
    }

    return $_new_string;
}

/**
 * Check if a string starts with another
 * @param  string $haystack
 * @param  string $needle
 * @return bool
 */
function wputh_startsWith($haystack = '', $needle = '') {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

/**
 * Check if a string starts with another
 * @param  string $haystack
 * @param  string $needle
 * @return bool
 */
function wputh_endsWith($haystack = '', $needle = '') {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

/* ----------------------------------------------------------
  Pagination
---------------------------------------------------------- */

if (!function_exists('wputh_paginate')) {
    function wputh_paginate($prev_text = false, $next_text = false, $wputh_paginate_query = false) {
        ob_start();
        $tpl = locate_template(array('tpl/paginate.php'), false);
        if ($tpl && file_exists($tpl)) {
            include $tpl;
        }
        return ob_get_clean();
    }
}

/* ----------------------------------------------------------
  Term switcher
---------------------------------------------------------- */

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
        $filter_html .= '<li class="' . esc_attr($args['classname_item']) . '">';
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

        $filter_html .= '<li class="' . esc_attr($args['classname_item']) . '">';
        $filter_html .= '<a class="' . esc_attr($args['classname_link']) . ' ' . ($is_active ? esc_attr($args['classname_current']) : '') . '" href="' . get_term_link($term) . '"><span>' . esc_html($term->name) . '</span></a>';
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
