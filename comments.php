<?php
include __DIR__ . '/z-protect.php';

if (post_password_required()) {
    return;
}

echo '<div class="comments">';
comment_form();
echo '<ul class="comments-list">';
wp_list_comments();
echo '</ul>';
paginate_comments_links();
echo '</div>';
