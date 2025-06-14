<?php

/* ----------------------------------------------------------
  [grid]test 1 -column-separator- test 2[/grid]
---------------------------------------------------------- */

function wputh_grid_shortcode($atts, $content = null) {
    $content = strip_tags($content, '<br><img><b><i><strong><em><small>');
    $content_cols = explode('-column-separator-', $content);
    $content = '';
    foreach ($content_cols as $col) {
        $content .= '<div class="col">' . trim($col) . '</div>';
    }
    return '<div class="post-content-grid">' . $content . '</div>';
}
add_shortcode('grid', 'wputh_grid_shortcode');

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
    return '<iframe width="' . $width . '" height="' . $height . '" src="//maps.google.com/maps?q=' . urlencode($content) . '&output=embed"></iframe>';
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
        'title' => ''
    ), $atts));

    $args = array(
        'before_widget' => '<div class="box widget">',
        'after_widget' => '</div>',
        'before_title' => '<div class="widget-title">',
        'after_title' => '</div>'
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

    return '<div class="wputh-video-container"><iframe src="//www.youtube.com/embed/' . $arr['v'] . '" height="315" width="560" allowfullscreen="" frameborder="0"></iframe></div>';
}

/* ----------------------------------------------------------
  [responsive_vimeo]https://www.vimeo.com/watch?v=wl2Rc4R0yLQ[/responsive_vimeo]
---------------------------------------------------------- */

add_shortcode('responsive_vimeo', 'wputh_vimeo_shortcode');
function wputh_vimeo_shortcode($atts, $content = "") {
    $url = trim($content);

    # Regex from http://blog.luutaa.com/php/extract-youtube-and-vimeo-video-id-from-link/
    $regexstr = '~
                # Match Vimeo link and embed code
                (?:<iframe [^>]*src=")?       # If iframe match up to first quote of src
                (?:                         # Group vimeo url
                    https?:\/\/             # Either http or https
                    (?:[\w]+\.)*            # Optional subdomains
                    vimeo\.com              # Match vimeo.com
                    (?:[\/\w]*\/videos?)?   # Optional video sub directory this handles groups links also
                    \/                      # Slash before Id
                    ([0-9]+)                # $1: VIDEO_ID is numeric
                    [^\s]*                  # Not a space
                )                           # End group
                "?                          # Match end quote if part of src
                (?:[^>]*></iframe>)?        # Match the end of the iframe
                (?:<p>.*</p>)?              # Match any title information stuff
                ~ix';

    preg_match($regexstr, $url, $url_details);

    // Check query
    if (!isset($url_details[1])) {
        return;
    }

    return '<div class="wputh-video-container"><iframe src="//player.vimeo.com/video/' . $url_details[1] . '?title=0&amp;byline=0&amp;portrait=0" width="1200" height="674" frameborder="0" allowfullscreen="allowfullscreen"></iframe></div>';
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
  Icon
---------------------------------------------------------- */

add_shortcode('wputh_icon', 'wputh_icon_shortcode');
function wputh_icon_shortcode($atts) {
    return '<i class="icon icon_' . esc_attr($atts['name']) . '" aria-hidden="true"></i>';
}
