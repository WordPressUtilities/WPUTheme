<?php
/* Template Name: Sitemap */
include dirname(__FILE__) . '/../z-protect.php';
include get_template_directory() . '/tpl/sitemap/datas.php';

/* ----------------------------------------------------------
  Page content
---------------------------------------------------------- */

get_header();
the_post();

foreach ($sitemap_posts as $sitemap_post) {
    if (empty($sitemap_post['posts'])) {
        continue;
    }
    echo '<h3>' . $sitemap_post['title'] . '</h3>';
    echo get_pages_sitemap_child_of($post_type, $sitemap_post['posts'], 0);
}

get_sidebar();
get_footer();
