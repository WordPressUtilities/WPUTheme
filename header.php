<?php
require_once dirname( __FILE__ ) . '/z-protect.php';
if (!defined('IS_AJAX') || !IS_AJAX ) {
?>
<head><?php echo wp_head(); ?></head>
<body <?php body_class( 'no-js cssc-is-responsive' ); ?>>
<?php if (apply_filters('wputheme_display_skiplinks', true)): ?><a class="skiptomain" href="#maincontent"><?php echo __( 'Skip to main content', 'wputh' ); ?></a><?php endif; ?>
<?php do_action('wputheme_header_items'); ?>
<?php if (apply_filters('wputheme_display_header', true)): ?>
<div class="main-header centered-container">
    <header class="banner" role="banner" id="banner">
    <?php
    do_action('wputheme_header_banner');
    ?>
    </header>
</div>
<?php endif; ?>
<?php do_action('wputheme_header_elements'); ?>
<?php if (apply_filters('wputheme_display_mainwrapper', true)): ?>
<div class="main-container centered-container"><div class="main-container--inner" id="content">
<?php endif;
do_action('wputheme_main_overcontent');
if (apply_filters('wputheme_display_skiplinks', true)) { echo '<a id="maincontent"></a>'; }
}
do_action('wputheme_main_overcontent_inajax');
