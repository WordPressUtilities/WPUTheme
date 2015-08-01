<?php
include dirname(__FILE__) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Main
---------------------------------------------------------- */

if (!IS_AJAX) {
    add_action('get_header', 'wputh_get_doctype_html', 1, 1);
}
if (!function_exists('wputh_get_doctype_html')) {
    function wputh_get_doctype_html() {
?>
<!DOCTYPE HTML>
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html <?php language_attributes(); ?> class="is_ie10"><![endif]-->
<!--[if !IE]><!--> <html <?php language_attributes(); ?>><!--<![endif]-->
<?php
    }
}

/* ----------------------------------------------------------
  HEAD
---------------------------------------------------------- */

/* Charset
 -------------------------- */

add_action('wp_head', 'wputh_head_add_charset', 0);
if (!function_exists('wputh_head_add_charset')) {
    function wputh_head_add_charset() {
        echo '<meta charset="' . get_bloginfo('charset') . '" />';
    }
}

/* Google Fonts
 -------------------------- */

add_action('wp_enqueue_scripts', 'wputh_head_enqueue_google_fonts');
function wputh_head_enqueue_google_fonts() {
    $query_args = apply_filters('wputh_google_fonts', array());
    if (!empty($query_args)) {
        wp_register_style('google-fonts', add_query_arg($query_args, "//fonts.googleapis.com/css") , array() , null);
        wp_enqueue_style('google-fonts');
    }
}

/* Page title
 -------------------------- */

if (!function_exists('_wp_render_title_tag')) {
    add_action('wp_head', 'wputh_head_add_title', 1);
    if (!function_exists('wputh_head_add_title')) {
        function wputh_head_add_title() {
            echo '<title>';
            wp_title();
            echo '</title>';
        }
    }
}

/* Viewport
 -------------------------- */

add_action('wp_head', 'wputh_head_add_viewport', 10);
if (!function_exists('wputh_head_add_viewport')) {
    function wputh_head_add_viewport() {
        echo '<meta name="viewport" content="width=device-width" />';
    }
}

/* Favicon
 -------------------------- */

add_action('wp_head', 'wputh_head_add_favicon', 10);
if (!function_exists('wputh_head_add_favicon')) {
    function wputh_head_add_favicon() {
        echo '<link rel="shortcut icon" href="' . get_template_directory_uri() . '/images/favicon.ico" />';
    }
}

/* IE Compatibility
 -------------------------- */

add_action('wp_head', 'wputh_head_add_iecompatibility', 10);
if (!function_exists('wputh_head_add_iecompatibility')) {
    function wputh_head_add_iecompatibility() {
        $script_src = get_template_directory_uri() . '/js/ie/';
        echo '<!--[if lt IE 9]><script type="text/javascript" src="' . $script_src . 'ie.js"></script><![endif]-->';
    }
}

/* ----------------------------------------------------------
  HEADER
---------------------------------------------------------- */

/* Title tag
 -------------------------- */

add_action('wputheme_header_banner', 'wputh_display_title');
if (!function_exists('wputh_display_title')) {
    function wputh_display_title() {
        $main_tag = is_home() ? 'h1' : 'div';
        $main_tag_classname = 'h1 main-title';
        $title_content = get_bloginfo('name');
        if (has_header_image()) {
            $title_content = '<img src="' . get_header_image() . '" alt="' . esc_attr($title_content) . '" />';
            $main_tag_classname.= ' main-logo';
        }
        echo '<' . $main_tag . ' class="' . $main_tag_classname . '"><a href="' . home_url() . '">' . $title_content . '</a></' . $main_tag . '>';
    }
}

/* Search form
 -------------------------- */

add_action('wputheme_header_banner', 'wputh_display_searchform');
if (!function_exists('wputh_display_searchform')) {
    function wputh_display_searchform() {
        include get_template_directory() . '/tpl/header/searchform.php';
    }
}

/* Social
 -------------------------- */

add_action('wputheme_header_banner', 'wputh_display_social');
if (!function_exists('wputh_display_social')) {
    function wputh_display_social() {
        include get_template_directory() . '/tpl/header/social.php';
    }
}

/* Main menu
 -------------------------- */

add_action('wputheme_header_banner', 'wputh_display_social');
if (!function_exists('wputh_display_social')) {
    function wputh_display_social() {
        include get_template_directory() . '/tpl/header/social.php';
    }
}

/* ----------------------------------------------------------
  HOME
---------------------------------------------------------- */

add_action('wputheme_home_content', 'wputheme_home_content__default');
if (!function_exists('wputheme_home_content__default')) {
    function wputheme_home_content__default() {
        include get_template_directory() . '/tpl/home/default.php';
    }
}

