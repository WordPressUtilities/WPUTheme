jQuery(document).ready(function() {
    var $wrapper = jQuery('.wputh-contact-form-wrapper');
    $wrapper.on('submit', '.wputh__contact__form', function(e) {
        $wrapper.addClass('contact-form-is-loading');
        e.preventDefault();
        jQuery.post(
            ajaxurl, jQuery(this).serialize(),
            function(response) {
                $wrapper.html(jQuery(response).html());
                $wrapper.removeClass('contact-form-is-loading');
            }
        );
    });
});
