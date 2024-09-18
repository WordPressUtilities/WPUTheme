document.addEventListener("DOMContentLoaded", function() {
    'use strict';
    var $swipes = document.querySelectorAll('.wputheme-swiper');
    Array.prototype.forEach.call($swipes, function($swipe) {
        wputheme_swiper_init($swipe, {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination'
            },
            scrollbar: {
                el: '.swiper-scrollbar',
            },
            keyboard: {
                enabled: true,
            },
            watchSlidesProgress: true,
            loop: true
        });
    });
});

function wputheme_swiper_init($element, _settings) {
    'use strict';
    /* Build wrapper */
    var $swiper = document.createElement('div');
    $swiper.classList.add('swiper', 'wputheme-swiper-wrapper');
    $element.parentNode.insertBefore($swiper, $element.nextSibling);
    $swiper.appendChild($element);

    /* Add element class */
    $element.classList.add('swiper-wrapper');
    Array.prototype.forEach.call($element.classList, function(_class) {
        $swiper.classList.add('wrapper__' + _class);
    });

    /* Add slides classes */
    Array.prototype.forEach.call($element.children, function($slide) {
        $slide.classList.add('swiper-slide');
    });

    /* Add utilities */
    var _utilsHTML = '';
    if (_settings.pagination) {
        _utilsHTML += '<div class="swiper-pagination"></div>';
    }
    if (_settings.navigation) {
        _settings.pagination.clickable = true;
        _utilsHTML += '<div class="swiper-button-prev"></div>';
        _utilsHTML += '<div class="swiper-button-next"></div>';
    }
    if (_settings.scrollbar) {
        _utilsHTML += '<div class="swiper-scrollbar"></div>';
    }
    var $utils = document.createElement('div');
    $utils.innerHTML = _utilsHTML;
    $swiper.appendChild($utils);

    /* Init swiper */
    var _swiper = new Swiper($swiper, _settings);
    return _swiper;
}
