<?php
include __DIR__ . '/z-protect.php';
get_header();
do_action('wputheme_home_content');
get_sidebar();
get_footer();
