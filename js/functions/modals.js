/*
<a data-wputheme-modal="mymodal" href="#">Open me</a>
<div data-wputheme-build-modal="mymodal">htmlcontent</div>;
 */

/* ----------------------------------------------------------
  Setup
---------------------------------------------------------- */

document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    /* Build all modals */
    document.querySelectorAll('[data-wputheme-build-modal]').forEach(function(item) {
        wputheme_modal_build(item);
    });

    /* Open modals on click */
    document.body.addEventListener('click', function(e) {
        var target = e.target.closest('[data-wputheme-modal]');
        if (!target) {
            return;
        }
        e.preventDefault();
        wputheme_modal_open(target.getAttribute('data-wputheme-modal'));
    });

    /* Close modals */
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
            e.preventDefault();
            wputheme_modal_close();
        }
    });

    /* Close on echap */
    document.addEventListener('keydown', function(e) {
        if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
            return;
        }
        if (e.key === 'Escape') {
            wputheme_modal_close();
        }
    });

});

/* ----------------------------------------------------------
  Build modal
---------------------------------------------------------- */

function wputheme_modal_build($modal) {
    'use strict';

    var modalId = $modal.getAttribute('data-wputheme-build-modal');

    /* Wrapper */
    var $modalWrapper = wputheme_build_item('div', {
        'id': modalId,
        'class': 'modal-wrapper modal-wrapper--'+ modalId,
        'aria-hidden': 'true',
        'tabindex': '-1'
    }, {
        parent: document.body
    });

    /* Overlay */
    var $modalOverlay = wputheme_build_item('div', {
        'class': 'modal-overlay'
    }, {
        parent: $modalWrapper
    });

    /* Inner */
    var $modalInner = wputheme_build_item('div', {
        'class': 'modal-inner'
    }, {
        parent: $modalWrapper
    });

    var $modalContent = wputheme_build_item('div', {
        'class': 'modal-content'
    }, {
        parent: $modalInner,
        content: $modal.firstElementChild
    });

    /* Close button */
    var $modalClose = wputheme_build_item('button', {
        'class': 'modal-close'
    }, {
        parent: $modalInner,
        content: '<span>&times;</span>'
    });

    /* Remove original element */
    $modal.remove();
}

/* ----------------------------------------------------------
  Open modal
---------------------------------------------------------- */

function wputheme_modal_open($modal) {
    'use strict';

    if (typeof $modal === 'string') {
        $modal = document.getElementById($modal);
    }
    if (!$modal) {
        return;
    }
    $modal.removeAttribute('data-init-modal');

    /* Open iframe */
    var iframe = $modal.querySelector('iframe');
    if (iframe) {
        iframe.src = iframe.getAttribute('data-src');
    }

    /* Open modal */
    document.body.setAttribute('data-modal-open', '1');
    $modal.classList.add('is-open');
    $modal.setAttribute('aria-hidden', 'false');
    $modal.focus();
}

/* ----------------------------------------------------------
  Close modal
---------------------------------------------------------- */

function wputheme_modal_close() {
    'use strict';
    document.body.removeAttribute('data-modal-open');
    document.querySelectorAll('.modal-wrapper.is-open').forEach(function($modal) {
        $modal.classList.remove('is-open');
        $modal.setAttribute('aria-hidden', 'true');

        /* Close iframe */
        var iframe = $modal.querySelector('iframe');
        if (iframe) {
            iframe.src = '';
        }
    });
}
