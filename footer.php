<?php
include dirname(__FILE__) . '/z-protect.php';
if (IS_AJAX) {
    return;
}
if (apply_filters('wputheme_display_mainwrapper', true)) {
    echo '</div></div>';
}
if (apply_filters('wputheme_display_footer', true)) {
    include get_template_directory() . '/tpl/footer/copyright.php';
}
wp_footer();
?>
</body>
</html>
