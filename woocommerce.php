<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
?>
<div class="main-content">
<?php woocommerce_content(); ?>
</div>
<?php
get_sidebar();
get_footer();
