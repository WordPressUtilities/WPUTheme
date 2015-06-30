jQuery(document).ready(function() {
    jQuery('body').removeClass('no-js');
    search_form_check();
    wpu_set_faq_accordion();
});

/* ----------------------------------------------------------
  Search form
---------------------------------------------------------- */

var search_form_check = function() {
    var el_search_form = jQuery('#header-search');
    if (el_search_form) {
        el_search_form.on('submit', function(e) {
            var input = jQuery('#s');
            if (!input || input.val().trim() === '') {
                e.preventDefault();
            }
        });
    }
};

/* ----------------------------------------------------------
  Set FAQ accordion
---------------------------------------------------------- */

var wpu_set_faq_accordion = function() {
    var faq_content = jQuery('#faq-content');
    if (faq_content.length > 0) {
        var faq_togglers = jQuery('.faq-element__title');
        var faq_elements = jQuery('.faq-element');
        faq_togglers.each(function(i, el) {
            var el = jQuery(el);
            el.attr('data-i', i);
            el.on('click', function(e) {
                faq_elements.addClass('is-hidden');
                var i = el.attr('data-i');
                if (faq_elements.eq(i)) {
                    faq_elements.eq(i).removeClass('is-hidden');
                }
            });
        });
        faq_togglers.eq(0).trigger('click');
    }
};