<?php
include __DIR__ . '/z-protect.php';
get_header();
the_post();

/* Attachment */
$isImage = wp_attachment_is_image(get_the_ID());
if ($isImage) {
    $att_image_size = apply_filters('wputh_attachment__image_size', 'large', get_the_ID());
    $att_image = wp_get_attachment_image_src(get_the_ID(), $att_image_size);
}

/* Full link */
$displayFullLink = apply_filters('wputh_attachment__full_link', true);
$fullLink = wp_get_attachment_url(get_the_ID());
$fullLinkText = apply_filters('wputh_attachment__full_link_text', basename($fullLink));

/* Parent Post */
$parentPostHTML = '';
if (is_object($post) && isset($post->post_parent) && $post->post_parent > 0) {
    $parentPostHTML = wputh_link($post->post_parent);
}

?>
<div class="main-content">
<article class="loop loop-attachment">
    <h1 class="title"><?php the_title();?></h1>
    <?php if ($parentPostHTML): ?><p class="parent-post"><?php echo $parentPostHTML; ?></p><?php endif;?>
    <div class="loop-attachment-content">
    <?php if ($isImage): ?>
    <p class="image-wrapper"><img src="<?php echo $att_image[0]; ?>" alt="<?php echo esc_attr(get_the_title()); ?>" /></p>
    <?php if ($displayFullLink): ?><p class="fulllink-wrapper"><a target="_blank" href="<?php echo $fullLink; ?>"><?php echo $fullLinkText; ?></a></p><?php endif;?>
    <?php else: ?>
    <a target="_blank" href="<?php echo $fullLink; ?>" title="<?php echo esc_attr(get_the_title()); ?>" rel="attachment"><?php echo $fullLinkText; ?></a>
    <?php endif;?>
    <?php get_template_part('tpl/attachment/prev-next');?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
