<?php
include dirname( __FILE__ ) . '/z-protect.php';
$main_tag = is_home() ? 'h1' : 'div';
if ( !isset( $_GET['is_ajax'] ) ) {
echo wputh_get_doctype_html();
?>
<head><?php echo wp_head(); ?></head>
<body <?php body_class( 'no-js cssc-is-responsive' ); ?>>
<div class="main-header centered-container">
    <header class="banner" role="banner" id="banner">
    <?php
    /* Title */
    echo '<'.$main_tag.' class="h1 main-title"><a href="' . site_url() . '">'.get_bloginfo( 'name' ).'</a></'.$main_tag.'>';
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
<div class="main-container centered-container"><div class="main-container--inner" id="content">
<?php }
include get_template_directory() . '/tpl/header/languages.php';
include get_template_directory() . '/tpl/header/breadcrumbs.php';
include get_template_directory() . '/tpl/header/jsvalues.php';
