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
        ob_start();
        language_attributes();
        $lang = ob_get_clean();

        echo '<!DOCTYPE HTML>';
        if (WPUTH_IECOMPATIBILITY) {
            echo '<!--[if IE 8 ]><html ' . $lang . ' class="is_ie8 lt_ie9 lt_ie10"><![endif]-->';
            echo '<!--[if IE 9 ]><html ' . $lang . ' class="is_ie9 lt_ie10"><![endif]-->';
            echo '<!--[if gt IE 9]><html ' . $lang . ' class="is_ie10"><![endif]-->';
            echo '<!--[if !IE]><!--> <html ' . $lang . '><!--<![endif]-->';
        } else {
            echo '<html ' . $lang . '>';
        }
    }
}

/* ----------------------------------------------------------
  HEAD
---------------------------------------------------------- */

/* Comments
 -------------------------- */

add_action('wp_head', 'wputh_head_set_comment_reply');
if (!function_exists('wputh_head_set_comment_reply')) {
    function wputh_head_set_comment_reply() {
        if (WPUTH_HAS_COMMENTS && is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}

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
        wp_register_style('google-fonts', add_query_arg($query_args, "//fonts.googleapis.com/css"), array(), null);
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
        if (!function_exists('has_site_icon') || !has_site_icon()) {
            echo '<link rel="shortcut icon" href="' . get_template_directory_uri() . '/images/favicon.ico" />';
        }
    }
}

/* IE Compatibility
 -------------------------- */

if (WPUTH_IECOMPATIBILITY):
    add_action('wp_head', 'wputh_head_add_iecompatibility', 10);
endif;
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
if (apply_filters('wputheme_display_title', true)):
    add_action('wputheme_header_banner', 'wputh_display_title');
endif;
add_action('wputheme_header_banner', 'wputh_display_title');
if (!function_exists('wputh_display_title')) {
    function wputh_display_title() {
        $main_tag = (is_home() || is_front_page()) ? 'h1' : 'div';
        $main_tag_classname = 'h1 main-title';
        $title_content = get_bloginfo('name');
        if (has_header_image()) {
            $title_content = '<img src="' . get_header_image() . '" alt="' . esc_attr($title_content) . '" />';
            $main_tag_classname .= ' main-logo';
        }
        echo '<' . $main_tag . ' class="' . $main_tag_classname . '"><a href="' . home_url() . '">' . $title_content . '</a></' . $main_tag . '>';
    }
}

/* Search form
 -------------------------- */

if (apply_filters('wputheme_display_searchform', true)):
    add_action('wputheme_header_banner', 'wputh_display_searchform');
endif;
if (!function_exists('wputh_display_searchform')) {
    function wputh_display_searchform() {
        include get_template_directory() . '/tpl/header/searchform.php';
    }
}

/* Social
 -------------------------- */

if (apply_filters('wputheme_display_social', true)):
    add_action('wputheme_header_banner', 'wputh_display_social');
endif;
if (!function_exists('wputh_display_social')) {
    function wputh_display_social() {
        echo wputh_get_social_links_html();
    }
}

/* Main menu
 -------------------------- */

if (apply_filters('wputheme_display_mainmenu', true)):
    add_action('wputheme_header_banner', 'wputh_display_mainmenu');
endif;
if (!function_exists('wputh_display_mainmenu')) {
    function wputh_display_mainmenu() {
        $main_menu_settings = apply_filters('wputheme_mainmenu_settings', array(
            'depth' => 1,
            'theme_location' => 'main',
            'menu_class' => 'main-menu'
        ));
        wp_nav_menu($main_menu_settings);
    }
}

/* ----------------------------------------------------------
  MAIN CONTENT
---------------------------------------------------------- */

if (apply_filters('wputheme_display_languages', true)):
    add_action('wputheme_main_overcontent', 'wputh_maincontent_languages');
endif;
if (!function_exists('wputh_maincontent_languages')) {
    function wputh_maincontent_languages() {
        include get_template_directory() . '/tpl/header/languages.php';
    }
}

if (apply_filters('wputheme_display_breadcrumbs', true)):
    add_action('wputheme_main_overcontent', 'wputh_maincontent_breadcrumbs');
endif;
if (!function_exists('wputh_maincontent_breadcrumbs')) {
    function wputh_maincontent_breadcrumbs() {
        include get_template_directory() . '/tpl/header/breadcrumbs.php';
    }
}

if (apply_filters('wputheme_display_jsvalues', true)):
    add_action('wputheme_main_overcontent_inajax', 'wputh_maincontent_jsvalues');
endif;
if (!function_exists('wputh_maincontent_jsvalues')) {
    function wputh_maincontent_jsvalues() {
        include get_template_directory() . '/tpl/header/jsvalues.php';
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

/* ----------------------------------------------------------
  Load more button
---------------------------------------------------------- */

add_filter('wputheme_loadmore_button', 'wputheme_loadmore_button__default', 10, 1);
if (!function_exists('wputheme_loadmore_button__default')) {
    function wputheme_loadmore_button__default($next_page_url) {
        return sprintf('<a class="load-more" href="%s">' . __('Next page', 'wputh') . '</a>', $next_page_url);
    }
}
