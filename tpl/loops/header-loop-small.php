<?php
$author_link = get_the_author_posts_link();
?><header>
    <h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
    <aside class="lpsm-metas">
<?php
if ($author_link) {
    echo '<span class="author">' . __('By', 'wputh') . ' ' . $author_link . '</span>';
    echo '<span class="sep">&bull;</span>';
}
echo wputh_get_time_tag();
?>
    </aside>
</header>
