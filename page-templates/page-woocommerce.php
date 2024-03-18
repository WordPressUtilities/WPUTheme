<?php

/* Template Name: Woocommerce */

include __DIR__ . '/../z-protect.php';

the_post();
get_header();
?>
<div class="main-content">
<?php do_action('woocommerce_before_main_content'); ?>
<?php the_content(); ?>
<?php do_action('woocommerce_after_main_content'); ?>
</div>
<?php
get_sidebar();
get_footer();
