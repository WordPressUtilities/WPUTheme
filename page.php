<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main-content">
<article <?php post_class(); ?>>
    <h1><?php the_title(); ?></h1>
    <div class="cssc-content cssc-block">
<?php
    the_content();

    /* If a nextpage tag is used */
    wp_link_pages();

    /* Displaying child pages */
    if (apply_filters('wputheme_display_page_child', true)):
        get_template_part('tpl/loops/child-pages');
    endif;
?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
