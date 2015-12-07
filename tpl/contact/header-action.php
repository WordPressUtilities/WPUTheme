<?php
$content_contact = '';

$contact__success = apply_filters('wputh_contact_success', '<p class="contact-success">' . __('Thank you for your message!', 'wputh') . '</p>');

$contact__settings = apply_filters('wputh_contact_settings', array(
    'ul_class' => 'cssc-form cssc-form--default float-form',
    'box_class' => 'box',
    'submit_class' => 'cssc-button cssc-button--default',
    'submit_label' => __('Submit', 'wputh') ,
    'li_submit_class' => ''
));

$contact_fields = apply_filters('wputh_contact_fields', array(
    'contact_name' => array(
        'label' => __('Name', 'wputh') ,
        'required' => 1
    ) ,
    'contact_email' => array(
        'label' => __('Email', 'wputh') ,
        'type' => 'email',
        'required' => 1
    ) ,
    'contact_message' => array(
        'label' => __('Message', 'wputh') ,
        'type' => 'textarea',
        'required' => 1
    ) ,
));

$default_field = array(
    'value' => '',
    'type' => 'text',
    'html_before' => '',
    'html_after' => '',
    'required' => 0,
);

// Testing missing settings
foreach ($contact_fields as $id => $field) {

    // Merge with default field.
    $contact_fields[$id] = array_merge(array() , $default_field, $field);

    // Default label
    if (!isset($field['label'])) {
        $contact_fields[$id]['label'] = ucfirst(str_replace('contact_', '', $id));
    }
}

// Checking before post
if (!empty($_POST)) {

    // Initial settings
    $msg_errors = array();
    $msg_success = '';

    // Checking for PHP Conf
    if (isset($_POST['control_stripslashes']) && $_POST['control_stripslashes'] == '\"') {
        foreach ($_POST as $id => $field) {
            $_POST[$id] = stripslashes($field);
        }
    }

    foreach ($contact_fields as $id => $field) {

        if (isset($_POST[$id]) && !empty($_POST[$id])) {
            $tmp_value = htmlentities(strip_tags($_POST[$id]));

            $field_ok = true;

            // Testing fields
            switch ($field['type']):
            case 'email':
                $field_ok = filter_var($tmp_value, FILTER_VALIDATE_EMAIL) !== false;
            break;
            case 'url':
                $field_ok = filter_var($tmp_value, FILTER_VALIDATE_URL) !== false;
            break;
            endswitch;

            if (!$field_ok) {
                $msg_errors[] = sprintf(__('The field "%s" is not correct', 'wputh') , $field['label']);
            }
            else {
                $contact_fields[$id]['value'] = $tmp_value;
            }
        }
        else {
            if ($field['required']) {
                $msg_errors[] = sprintf(__('The field "%s" is required', 'wputh') , $field['label']);
            }
        }
    }

    if (empty($msg_errors)) {

        // Setting success message
        $content_contact.= $contact__success;

        // Send mail
        $mail_content = '<p>' . __('Message from your contact form', 'wputh') . '</p>';

        foreach ($contact_fields as $id => $field) {

            // Emptying values
            $mail_content.= '<hr /><p><strong>' . $field['label'] . '</strong>:<br />' . $field['value'] . '</p>';
            $contact_fields[$id]['value'] = '';
        }

        wputh_sendmail(get_option('admin_email') , __('Message from your contact form', 'wputh') , $mail_content);
    }
    else {
        $content_contact.= '<p class="contact-error"><strong>' . __('Error:', 'wputh') . '</strong><br />' . implode('<br />', $msg_errors) . '</p>';
    }
}

// Display contact form
$content_contact.= '<form action="" method="post"><ul class="' . $contact__settings['ul_class'] . '">';
foreach ($contact_fields as $id => $field) {
    $field_id_name = 'id="' . $id . '" name="' . $id . '"';
    if ($field['required']) {
        $field_id_name.= ' required="required"';
    }
    $field_val = 'value="' . $field['value'] . '"';
    $content_contact.= $field['html_before'] . '<li class="' . $contact__settings['box_class'] . '">';
    if (isset($field['label'])) {
        $content_contact.= '<label for="' . $id . '">' . $field['label'] . '</label>';
    }
    switch ($field['type']) {
        case 'text':
        case 'url':
        case 'email':
            $content_contact.= '<input type="' . $field['type'] . '" ' . $field_id_name . ' ' . $field_val . ' />';
        break;
        case 'textarea':
            $content_contact.= '<textarea cols="30" rows="5" ' . $field_id_name . '>' . $field['value'] . '</textarea>';
        break;
    }
    $content_contact.= '</li>' . $field['html_after'];
}
$content_contact.= '<li class="' . $contact__settings['li_submit_class'] . '">
<input type="hidden" name="control_stripslashes" value="&quot;" />
<button class="' . $contact__settings['submit_class'] . '" type="submit">' . $contact__settings['submit_label'] . '</button>
</li>';
$content_contact.= '</ul></form>';
