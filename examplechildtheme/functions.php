<?php
require_once dirname(__FILE__) . '/../WPUTheme/z-protect.php';

/* ----------------------------------------------------------
  Hide these blocks on parent theme
---------------------------------------------------------- */

add_filter('wputheme_display_searchform', '__return_false');
add_filter('wputheme_display_social', '__return_false');

/* ----------------------------------------------------------
  Social links
---------------------------------------------------------- */

add_filter('wputheme_social_links', 'wpuchildtheme_social_links');
function wpuchildtheme_social_links($links) {
    $links = array(
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
    );
    return $links;
}

/* ----------------------------------------------------------
  Load new blocks
---------------------------------------------------------- */

/* Home page
 -------------------------- */

add_action('wputheme_home_content', 'mychildtheme_home_block');
if (!function_exists('mychildtheme_home_block')) {
    function mychildtheme_home_block() {
        echo '<div>my block</div>';
    }
}
