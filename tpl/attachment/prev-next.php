<?php
global $post;
// Previous & next attachment

$attachments = wputh_gallery_get_attachments($post->post_parent);
$current_attachment = -1;

if (empty($attachments) || count($attachments) < 2) {
    return;
}

$image_size = apply_filters('wputh_pagination_attachments__image_size','thumbnail');

$nb_attachments = count($attachments);

// Searching for attachment index
foreach ($attachments as $i => $attachment) {
    if ($attachment->ID == $post->ID) {
        $current_attachment = $i;
    }
}

if ($current_attachment === -1) {
    return;
}

// Setting Previous & Next
$previous_attachment = $current_attachment - 1;
$next_attachment = $current_attachment + 1;
if ($previous_attachment < 0) {
    $previous_attachment = $nb_attachments - 1;
}
if ($next_attachment >= $nb_attachments) {
    $next_attachment = 0;
}

// Previous
$previous = $attachments[$previous_attachment];
$previous_content = '';
if (wp_attachment_is_image($previous->ID)) {
    $previous_content = wp_get_attachment_image($previous->ID, $image_size);
} else {
    $previous_content = apply_filters('the_title', $previous->post_title);
}

// Next
$next = $attachments[$next_attachment];
$next_content = '';
if (wp_attachment_is_image($next->ID)) {
    $next_content = wp_get_attachment_image($next->ID, $image_size);
} else {
    $next_content = apply_filters('the_title', $next->post_title);
}

if (!function_exists('wputh_pagination_attachments')) {
    function wputh_pagination_attachments($prev_id, $previous_content, $next_id, $next_content) {
        $html = '';

        // Display attachment navigation
        $html .= '<a class="prev" href="' . get_attachment_link($prev_id) . '">' . $previous_content . '</a>';
        $html .= '<a class="next" href="' . get_attachment_link($next_id) . '">' . $next_content . '</a>';

        return '<div class="pagination-attachment">' . $html . '</div>';
    }
}

echo wputh_pagination_attachments($previous->ID, $previous_content, $next->ID, $next_content);
