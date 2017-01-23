<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();

$att_image = '';
$isImage = wp_attachment_is_image( $post->ID );
$att_image = wp_get_attachment_image_src( $post->ID, "medium" );

?>
<div class="main-content">
<article class="loop">
    <h1><?php the_title(); ?></h1>
    <div>
    <?php if ( $isImage ) : ?>
    <p><img src="<?php echo $att_image[0];?>" alt="<?php $post->post_excerpt; ?>" /></p>
    <?php else : ?>
    <a href="<?php echo wp_get_attachment_url( $post->ID ) ?>" title="<?php echo esc_html( get_the_title( $post->ID ), 1 ) ?>" rel="attachment">
        <?php echo basename( $post->guid ) ?>
    </a>
    <?php endif; ?>
    <?php include get_template_directory() . '/tpl/attachment/prev-next.php'; ?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
