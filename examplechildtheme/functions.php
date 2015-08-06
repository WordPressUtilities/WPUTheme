<?php

/* ----------------------------------------------------------
  Hide these blocks on parent theme
---------------------------------------------------------- */

add_filter('wputheme_display_searchform', '__return_false');
add_filter('wputheme_display_social', '__return_false');

/* ----------------------------------------------------------
  Load new blocks
---------------------------------------------------------- */

/* Home page
-------------------------- */

add_action('wputheme_home_content', 'mychildtheme_home_block');
function mychildtheme_home_block() {
    echo '<div>my block</div>';
}
