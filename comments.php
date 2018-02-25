<?php
include dirname(__FILE__) . '/z-protect.php';

echo '<div class="comments">';
comment_form();
echo '<ul class="comments-list">';
wp_list_comments();
echo '</ul>';
paginate_comments_links();
echo '</div>';
