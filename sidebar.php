<?php
include dirname( __FILE__ ) . '/z-protect.php';
if ( WPUTH_HAS_SIDEBAR && is_active_sidebar( 'wputh-sidebar' ) ) : ?>
<aside>
    <ul class="wputh-sidebar">
        <?php dynamic_sidebar( 'wputh-sidebar' ); ?>
    </ul>
</aside>
<?php endif;
