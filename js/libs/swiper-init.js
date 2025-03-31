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
    var $utils = false;
    var _utilsHTML = '';
    if (_settings.pagination) {
        if (typeof _settings.pagination != 'object' || !_settings.pagination.el) {
            _settings.pagination = {
                el: '.swiper-pagination'
            }
        }
        _settings.pagination.clickable = true;
        _utilsHTML += '<div class="swiper-pagination"></div>';
    }
    if (_settings.navigation) {
        if (typeof _settings.navigation != 'object') {
            _settings.navigation = {};
        }
        if (!_settings.navigation.nextEl || (_settings.navigation.nextEl && typeof _settings.navigation.nextEl != 'object')) {
            var _next_el_classname = '.swiper-button-next';
            if(typeof _settings.navigation.nextEl == 'string') {
                _next_el_classname = _settings.navigation.nextEl;
            }
            _settings.navigation.nextEl = _next_el_classname;
            _utilsHTML += '<div class="' + (_next_el_classname.replace(/\./g, ' ')) + '"></div>';
        }
        if (!_settings.navigation.prevEl || (_settings.navigation.prevEl && typeof _settings.navigation.prevEl != 'object')) {
            var _prev_el_classname = '.swiper-button-prev';
            if(typeof _settings.navigation.prevEl == 'string') {
                _prev_el_classname = _settings.navigation.prevEl;
            }
            _settings.navigation.prevEl = _prev_el_classname;
            _utilsHTML += '<div class="' + (_prev_el_classname.replace(/\./g, ' ')) + '"></div>';
        }
    }
    if (_settings.scrollbar) {
        _utilsHTML += '<div class="swiper-scrollbar"></div>';
    }

    if (_utilsHTML) {
        $utils = document.createElement('div');
        $utils.classList.add('swiper-utils');
        $utils.innerHTML = _utilsHTML;
        $swiper.appendChild($utils);
    }

    /* Init swiper */
    var _swiper = new Swiper($swiper, _settings);

    /* Add resize events */
    if ($utils){
        window.addEventListener('resize', wputheme_debounce(function() {
            wputheme_swiper_set_utils_attributes(_swiper, $utils);
        }, 250));
        wputheme_swiper_set_utils_attributes(_swiper, $utils);
    }
    return _swiper;
}

/*
 * Add attributes to utils
 */
function wputheme_swiper_set_utils_attributes(_swiper, $utils) {
    var _next_enabled = false,
        _prev_enabled = false,
        _pagination_enabled = false;
    if (_swiper.navigation) {
        if (_swiper.navigation.nextEl) {
            _next_enabled = !_swiper.navigation.nextEl.classList.contains('swiper-button-disabled');
        }
        if (_swiper.navigation.prevEl) {
            _prev_enabled = !_swiper.navigation.prevEl.classList.contains('swiper-button-disabled');
        }
    }
    if (_swiper.pagination && _swiper.pagination.el) {
        _pagination_enabled = _swiper.pagination.el.children.length > 1;
    }
    $utils.setAttribute('data-next-enabled', _next_enabled ? '1' : '0');
    $utils.setAttribute('data-prev-enabled', _prev_enabled ? '1' : '0');
    $utils.setAttribute('data-pagination-enabled', _pagination_enabled ? '1' : '0');
}
