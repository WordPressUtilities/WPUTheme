/* globals jQuery,ajaxurl */

'use strict';

jQuery(document).ready(function() {
    set_wputh_contact_form();
});

/* ----------------------------------------------------------
  Set Contact form
---------------------------------------------------------- */

function set_wputh_contact_form() {
    var $wrapper = jQuery('.wputh-contact-form-wrapper');

    function submit_form(e) {
        e.preventDefault();
        $wrapper.addClass('contact-form-is-loading');
        $wrapper.find('button').attr('aria-disabled', 'true').attr('disabled', 'disabled');
        $wrapper.trigger('wputh_contact_before_ajax');
        jQuery(this).ajaxSubmit({
            target: $wrapper,
            url: ajaxurl,
            success: ajax_success
        });
    }

    function ajax_success() {
        $wrapper.removeClass('contact-form-is-loading');
        $wrapper.trigger('wputh_contact_after_ajax');
    }

    /* Events -------------------------- */

    /* Form submit */
    $wrapper.on('submit', '.wputh__contact__form', submit_form);

    /* Special actions before AJAX send */
    $wrapper.on('wputh_contact_before_ajax', function() {
        jQuery('html, body').animate({
            scrollTop: $wrapper.offset().top - 50
        }, 300);
    });

}
