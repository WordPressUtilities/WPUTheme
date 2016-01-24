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
        if (!isset($page['disable_items'])) {
            $pages[$id]['disable_items'] = array();
        }
    }

    return $pages;
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
            'post_title' => 'Mentions légales',
            'post_content' => '<p>Contenu des mentions légales</p>'
        );
        return $pages_site;
    }
}

add_filter('wputh_pages_site', 'wputh_set_pages_site');

$pages_site = wputh_setup_pages_site(apply_filters('wputh_pages_site', array()));

foreach ($pages_site as $id => $page) {
    $opt_id = get_option($id);
    define($page['constant'], $opt_id);
    $link = '#';
    if (is_numeric($opt_id)) {
        $link = get_page_link($opt_id);
    }
    define($page['constant'] . '__LINK', $link);
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
    foreach ($pages_site as $id => $page) {
        if (!empty($page['disable_items']) && $post_id == get_option($id)) {
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
    $css .= '#wp-content-editor-container, #post-status-info, .wp-switch-editor { display: none; }';
    $css .= '.wp-editor-expand + .qtranxs-lang-switch-wrap {display: none;}';
    $css .= '.wp-editor-expand #wp-content-editor-tools {border-bottom: 0;}';
    $css .= '</style>';
    echo $css;
}
