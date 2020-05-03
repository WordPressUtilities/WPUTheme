<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
?>
<div class="main-content">
<article>
    <h1 class="title"><?php echo __( '404 Error', 'wputh' ); ?></h1>
    <p><?php echo __( 'Sorry, but this page doesn&rsquo;t exists.', 'wputh' ); ?></p>
    <p><a href="<?php echo get_site_url(); ?>" class="cssc-button cssc-button--404"><span><?php echo __( 'Back to the homepage', 'wputh' ); ?></span></a></p>
</article>
</div>
<?php
get_sidebar();
get_footer();
