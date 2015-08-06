<?php
include dirname(__FILE__) . '/z-protect.php';
do_action('wputheme_main_undercontent_inajax');
if (defined('IS_AJAX') && IS_AJAX) {
    return;
}
do_action('wputheme_main_undercontent');
if (apply_filters('wputheme_display_mainwrapper', true)) {
    echo '</div></div>';
}
do_action('wputheme_footer_elements');
if (apply_filters('wputheme_display_footer', true)) {
    include get_template_directory() . '/tpl/footer/copyright.php';
}
wp_footer();
?>
</body>
</html>
