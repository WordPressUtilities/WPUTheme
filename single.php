<?php
include dirname(__FILE__) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main-content">
<?php

/* Content */
get_template_part('loop');

if (apply_filters('wputheme_display_single_share', true)):
    include get_template_directory() . '/tpl/loops/share.php';
endif;

/* Comments */
comments_template();

if (apply_filters('wputheme_display_single_prevnext', true)):
    include get_template_directory() . '/tpl/loops/prev-next.php';
endif;

?>
</div>
<?php
get_sidebar();
get_footer();
