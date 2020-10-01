jQuery(document).ready(function($) {
    var $jQBody = jQuery('body'),
        $jQMainMenu = jQuery('.main-menu');

    /* Add toggle into main menu */
    (function() {
        var $toggle = jQuery('<a href="#" class="nav-toggle"><span></span></a>');
        $jQMainMenu.prepend($toggle);
    }());

    /* Add Toggle */
    jQuery('.nav-toggle').on('click', function(e) {
        e.preventDefault();
        $jQBody.toggleClass('has--opened-main-menu');
    });

    /* Insert toggle buttons */
    $jQMainMenu.children().each(function() {
        var $this = jQuery(this);
        if (!$this.hasClass('menu-item-has-children')) {
            return;
        }
        var $button = jQuery('<button class="buttonreset btn-toggle" type="button"><span>&gt;</span></button>');
        $this.prepend($button);
        $button.on('click', function(e) {
            e.preventDefault();
            $this.toggleClass('is-active');
        });
    });
});
