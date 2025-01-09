document.addEventListener("DOMContentLoaded", function() {
    'use strict';
    Array.prototype.forEach.call(document.querySelectorAll('.wputheme-swiper'), wputheme_call_default_swiper);
});

function wputheme_call_default_swiper($swipe) {
    var _autoplay = $swipe.getAttribute('data-slider-autoplay'),
        _autoplay_speed = $swipe.getAttribute('data-slider-autoplay-speed');
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
        loop: true,
        autoplay: _autoplay == '1' ? {
            delay: _autoplay_speed
        } : false
    });
}

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
        _settings.pagination.clickable = true;
        _utilsHTML += '<div class="swiper-pagination"></div>';
    }
    if (_settings.navigation) {
        if (!_settings.navigation.nextEl || (_settings.navigation.nextEl && typeof _settings.navigation.nextEl != 'object')) {
            _utilsHTML += '<div class="swiper-button-prev"></div>';
        }
        if (!_settings.navigation.prevEl || (_settings.navigation.prevEl && typeof _settings.navigation.prevEl != 'object')) {
            _utilsHTML += '<div class="swiper-button-next"></div>';
        }
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
