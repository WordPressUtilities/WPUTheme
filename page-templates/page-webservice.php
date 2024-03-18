<?php
/* Template Name: Webservice */

include __DIR__ . '/../z-protect.php';

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
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            get_template_part('404');
        }
        die;
}
