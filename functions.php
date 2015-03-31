<?php
session_start();
include dirname( __FILE__ ) . '/z-protect.php';

do_action('wputh_functionsphp_start');

/* Globals
-------------------------- */

define( "THEME_URL", get_template_directory_uri() );
define( "IS_AJAX", isset($_GET['ajax']) );
define( 'PAGINATION_KIND', 'numbers' ); // load-more || numbers || default

/* Pages IDs
 -------------------------- */

if (!function_exists('wputh_set_pages_site')) {
    function wputh_set_pages_site($pages_site) {
        $pages_site = array(
            'about__page_id' => array(
                'constant' => 'ABOUT__PAGE_ID',
                'post_title' => 'A Propos',
                'post_content' => '<p>A Propos de ce site.</p>',
            ) ,
            'mentions__page_id' => array(
                'constant' => 'MENTIONS__PAGE_ID',
                'post_title' => 'Mentions légales',
                'post_content' => '<p>Contenu des mentions légales</p>',
            ) ,
        );
        return $pages_site;
    }
}

add_filter('wputh_pages_site', 'wputh_set_pages_site');

$pages_site = apply_filters('wputh_pages_site', array());

foreach ($pages_site as $id => $option) {
    if (!isset($option['constant'])) {
        $option['constant'] = strtoupper($id);
    }
    define($option['constant'], get_option($id));
}

/* Social links
-------------------------- */

if (!isset($wpu_social_links) || !is_array($wpu_social_links)) {
    $wpu_social_links = array(
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
    );
}

define('WPU_SOCIAL_LINKS', serialize($wpu_social_links));

/* Menus
-------------------------- */

register_nav_menus( array(
        'main' => __( 'Main menu', 'wputh' ),
    ) );

/* Post Types
-------------------------- */

add_filter('wputh_get_posttypes', 'wputh_set_theme_posttypes');
if (!function_exists('wputh_set_theme_posttypes')) {
    function wputh_set_theme_posttypes($post_types) {
        // $post_types['work'] = array(
        //     'menu_icon' => 'dashicons-portfolio',
        //     'name' => __('Work', 'wputh') ,
        //     'plural' => __('Works', 'wputh') ,
        //     'female' => 0
        // );
        return $post_types;
    }
}

/* Taxonomies
-------------------------- */

add_filter( 'wputh_get_taxonomies', 'wputh_set_theme_taxonomies' );
if(!function_exists('wputh_set_theme_taxonomies')){
    function wputh_set_theme_taxonomies( $taxonomies ) {
        $taxonomies = array(
            // 'work-type' => array(
            //     'name' => __( 'Work type', 'wputh' ),
            //     'post_type' => 'work'
            // )
        );
        return $taxonomies;
    }
}

/* Sidebars
-------------------------- */

register_sidebar( array(
        'name' => __( 'Default Sidebar', 'wputh' ),
        'id' => 'wputh-sidebar',
        'description' => __( 'Default theme sidebar', 'wputh' ),
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ) );


/* Thumbnails
-------------------------- */

// Default thumbnail size
if ( function_exists( 'set_post_thumbnail_size' ) ) {
    set_post_thumbnail_size( 1200, 1200 );
}

/* ----------------------------------------------------------
  Includes
---------------------------------------------------------- */

/* Theme
-------------------------- */

include get_template_directory() . '/inc/theme/params.php';
include get_template_directory() . '/inc/theme/utilities.php';
include get_template_directory() . '/inc/theme/shortcodes.php';
include get_template_directory() . '/inc/theme/activation.php';
include get_template_directory() . '/inc/theme/customize.php';
include get_template_directory() . '/inc/theme/head.php';
include get_template_directory() . '/inc/theme/display.php';

if ( ! isset( $content_width ) ) $content_width = 680;

/* Plugins Configuration
-------------------------- */

include get_template_directory() . '/inc/plugins/wpu-options.php';
include get_template_directory() . '/inc/plugins/wpu-postmetas.php';
include get_template_directory() . '/inc/plugins/wpu-usermetas.php';
include get_template_directory() . '/inc/plugins/wpu-tinymce.php';

/* Assets
-------------------------- */

include get_template_directory() . '/inc/assets/styles.php';
include get_template_directory() . '/inc/assets/scripts.php';

/* Widgets
-------------------------- */

include get_template_directory() . '/tpl/widgets/widget_post_categories.php';

/* Langs
-------------------------- */

add_action( 'after_setup_theme', 'wputh_setup' );

function wputh_setup() {
    load_theme_textdomain( 'wputh', get_template_directory() . '/inc/lang' );
}


do_action('wputh_functionsphp_end');