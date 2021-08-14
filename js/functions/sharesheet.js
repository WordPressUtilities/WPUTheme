jQuery(document).ready(function($) {
    var $shareItems = jQuery('[data-share-title],[data-share-url]');
    if (!$shareItems.length) {
        return;
    }

    /* Native sharing is disabled : remove block */
    if (!navigator.share) {
        $shareItems.each(function() {
            var $item = jQuery(this),
                $parent = $item.parent();
            if ($parent.children().length == 1) {
                $parent.remove();
                return;
            }
            $item.remove();
        });
        return;
    }

    $shareItems.on('click', function(e) {
        e.preventDefault();
        navigator.share({
            title: jQuery(this).attr('data-share-title'),
            url: jQuery(this).attr('data-share-url'),
        });
    });
});
