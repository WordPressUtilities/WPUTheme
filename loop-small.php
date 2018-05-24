<?php
include dirname( __FILE__ ) . '/z-protect.php';
$has_thumb = has_post_thumbnail();
?><article <?php post_class( 'loop-small' ); ?>>
    <div class="<?php echo $has_thumb ? 'bmedia':''; ?>">
        <?php if ( $has_thumb ): ?>
        <div>
            <?php echo the_post_thumbnail( 'thumbnail' ); ?>
        </div>
        <?php endif; ?>
        <div class="bm-cont">
            <?php get_template_part('tpl/loops/header-loop-small'); ?>
            <?php the_excerpt(); ?>
            <?php if(wputh_has_more()): ?>
                <p><a href="<?php the_permalink(); ?>"><?php echo __( 'Read more' ); ?></a></p>
            <?php endif; ?>
            <footer class="lpsm-metas">
                <?php the_category( ', ' ); ?>
            </footer>
        </div>
    </div>
</article>
