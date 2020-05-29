jQuery(document).ready(function($) {
    jQuery('.wputheme-slick').each(function() {
        jQuery(this).slick();
    });
    jQuery('.wputheme-slick-mobile').each(function() {
        slick_on_mobile(jQuery(this));
    });
});

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

function slick_on_mobile($slider, settings) {
    var $window = jQuery(window);
    $window.on('resize', set_slider);
    set_slider();

    function set_slider() {
        var _layout = window.getComputedStyle(document.body, ':after').getPropertyValue('content');
        if (_layout == 'desktop' || _layout == 'large' || _layout == '"desktop"' || _layout == '"large"') {
            if ($slider.hasClass('slick-initialized')) {
                $slider.slick('unslick');
            }
            return;
        }
        if (!$slider.hasClass('slick-initialized')) {
            /* Setup slick lazyloading */
            $slider.find('[data-vllsrc]').each(function() {
                var $this = jQuery(this);
                $this.attr('data-lazy', $this.attr('data-vllsrc'));
            });
            return $slider.slick(settings);
        }
    }
}
