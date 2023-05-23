<?php
require_once dirname( __FILE__ ) . '/z-protect.php';
if (!defined('IS_AJAX') || !IS_AJAX ) {
?>
<head><?php echo wp_head(); ?></head>
<body <?php body_class('cssc-is-responsive'); ?>>
<?php wp_body_open(); ?>
<?php do_action('wputheme_header_items'); ?>
<?php if (apply_filters('wputheme_display_header', true)): ?>
<div class="main-header centered-container">
    <header class="banner" id="banner">
    <?php
    do_action('wputheme_header_banner');
    ?>
    </header>
</div>
<?php endif; ?>
<?php do_action('wputheme_header_elements'); ?>
<?php if (apply_filters('wputheme_display_mainwrapper', true)): ?>
<div class="main-container centered-container"><div class="main-container--inner" id="content">
<?php else: ?>
<?php if (apply_filters('wputheme_display_mainwrapper__content', true)): ?>
<div id="content">
<?php endif; ?>
<?php endif;
do_action('wputheme_main_overcontent');
if (apply_filters('wputheme_display_skiplinks', true)) { echo '<div id="maincontent"></div>'; }
}
do_action('wputheme_main_overcontent_inajax');
