jQuery(document).ready(function($) {

    if (typeof juxtapose != 'object' && typeof juxtapose.sliders != 'object') {
        return;
    }

    // Fix juxtapose.js responsive behavior
    // https://github.com/NUKnightLab/juxtapose/issues/105
    var $juxtapose = jQuery('.juxtapose');
    $juxtapose.each(function(index, element) {
        var $juxtaposeContainer = $juxtapose.parent();
        var $e = jQuery(element);

        var juxtaposeRatio;

        jQuery(window).on('load', function(event) {
            juxtaposeRatio = $e.outerHeight() / $e.outerWidth();
        });

        jQuery(window).on('resize', function(event) {
            var newWidth = $juxtaposeContainer.outerWidth();
            var newHeight = newWidth * juxtaposeRatio;
            $e.css({
                width: newWidth,
                height: newHeight
            });
        });

    });
});
