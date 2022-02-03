<?php

/* Displaying child pages */
$args = array(
    'posts_per_page' => -1,
    'post_type' => get_post_type(),
    'orderby' => 'title',
    'order' => 'ASC',
    'post_parent' => get_the_ID()
);
$wpq_child_pages = new WP_Query($args);
if ($wpq_child_pages->have_posts()) {
    echo '<ul class="post-child-pages">';
    while ($wpq_child_pages->have_posts()) {
        $wpq_child_pages->the_post();
        echo '<li>';
        echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
        echo '</li>';
    }
    echo '</ul>';
}
wp_reset_postdata();
