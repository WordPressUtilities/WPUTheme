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
    }

    function set_options() {
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
            'li_submit_class' => ''
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

            // Merge with default field.
            $this->contact_fields[$id] = array_merge(array() , $this->default_field, $field);

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

    function page_content() {

        // Display contact form
        $this->content_contact.= '<form action="" method="post"><ul class="' . $this->contact__settings['ul_class'] . '">';
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
        <button class="' . $this->contact__settings['submit_class'] . '" type="submit">' . $this->contact__settings['submit_label'] . '</button>
        </li>';
        $this->content_contact.= '</ul></form>';
        echo $this->content_contact;
    }

    function post_contact() {

        // Checking before post
        if (empty($_POST) || !isset($_POST['wputh_contact_send'])) {
            return;
        }

        // Initial settings
        $msg_errors = array();
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

        foreach ($this->contact_fields as $id => $field) {

            $tmp_value = '';
            if (isset($_POST[$id])) {
                $tmp_value = trim(htmlentities(strip_tags($_POST[$id])));
            }

            if ($tmp_value != '') {
                $field_ok = true;

                // Testing fields
                switch ($field['type']):
                case 'select':
                    $field_ok = array_key_exists($tmp_value, $field['datas']);
                break;
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

                    if ($field['type'] == 'select') {
                        $tmp_value = $field['datas'][$tmp_value];
                    }

                    $this->contact_fields[$id]['value'] = $tmp_value;
                }
            }
            else {
                if ($field['required']) {
                    $msg_errors[] = sprintf(__('The field "%s" is required', 'wputh') , $field['label']);
                }
            }
        }

        if (isset($this->contact_fields['contact_message'])) {
            $contact_message = apply_filters('wputh_contact_message', $this->contact_fields['contact_message']['value']);
            if (is_array($contact_message)) {
                foreach ($contact_message as $msg) {
                    $msg_errors[] = $msg;
                }
            }
            else {
                $this->contact_fields['contact_message']['value'] = $contact_message;
            }
        }

        if (empty($msg_errors)) {

            // Setting success message
            $this->content_contact.= $this->contact__success;

            // Send mail
            $mail_content = '<p>' . __('Message from your contact form', 'wputh') . '</p>';

            foreach ($this->contact_fields as $id => $field) {

                // Emptying values
                $mail_content.= '<hr /><p><strong>' . $field['label'] . '</strong>:<br />' . $field['value'] . '</p>';
                $this->contact_fields[$id]['value'] = '';
            }

            wputh_sendmail(get_option('admin_email') , __('Message from your contact form', 'wputh') , $mail_content);
        }
        else {
            $this->content_contact.= '<p class="contact-error"><strong>' . __('Error:', 'wputh') . '</strong><br />' . implode('<br />', $msg_errors) . '</p>';
        }
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
