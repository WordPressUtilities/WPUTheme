jQuery(document).ready(function() {
    var $wrapper = jQuery('.wputh-contact-form-wrapper');
    $wrapper.on('submit', '.wputh__contact__form', function(e) {
        $wrapper.addClass('contact-form-is-loading');
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
