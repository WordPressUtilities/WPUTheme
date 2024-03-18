<?php
include __DIR__ . '/z-protect.php';
get_header();
the_post();
?>
<div class="main-content">
<?php

/* Content */
get_template_part('loop');

if (apply_filters('wputheme_display_single_share', true)):
    get_template_part('tpl/loops/share');
endif;

/* Comments */
comments_template();

if (apply_filters('wputheme_display_single_prevnext', true)):
    get_template_part('tpl/loops/prev-next');
endif;

?>
</div>
<?php
get_sidebar();
get_footer();
