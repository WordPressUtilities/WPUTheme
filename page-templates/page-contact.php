<?php
/* Template Name: Contact */
include __DIR__ . '/../z-protect.php';
get_header();
the_post();
?>
<div class="main-content">
<article>
    <h1><?php the_title();?></h1>
    <?php the_content();?>
    <?php do_action('wpucontactforms_content', false, 'default');?>
</article>
</div>
<?php
get_sidebar();
get_footer();
