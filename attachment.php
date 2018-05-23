<?php
include dirname(__FILE__) . '/z-protect.php';
get_header();
the_post();

$att_image = '';
$isImage = wp_attachment_is_image(get_the_ID());
$att_image = wp_get_attachment_image_src(get_the_ID(), "large");

?>
<div class="main-content">
<article class="loop">
    <h1><?php the_title();?></h1>
    <div>
    <?php if ($isImage): ?>
    <p><img src="<?php echo $att_image[0]; ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /></p>
    <?php else: ?>
    <a href="<?php echo wp_get_attachment_url(get_the_ID()); ?>" title="<?php echo esc_attr(get_the_title()); ?>" rel="attachment">
        <?php echo basename($post->guid); ?>
    </a>
    <?php endif;?>
    <?php include get_stylesheet_directory() . '/tpl/attachment/prev-next.php';?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
