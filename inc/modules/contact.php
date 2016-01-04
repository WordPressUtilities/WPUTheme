<?php
class wputh__contact {
    function __construct() {
        if (apply_filters('disable_wputh_contact', false)) {
            return;
        }
        $this->set_options();
        add_action('template_redirect', array(&$this,
            'post_contact'
        ));
        add_action('wputh_contact_content', array(&$this,
            'page_content'
        ));

        if ($this->contact__settings['ajax_enabled']) {
            add_action('wp_ajax_wputh__contact', array(&$this,
                'ajax_action'
            ));
            add_action('wp_ajax_nopriv_wputh__contact', array(&$this,
                'ajax_action'
            ));
            add_action('wp_enqueue_scripts', array(&$this,
                'form_scripts'
            ));
        }
    }

    function form_scripts() {
        wp_enqueue_script('jquery-form');
        wp_enqueue_script('wputh-contact-form', get_template_directory_uri() . '/js/functions/contact-form.js', array(
            'jquery'
        ) , '1.0', true);

        // pass Ajax Url to script.js
        wp_localize_script('wputh-contact-form', 'ajaxurl', admin_url('admin-ajax.php'));
    }
    function set_options() {
        $this->has_upload = false;
        $this->contact__success = apply_filters('wputh_contact_success', '<p class="contact-success">' . __('Thank you for your message!', 'wputh') . '</p>');
        $this->default_field = array(
            'value' => '',
            'type' => 'text',
            'html_before' => '',
            'html_after' => '',
            'box_class' => '',
            'required' => 0,
            'datas' => array(
                __('No', 'wputh') ,
                __('Yes', 'wputh')
            ) ,
        );

        $this->contact__settings = apply_filters('wputh_contact_settings', array(
            'ul_class' => 'cssc-form cssc-form--default float-form',
            'box_class' => 'box',
            'submit_class' => 'cssc-button cssc-button--default',
            'submit_label' => __('Submit', 'wputh') ,
            'li_submit_class' => '',
            'ajax_enabled' => true,
            'file_types' => array(
                'image/png',
                'image/jpeg',
            ) ,
            'max_file_size' => 2 * 1024 * 1024,
            'attach_to_post' => get_the_ID() ,
        ));

        $this->contact_fields = apply_filters('wputh_contact_fields', array(
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

        // Testing missing settings
        foreach ($this->contact_fields as $id => $field) {

            if ($field['type'] == 'file') {
                $this->has_upload = true;
            }

            // Merge with default field.
            $this->contact_fields[$id] = array_merge(array() , $this->default_field, $field);

            $this->contact_fields[$id]['id'] = $id;

            // Default label
            if (!isset($field['label'])) {
                $this->contact_fields[$id]['label'] = ucfirst(str_replace('contact_', '', $id));
            }

            if (!isset($field['datas']) || !is_array($field['datas'])) {
                $this->contact_fields[$id]['datas'] = $this->default_field['datas'];
            }
        }

        $this->content_contact = '';
    }

    function page_content($hide_wrapper = false) {

        // Display contact form
        $this->content_contact.= '<form class="wputh__contact__form" action="" method="post" ' . ($this->has_upload ? 'enctype="multipart/form-data' : '') . '"><ul class="' . $this->contact__settings['ul_class'] . '">';
        foreach ($this->contact_fields as $id => $field) {
            $field_id_name = 'id="' . $id . '" name="' . $id . '"';
            if ($field['required']) {
                $field_id_name.= ' required="required"';
            }
            $field_val = 'value="' . $field['value'] . '"';
            $this->content_contact.= $field['html_before'] . '<li class="' . $this->contact__settings['box_class'] . ' ' . $field['box_class'] . '">';
            if (isset($field['label'])) {
                $this->content_contact.= '<label for="' . $id . '">' . $field['label'] . '</label>';
            }
            switch ($field['type']) {
                case 'select':
                    $this->content_contact.= '<select  ' . $field_id_name . '>';
                    $this->content_contact.= '<option value="" disabled selected style="display:none;">' . __('Select a value') . '</option>';
                    foreach ($field['datas'] as $key => $val) {
                        $this->content_contact.= '<option ' . (!empty($field['value']) && $field['value'] == $key ? 'selected="selected"' : '') . ' value="' . esc_attr($key) . '">' . $val . '</option>';
                    }
                    $this->content_contact.= '</select>';
                break;
                case 'text':
                case 'url':
                case 'file':
                case 'email':
                    $this->content_contact.= '<input type="' . $field['type'] . '" ' . $field_id_name . ' ' . $field_val . ' />';
                break;
                case 'textarea':
                    $this->content_contact.= '<textarea cols="30" rows="5" ' . $field_id_name . '>' . $field['value'] . '</textarea>';
                break;
            }
            $this->content_contact.= '</li>' . $field['html_after'];
        }

        /* Quick honeypot */
        $this->content_contact.= '<li class="screen-reader-text">';
        $this->content_contact.= '<label>If you are a human, leave this empty</label>';
        $this->content_contact.= '<input tabindex="-1" name="hu-man-te-st" type="text"/>';
        $this->content_contact.= '</li>';

        $this->content_contact.= '<li class="' . $this->contact__settings['li_submit_class'] . '">
        <input type="hidden" name="control_stripslashes" value="&quot;" />
        <input type="hidden" name="wputh_contact_send" value="1" />
        <input type="hidden" name="action" value="wputh__contact" />
        <button class="' . $this->contact__settings['submit_class'] . '" type="submit">' . $this->contact__settings['submit_label'] . '</button>
        </li>';
        $this->content_contact.= '</ul></form>';
        if ($hide_wrapper !== true) {
            echo '<div class="wputh-contact-form-wrapper">';
        }
        echo $this->content_contact;
        if ($hide_wrapper !== true) {
            echo '</div>';
        }
    }

    function post_contact() {

        // Checking before post
        if (empty($_POST) || !isset($_POST['wputh_contact_send'])) {
            return;
        }

        // Initial settings
        $this->msg_errors = array();
        $msg_success = '';

        // Checking for PHP Conf
        if (isset($_POST['control_stripslashes']) && $_POST['control_stripslashes'] == '\"') {
            foreach ($_POST as $id => $field) {
                $_POST[$id] = stripslashes($field);
            }
        }

        // Checking bots
        if (!isset($_POST['hu-man-te-st']) || !empty($_POST['hu-man-te-st'])) {
            return;
        }

        $this->contact_fields = $this->extract_value_from_post($_POST, $this->contact_fields);

        if (isset($this->contact_fields['contact_message'])) {
            $contact_message = apply_filters('wputh_contact_message', $this->contact_fields['contact_message']['value']);
            if (is_array($contact_message)) {
                foreach ($contact_message as $msg) {
                    $this->msg_errors[] = $msg;
                }
            }
            else {
                $this->contact_fields['contact_message']['value'] = $contact_message;
            }
        }

        if (empty($this->msg_errors)) {

            // Setting success message
            $this->content_contact.= $this->contact__success;
            $attachments_to_destroy = array();
            $this->more = array(
                'attachments' => array()
            );

            // Send mail
            $mail_content = '<p>' . __('Message from your contact form', 'wputh') . '</p>';

            foreach ($this->contact_fields as $id => $field) {

                if ($field['type'] == 'file') {

                    // Store attachment id
                    $attachments_to_destroy[] = $this->contact_fields[$id]['value'];

                    // Add to mail attachments
                    $this->more['attachments'][] = get_attached_file($this->contact_fields[$id]['value']);
                    $this->contact_fields[$id]['value'] = '';
                    continue;
                }

                // Emptying values
                $mail_content.= '<hr /><p><strong>' . $field['label'] . '</strong>:<br />' . $field['value'] . '</p>';
                $this->contact_fields[$id]['value'] = '';
            }

            wputh_sendmail(get_option('admin_email') , __('Message from your contact form', 'wputh') , $mail_content, $this->more);

            // Delete temporary attachments
            foreach ($attachments_to_destroy as $att_id) {
                wp_delete_attachment($att_id);
            }
        }
        else {
            $this->content_contact.= '<p class="contact-error"><strong>' . __('Error:', 'wputh') . '</strong><br />' . implode('<br />', $this->msg_errors) . '</p>';
        }
    }

    function extract_value_from_post($post, $contact_fields) {
        foreach ($contact_fields as $id => $field) {

            $tmp_value = '';
            if (isset($post[$id])) {
                $tmp_value = trim(htmlentities(strip_tags($post[$id])));
            }

            if ($field['type'] == 'file') {
                if (isset($_FILES[$id]) && $_FILES[$id]['error'] == 0) {
                    $tmp_value = $_FILES[$id]['tmp_name'];
                }
            }

            if ($tmp_value != '') {
                if ($field['type'] == 'file') {
                    $field_ok = $this->validate_field_file($_FILES[$id], $field);
                }
                else {
                    $field_ok = $this->validate_field($tmp_value, $field);
                }

                if (!$field_ok) {
                    $this->msg_errors[] = sprintf(__('The field "%s" is not correct', 'wputh') , $field['label']);
                }
                else {

                    if ($field['type'] == 'select') {
                        $tmp_value = $field['datas'][$tmp_value];
                    }

                    if ($field['type'] == 'file') {
                        $tmp_value = $this->upload_file_return_att_id($_FILES[$id], $field);
                    }

                    $contact_fields[$id]['value'] = $tmp_value;
                }
            }
            else {
                if ($field['required']) {
                    $this->msg_errors[] = sprintf(__('The field "%s" is required', 'wputh') , $field['label']);
                }
            }
        }
        return $contact_fields;
    }

    function upload_file_return_att_id($file, $field) {

        require_once (ABSPATH . 'wp-admin/includes/image.php');
        require_once (ABSPATH . 'wp-admin/includes/file.php');
        require_once (ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload($field['id'], $this->contact__settings['attach_to_post']);

        if (is_wp_error($attachment_id)) {
            return false;
        }
        else {
            return $attachment_id;
        }
    }

    function validate_field_file($file, $field) {
        $field_ok = true;

        // Max size
        if ($file['size'] >= $this->contact__settings['max_file_size']) {
            $field_ok = false;
        }

        // Type
        if (!in_array($file['type'], $this->contact__settings['file_types'])) {
            $field_ok = false;
        }

        return $field_ok;
    }

    function validate_field($tmp_value, $field) {
        $field_ok = true;

        switch ($field['type']) {
            case 'select':
                $field_ok = array_key_exists($tmp_value, $field['datas']);
            break;
            case 'email':
                $field_ok = filter_var($tmp_value, FILTER_VALIDATE_EMAIL) !== false;
            break;
            case 'url':
                $field_ok = filter_var($tmp_value, FILTER_VALIDATE_URL) !== false;
            break;
        }

        return $field_ok;
    }

    function ajax_action() {
        $this->post_contact();
        $this->page_content(true);
        die;
    }
}

add_action('init', 'launch_wputh__contact');
function launch_wputh__contact() {
    new wputh__contact();
}

/* ----------------------------------------------------------
  Shortcode for form
---------------------------------------------------------- */

add_shortcode('wputh_contact_form', 'wputh__contact__content');
function wputh__contact__content() {
    do_action('wputh_contact_content');
}

/* ----------------------------------------------------------
  Antispam
---------------------------------------------------------- */

/* Count number of links
 -------------------------- */

add_filter('wputh_contact_message', 'wputh_contact_message___maxlinks', 10, 1);
function wputh_contact_message___maxlinks($message) {
    $maxlinks_nb = apply_filters('wputh_contact_message___maxlinks__nb', 5);
    $http_count = substr_count($message, 'http');
    if ($http_count > $maxlinks_nb) {
        return array(
            sprintf(__('No more than %s links, please', 'wputh') , $maxlinks_nb)
        );
    }

    return $message;
}
