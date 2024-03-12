<?php
require_once dirname(__FILE__) . '/z-protect.php';
if (!defined('IS_AJAX') || !IS_AJAX) {

    /* ----------------------------------------------------------
      HEAD
    ---------------------------------------------------------- */

    do_action('qm/start', 'wputheme_wp_head');
    echo '<head>';
    wp_head();
    echo '</head>';
    do_action('qm/stop', 'wputheme_wp_head');

    /* ----------------------------------------------------------
      BODY
    ---------------------------------------------------------- */

    /* BODY CLASS */
    do_action('qm/start', 'wputheme_body_class');
    echo '<body ';
    body_class('cssc-is-responsive');
    echo '>';
    do_action('qm/stop', 'wputheme_body_class');

    /* BODY OPEN */
    do_action('qm/start', 'wputheme_body_open');
    wp_body_open();
    do_action('qm/stop', 'wputheme_body_open');

    /* ----------------------------------------------------------
      HEADER
    ---------------------------------------------------------- */

    do_action('qm/start', 'wputheme_header');
    do_action('wputheme_header_items');

    /* HEADER BANNER */
    if (apply_filters('wputheme_display_header', true)):
        echo '<div class="main-header centered-container">';
        echo '<header class="banner" id="banner">';
        do_action('wputheme_header_banner');
        echo '</header>';
        echo '</div>';
    endif;

    /* MAIN WRAPPER */
    do_action('wputheme_header_elements');
    if (apply_filters('wputheme_display_mainwrapper', true)):
        echo '<div class="main-container centered-container"><div class="main-container--inner" id="content">';
    else:
        if (apply_filters('wputheme_display_mainwrapper__content', true)):
            echo '<div id="content">';
        endif;
    endif;

    /* OVER CONTENT */
    do_action('wputheme_main_overcontent');
    if (apply_filters('wputheme_display_skiplinks', true)) {
        echo '<div id="maincontent"></div>';
    }
    do_action('qm/stop', 'wputheme_header');
}
do_action('wputheme_main_overcontent_inajax');
do_action('qm/start', 'wputheme_content');
