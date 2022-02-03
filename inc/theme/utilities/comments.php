<?php

/**
 * Get comments title
 *
 * @param unknown $count_comments
 * @param unknown $zero           (optional)
 * @param unknown $one            (optional)
 * @param unknown $more           (optional)
 * @param unknown $closed         (optional)
 * @return unknown
 */
function wputh_get_comments_title($count_comments, $zero = false, $one = false, $more = false, $closed = false) {
    global $post;
    $return = '';
    if (is_array($count_comments)) {
        $count_comments = count($count_comments);
    }
    if (!is_numeric($count_comments)) {
        $count_comments = $post->comment_count;
    }
    if ($zero === false) {
        $zero = __('<strong>no</strong> comments', 'wputh');
    }
    if ($one === false) {
        $one = __('<strong>1</strong> comment', 'wputh');
    }
    if ($more === false) {
        $more = __('<strong>%s</strong> comments', 'wputh');
    }
    if ($closed === false) {
        $closed = __('Comments are closed', 'wputh');
    }
    if (!comments_open()) {
        $return = $closed;
    } else {
        switch ($count_comments) {
        case 0:
            $return = $zero;
            break;
        case 1:
            $return = $one;
            break;
        default:
            $return = sprintf($more, $count_comments);
        }
    }

    return $return;
}

/**
 * Get comment author name with link
 *
 * @param unknown $comment
 * @return unknown
 */
function wputh_get_comment_author_name_link($comment) {
    $return = '';
    $comment_author_url = '';
    if (!empty($comment->comment_author_url)) {
        $comment_author_url = $comment->comment_author_url;
    }
    if (empty($comment_author_url) && $comment->user_id != 0) {
        $user_info = get_user_by('id', $comment->user_id);
        $comment_author_url = $user_info->user_url;
    }

    $return = $comment->comment_author;

    if (!empty($comment_author_url)) {
        $return = '<a href="' . $comment_author_url . '" target="_blank">' . $return . '</a>';
    }

    return '<strong class="comment_author_url">' . $return . '</strong>';
}
