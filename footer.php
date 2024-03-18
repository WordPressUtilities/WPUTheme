<?php
include __DIR__ . '/z-protect.php';
do_action('qm/stop', 'wputheme_content');
do_action('qm/start', 'wputheme_footer');

/* AJAX CHECK */
do_action('wputheme_main_undercontent_inajax');
if (defined('IS_AJAX') && IS_AJAX) {
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    echo "<script>if(typeof jQuery !== 'function'){window.location='" . $current_url . "';}</script>";
    return;
}

/* WRAPPER */
do_action('wputheme_main_undercontent');
if (apply_filters('wputheme_display_mainwrapper', true)) {
    echo '</div></div>';
} else {
    if (apply_filters('wputheme_display_mainwrapper__content', true)):
        echo '</div>';
    endif;
}

/* FOOTER ELEMENTS */
do_action('wputheme_footer_elements');
if (apply_filters('wputheme_display_footer', true)) {
    get_template_part('tpl/footer/copyright');
}
do_action('qm/stop', 'wputheme_footer');

/* WP FOOTER */
do_action('qm/start', 'wputheme_wp_footer');
wp_footer();
do_action('qm/stop', 'wputheme_wp_footer');

/* END WRAPPER */
echo '</body>';
echo '</html>';
