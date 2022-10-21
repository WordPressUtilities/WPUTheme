function wputheme_setup_sharesheet() {
    /* Native sharing is disabled : remove block */
    jQuery('[data-share-method="sharesheet"]').each(function(i, $block) {
        if (navigator.share) {
            $block.style.display = '';
        }
        else {
            $block.parentNode.removeChild($block);
        }
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
