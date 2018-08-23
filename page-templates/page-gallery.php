<?php
/* Template Name: Gallery */
include dirname(__FILE__) . '/../z-protect.php';

get_header();
the_post();
?>
<div class="main-content">
<article>
    <h1 class="main-content__title"><?php the_title();?></h1>
<?php
the_content();

$order = get_post_meta(get_the_ID(), 'wputh_gallery_order', 1);
if (!$order) {
    $order = 'DESC';
}
$orderby = get_post_meta(get_the_ID(), 'wputh_gallery_orderby', 1);
if (!$orderby) {
    $orderby = 'ideas';
}
$backgroundmethod = get_post_meta(get_the_ID(), 'wputh_gallery_backgroundmethod', 1);
if (!$backgroundmethod) {
    $backgroundmethod = 'background';
}

$attachments = get_posts(array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'orderby' => $orderby,
    'order' => $order,
    'post_status' => 'any',
    'post_mime_type' => 'image',
    'post_parent' => $post->ID)
);
if (!empty($attachments)) {
    echo '<ul class="gallery-list">';
    foreach ($attachments as $attachment) {
        $attachment_url = get_attachment_link($attachment->ID);
        $attachment_thumb = wp_get_attachment_image_src($attachment->ID, 'thumbnail');
        $img_url = $attachment_thumb[0];
        $attr = 'style="background-image:url(' . $img_url . ');"';
        if ($backgroundmethod == 'lazy') {
            $attr = 'data-vllsrc="' . $img_url . '" data-vlltype="background"';
        }

        echo '<li><a ' . $attr . ' href="' . $attachment_url . '">';
        echo '<strong class="trans-opa">' . apply_filters('the_title', $attachment->post_title) . '</strong>';
        echo '</a></li>';
    }
    echo '</ul>';
}

?>
</article>
</div>
<?php
get_sidebar();
get_footer();
