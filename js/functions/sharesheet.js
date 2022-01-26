function wputheme_setup_sharesheet() {
    if (navigator.share) {
        return;
    }

    /* Native sharing is disabled : remove block */
    jQuery('[data-share-title],[data-share-url]').each(function() {
        var $item = jQuery(this),
            $parent = $item.parent();
        /* Remove parent node if this one is an only child */
        if ($parent.children().length == 1) {
            $parent.remove();
            return;
        }
        $item.remove();
    });
}

jQuery(document).ready(function($) {
    /* Share on click */
    jQuery('body').on('click', '[data-share-title],[data-share-url]', function(e) {
        e.preventDefault();
        navigator.share({
            title: jQuery(this).attr('data-share-title'),
            url: jQuery(this).attr('data-share-url'),
        });
    });
    /* Conditionally hide sharesheet  */
    wputheme_setup_sharesheet();
    jQuery('body').on('wpu-ajax-ready', wputheme_setup_sharesheet);
});
