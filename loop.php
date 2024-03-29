<?php
include __DIR__ . '/z-protect.php';
?><article <?php post_class(); ?>>
    <h1><?php the_title(); ?></h1>
    <aside class="lp-metas">
        <?php echo get_the_term_list( get_the_ID(), 'category', '', ', ', ' &bull;'  ); ?>
        <time class="lp-time" datetime="<?php echo get_the_time( DATE_W3C ); ?>"><?php echo get_the_time( __( 'F j, Y', 'wputh' ) ); ?></time>
        &bull;
        <?php echo __( 'By', 'wputh' ); ?> <?php echo get_the_author_link(); ?>
    </aside>
    <div class="cssc-content cssc-block"><?php the_content(); ?></div>
    <footer>
        <?php the_tags( '<p><strong>' . __( 'Tags:', 'wputh' ) . '</strong> ', ', ', '</p>' ); ?>
    </footer>
</article>
