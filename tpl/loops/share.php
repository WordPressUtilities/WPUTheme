<?php
$_methods = wputh_get_share_methods($post);
?>
<div class="post-share">
    <h3><?php echo __('Share this post', 'wputh'); ?></h3>
    <ul class="share-list">
        <?php foreach ($_methods as $_id => $_method) { ?>
        <li><a target="_blank" href="<?php echo $_method['url']; ?>" class="<?php echo $_id; ?>"><?php echo $_method['name']; ?></a></li>
        <?php } ?>
    </ul>
</div>