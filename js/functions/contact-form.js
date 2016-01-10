jQuery(document).ready(function() {
    var $wrapper = jQuery('.wputh-contact-form-wrapper');
    $wrapper.on('submit', '.wputh__contact__form', function(e) {
        $wrapper.addClass('contact-form-is-loading');
        $wrapper.find('button').attr('aria-disabled','true').attr('disabled','disabled');
        jQuery('html, body').animate({
            scrollTop: $wrapper.offset().top-50
        }, 300);
        e.preventDefault();
        jQuery(this).ajaxSubmit({
            target: $wrapper,
            url: ajaxurl,
            success: function() {
                $wrapper.removeClass('contact-form-is-loading');
            }
        });
    });
});
