<?php
$_title = get_the_title();
$_permalink = get_permalink();
$_image = '';
if (function_exists('wputhumb_get_thumbnail_url')) {
    $_image = urlencode(wputhumb_get_thumbnail_url('thumbnail', get_the_ID()));
}

$_methods = array(
    'twitter' => array(
        'name' => 'Twitter',
        'url' => 'https://twitter.com/home?status=' . urlencode($_title) . '+' . urlencode($_permalink)
    ) ,
    'facebook' => array(
        'name' => 'Facebook',
        'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($_permalink)
    ) ,
    'pinterest' => array(
        'name' => 'Pinterest',
        'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($_permalink) . (!empty($_image) ? '&media=' . $_image : '') . '&description=' . urlencode($_title)
    ) ,
    'googleplus' => array(
        'name' => 'Google Plus',
        'url' => 'https://plus.google.com/share?url=' . urlencode($_permalink)
    ) ,
    'email' => array(
        'name' => 'Email',
        'url' => str_replace('+', '%20', 'mailto:mail@mail.com?subject=' . urlencode($_title) . '&body=' . urlencode($_title) . '+' . urlencode($_permalink))
    )
);
?>
<div class="post-share">
    <h3><?php echo __('Share this post', 'wputh'); ?></h3>
    <ul class="share-list">
        <?php foreach ($_methods as $_id => $_method) { ?>
        <li><a target="_blank" href="<?php echo $_method['url']; ?>" class="<?php echo $_id; ?>"><?php echo $_method['name']; ?></a></li>
        <?php } ?>
    </ul>
</div>