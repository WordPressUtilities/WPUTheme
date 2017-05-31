<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
?>
<div class="main-content">
<?php do_action('woocommerce_before_main_content'); ?>
<?php woocommerce_content(); ?>
<?php do_action('woocommerce_after_main_content'); ?>
</div>
<?php
get_sidebar();
get_footer();
