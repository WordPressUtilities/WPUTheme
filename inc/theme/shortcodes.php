<?php

/* ----------------------------------------------------------
  [columns]Text on multiple columns[/columns]
---------------------------------------------------------- */

function wputh_columns_shortcode($atts, $content = null) {
    return '<div class="post-content-columns">' . $content . '</div>';
}
add_shortcode('columns', 'wputh_columns_shortcode');

/* ----------------------------------------------------------
  [googlemap]8 Rue de Londres, 75009 Paris, France[/googlemap]
---------------------------------------------------------- */

function wputh_googlemap_shortcode($atts, $content = null) {
    $width = isset($atts['width']) ? $atts['width'] : 640;
    $height = isset($atts['height']) ? $atts['height'] : 480;
    return '<iframe width="' . $width . '" height="' . $height . '" src="http://maps.google.com/maps?q=' . urlencode($content) . '&output=embed"></iframe>';
}
add_shortcode("googlemap", "wputh_googlemap_shortcode");

/* ----------------------------------------------------------
  [widget type="WP_Widget_Recent_Posts"]
---------------------------------------------------------- */

/* Thx http://wp.smashingmagazine.com/2012/12/11/inserting-widgets-with-shortcodes/ */

add_shortcode('widget', 'wputh_widget_shortcode');
function wputh_widget_shortcode($atts) {

    // Configure defaults and extract the attributes into variables
    extract(shortcode_atts(array(
        'type' => '',
        'title' => '',
    ) , $atts));

    $args = array(
        'before_widget' => '<div class="box widget">',
        'after_widget' => '</div>',
        'before_title' => '<div class="widget-title">',
        'after_title' => '</div>',
    );

    ob_start();
    the_widget($type, $atts, $args);
    return ob_get_clean();
}

/* ----------------------------------------------------------
  [get_site_option key="option_name"] Get option
---------------------------------------------------------- */

add_shortcode('get_site_option', 'wputh_get_site_option');
function wputh_get_site_option($atts) {
    return get_option($atts['key']);
}

/* ----------------------------------------------------------
  [responsive_youtube]https://www.youtube.com/watch?v=wl2Rc4R0yLQ[/responsive_youtube]
---------------------------------------------------------- */

add_shortcode('responsive_youtube', 'wputh_youtube_shortcode');
function wputh_youtube_shortcode($atts, $content = "") {
    $url = trim($content);

    // Check URL
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
        return;
    }

    // Extract URL params
    $url_details = parse_url($url);

    // Check query
    if (!isset($url_details['query'])) {
        return;
    }

    // Extract query params
    parse_str($url_details['query'], $arr);

    // Check "v" param
    if (!isset($arr['v'])) {
        return;
    }

    return '<div class="wputh-video-container"><iframe src="http://www.youtube.com/embed/' . $arr['v'] . '" height="315" width="560" allowfullscreen="" frameborder="0"></iframe></div>';
}
