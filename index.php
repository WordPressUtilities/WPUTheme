<?php
include __DIR__ . '/z-protect.php';
get_header();
echo '<div class="main-content">';
echo function_exists('get_the_loop') ? get_the_loop() : 'No content available.';
echo '</div>';
get_sidebar();
get_footer();
