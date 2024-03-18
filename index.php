<?php
include __DIR__ . '/z-protect.php';
get_header();

echo '<div class="main-content">';
echo get_the_loop();
echo '</div>';
get_sidebar();
get_footer();
