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
        function setRatio() {
            juxtaposeRatio = $e.outerHeight() / $e.outerWidth();
            if (!juxtaposeRatio) {
                juxtaposeRatio = 9/16;
            }
        }

        setRatio();
        jQuery(window).on('load', function(event) {
            setRatio();
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
