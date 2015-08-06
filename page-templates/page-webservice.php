<?php
/* Template Name: Webservice */

include dirname(__FILE__) . '/../z-protect.php';

$mode = '';
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

switch ($mode) {
    case 'ajax_content':
        the_post();
        the_content();
    break;
    default:
        do_action('wputh_webservice', $_GET);
        if (!has_action('wputh_webservice')) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            include get_template_directory() . '/404.php';
        }
        die;
}
