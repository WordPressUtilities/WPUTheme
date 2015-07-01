<?php
include dirname( __FILE__ ) . '/z-protect.php';
if ( !IS_AJAX ) {
echo wputh_get_doctype_html();
?>
<head><?php echo wp_head(); ?></head>
<body <?php body_class( 'no-js cssc-is-responsive' ); ?>>
<?php do_action('wputheme_header_items'); ?>
<?php if (apply_filters('wputheme_display_header', true)): ?>
<div class="main-header centered-container">
    <header class="banner" role="banner" id="banner">
    <?php
    /* Title */
    do_action('wputheme_header_banner');
    /* Search form */
    include get_template_directory() . '/tpl/header/searchform.php';
    /* Social links */
    include get_template_directory() . '/tpl/header/social.php';
    /* Main menu */
    wp_nav_menu( array(
        'depth' => 1,
        'theme_location' => 'main',
        'menu_class' => 'main-menu'
    ) );
    ?>
    </header>
</div>
<?php endif; ?>
<?php do_action('wputheme_header_elements'); ?>
<?php if (apply_filters('wputheme_display_mainwrapper', true)): ?>
<div class="main-container centered-container"><div class="main-container--inner" id="content">
<?php endif; ?>
<?php }
if (apply_filters('wputheme_display_languages', true)):
include get_template_directory() . '/tpl/header/languages.php';
endif;
if (apply_filters('wputheme_display_breadcrumbs', true)):
include get_template_directory() . '/tpl/header/breadcrumbs.php';
endif;
if (apply_filters('wputheme_display_jsvalues', true)):
include get_template_directory() . '/tpl/header/jsvalues.php';
endif;
