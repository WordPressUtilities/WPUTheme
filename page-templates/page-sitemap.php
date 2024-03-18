<?php
/* Template Name: Sitemap */
include __DIR__ . '/../z-protect.php';
include get_template_directory() . '/tpl/sitemap/datas.php';

/* ----------------------------------------------------------
  Page content
---------------------------------------------------------- */

get_header();
the_post();
?>
<div class="main-content">
<article>
    <h1 class="main-content__title"><?php the_title();?></h1>
<?php
foreach ($sitemap_posts as $sitemap_post) {
    if (empty($sitemap_post['posts'])) {
        continue;
    }
    echo '<div class="block-sitemap">';
    echo '<h2 class="block-sitemap__title">' . $sitemap_post['title'] . '</h2>';
    echo get_pages_sitemap_child_of($sitemap_post['post_type'], $sitemap_post['posts'], 0);
    echo '</div>';
}
?>
</article>
</div>
<?php
get_sidebar();
get_footer();
