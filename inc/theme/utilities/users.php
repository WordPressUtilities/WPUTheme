<?php

/* ----------------------------------------------------------
  Get all user values for a meta
---------------------------------------------------------- */

function wputh_get_all_users_values_for($meta_key = '') {
    global $wpdb;
    $_users = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s", $meta_key));
    $users = array();
    foreach ($_users as $_user) {
        $users[$_user->user_id] = $_user->meta_value;
    }
    return $users;
}
