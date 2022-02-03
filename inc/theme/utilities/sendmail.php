<?php

/**
 * Send a preformated mail
 *
 * @param string  $address
 * @param string  $subject
 * @param string  $content
 */
function wputh_sendmail($address, $subject, $content, $more = array()) {

    // Set "more" default values values
    if (!is_array($more)) {
        $more = array();
    }
    $ids = array('headers', 'attachments', 'vars');
    foreach ($ids as $id) {
        if (!isset($more[$id]) || !is_array($more[$id])) {
            $more[$id] = array();
        }
    }
    if (!isset($more['model'])) {
        $more['model'] = '';
    }

    // Include headers
    $tpl_mail = get_template_directory() . '/tpl/mails/';
    $mail_content = '';
    if (file_exists($tpl_mail . 'header.php')) {
        ob_start();
        include $tpl_mail . 'header.php';
        $mail_content .= ob_get_clean();
    }

    $model = $tpl_mail . 'model-' . $more['model'] . '.php';
    if (!empty($more['model']) && file_exists($model)) {
        ob_start();
        include $model;
        $mail_content .= ob_get_clean();
    } else {
        $mail_content .= $content;
    }

    if (file_exists($tpl_mail . 'footer.php')) {
        ob_start();
        include $tpl_mail . 'footer.php';
        $mail_content .= ob_get_clean();
    }

    add_filter('wp_mail_content_type', 'wputh_sendmail_set_html_content_type');
    wp_mail($address, '[' . get_bloginfo('name') . '] ' . $subject, $mail_content, $more['headers'], $more['attachments']);
    // reset content-type to to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    remove_filter('wp_mail_content_type', 'wputh_sendmail_set_html_content_type');
}

function wputh_sendmail_set_html_content_type() {
    return 'text/html';
}
