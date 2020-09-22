<?php
/* Template Name: Page ACF Flexible */
add_filter('wputheme_display_mainwrapper', '__return_false');
get_header();
the_post();
do_action('wputh_page_template_flexible__before_blocks');
?>
<div class="main-content main-content--contenu">
    <div>
        <?php
        if (function_exists('get_wpu_acf_flexible_content')) {
            echo get_wpu_acf_flexible_content(apply_filters('wputh_page_template_flexible__group_name', 'content-blocks'));
        }
        ?>
    </div>
</div>
<?php
do_action('wputh_page_template_flexible__after_blocks');
get_footer();
