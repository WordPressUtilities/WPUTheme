<?php

/* ----------------------------------------------------------
  Setup
---------------------------------------------------------- */

function wputh_setup_pages_site($pages) {

    foreach ($pages as $id => $page) {
        if (!isset($page['constant'])) {
            $pages[$id]['constant'] = strtoupper($id);
        }
        if (!isset($page['post_title'])) {
            $pages[$id]['post_title'] = ucwords(str_replace('__page_id', '', $id));
        }
        if (!isset($page['post_content'])) {
            $pages[$id]['post_content'] = '';
        }
        if (!isset($page['post_status'])) {
            $pages[$id]['post_status'] = 'publish';
        }
        if (!isset($page['post_type'])) {
            $pages[$id]['post_type'] = 'page';
        }
        if (!isset($page['disable_items']) || !is_array($page['disable_items'])) {
            $pages[$id]['disable_items'] = array();
        }
        if (!isset($page['wpu_post_metas']) || !is_array($page['wpu_post_metas'])) {
            $pages[$id]['wpu_post_metas'] = array();
        }
    }

    return $pages;
}

/* ----------------------------------------------------------
  Page creation
---------------------------------------------------------- */

function wputh_pages_site_setup() {
    /* Avoid double launch */
    if (defined('WPUTH_PAGES_SITE_SETUP_LAUNCHED')) {
        return;
    }
    define('WPUTH_PAGES_SITE_SETUP_LAUNCHED', '1');

    /* Get admin users */
    $admin_user = false;
    $adminusers = get_users(array(
        'fields' => 'ids',
        'role__in' => array('administrator')
    ));
    if (isset($adminusers[0]) && is_numeric($adminusers[0])) {
        $admin_user = intval($adminusers[0], 10);
    }

    // Creating pages
    $pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));
    foreach ($pages_site as $id => $page) {
        $option_page = get_option($id);

        /* If page should not be created */
        if (isset($page['prevent_creation'])) {
            continue;
        }

        /* Set author */
        if (!isset($page['post_author']) && $admin_user) {
            $page['post_author'] = $admin_user;
        }

        /* Check if page exists */
        if (is_numeric($option_page) && get_permalink($option_page)) {
            continue;
        }

        /* Default content : try to load template */
        if (empty($page['post_content'])) {
            $file_name = __DIR__ . '/activation/' . str_replace('__page_id', '', $id) . '.php';
            ob_start();
            locate_template($file_name, 1);
            $page['post_content'] = ob_get_clean();
        }

        /* Create page */
        $option_page = wp_insert_post($page);
        if (is_numeric($option_page)) {
            update_option($id, $option_page);
            /* Add optional post metas */
            foreach ($page['wpu_post_metas'] as $_key => $_var) {
                add_post_meta($option_page, $_key, $_var);
            }
        }
    }
}

add_action('init', 'wputh_setup_pages_init');
function wputh_setup_pages_init() {
    $opt_name = 'wputh_pages_site_setup_hash';
    $pages_site = md5(json_encode(wputh_setup_pages_site(apply_filters('wputh_pages_site', array()))));

    if (get_option($opt_name) != $pages_site || !get_transient('wputh_pages_site_setup')) {
        set_transient('wputh_pages_site_setup', 1, 60 * 10);
        update_option('wputh_pages_site_setup_hash', $pages_site, true);
        wputh_pages_site_setup();
    }
}

add_action('wp_head', 'wputh_setup_pages_wp_head');
function wputh_setup_pages_wp_head() {
    $pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));
    $displayed_values = array();
    foreach ($pages_site as $id => $page) {
        if (isset($page['load_url_js']) && $page['load_url_js']) {
            $displayed_values[str_replace('__page_id', '', $id)] = get_page_link(get_option($id));
        }
    }
    if (!$displayed_values) {
        return;
    }
    echo '<script>var wputh_pages_list=' . json_encode($displayed_values) . '</script>';
}

/* ----------------------------------------------------------
  Pages IDs
---------------------------------------------------------- */

if (!function_exists('wputh_set_pages_site')) {
    function wputh_set_pages_site($pages_site) {
        $pages_site['about__page_id'] = array(
            'constant' => 'ABOUT__PAGE_ID',
            'post_title' => 'A Propos',
            'post_content' => '<p>A Propos de ce site.</p>'
        );
        $pages_site['mentions__page_id'] = array(
            'constant' => 'MENTIONS__PAGE_ID',
            'post_title' => 'Mentions légales'
        );
        return $pages_site;
    }
}

add_filter('wputh_pages_site', 'wputh_set_pages_site');

$pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));
foreach ($pages_site as $id => $p) {
    define($p['constant'], get_option($id));
}

/* ----------------------------------------------------------
  Hide editor & items on page admin
---------------------------------------------------------- */

/* http://stackoverflow.com/a/12219456 */
add_action('admin_init', 'wputh_pages_hide_editor');
function wputh_pages_hide_editor() {
    $post_id = false;
    // Get the Post ID.
    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } else if (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    }

    if (empty($post_id) || !is_numeric($post_id)) {
        return;
    }

    $pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));
    $page_template = get_post_meta($post_id, '_wp_page_template', 1);
    foreach ($pages_site as $id => $page) {
        $page_option_id = get_option($id);
        if (!empty($page['disable_items']) && ($post_id == $page_option_id || (isset($page['page_template']) && $page_template == $page['page_template']))) {
            foreach ($page['disable_items'] as $item) {
                switch ($item) {
                case 'editor':
                    add_action('admin_footer', 'wputh_pages_hide_editor_css');
                    break;
                case 'custom-fields':
                case 'revisions':
                case 'thumbnail':
                    remove_post_type_support('page', $item);
                    break;
                default:
                }
            }
        }
    }
}

function wputh_pages_hide_editor_css() {
    $css = '<style type="text/css">';
    $css .= '#wp-content-editor-container, #post-status-info, #post-body-content .wp-switch-editor, .editor-block-list__layout { display: none; }';
    $css .= '.wp-editor-expand + .qtranxs-lang-switch-wrap {display: none;}';
    $css .= '.wp-editor-expand #wp-content-editor-tools {border-bottom: 0;}';
    $css .= '</style>';
    echo $css;
}

/* ----------------------------------------------------------
  Privacy policy
---------------------------------------------------------- */

function wputh_pages_set_privacy_policy() {

    // Kill if already defined
    $privacy_policy_page_id = get_option('wp_page_for_privacy_policy');
    if (is_numeric($privacy_policy_page_id)) {
        return;
    }

    // Load content class
    if (!class_exists('WP_Privacy_Policy_Content')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
    }

    // Create page
    $privacy_policy_page_content = WP_Privacy_Policy_Content::get_default_content();
    $privacy_policy_page_id = wp_insert_post(
        array(
            'post_title' => __('Privacy Policy'),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => $privacy_policy_page_content
        ),
        true
    );

    // If success : set as the new page.
    if (is_numeric($privacy_policy_page_id)) {
        update_option('wp_page_for_privacy_policy', $privacy_policy_page_id);
    }
}
