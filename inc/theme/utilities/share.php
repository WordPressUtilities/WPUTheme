<?php

/* ----------------------------------------------------------
  Share methods
---------------------------------------------------------- */

function wputh_get_share_methods($item, $title = false, $permalink = false, $image = false) {

    if (!is_object($item)) {
        if (!is_numeric($item)) {
            return array();
        }
        $item = get_post($item);
    }

    $_title = $title;
    $_permalink = $permalink;
    if (is_object($item) && isset($item->post_type)) {
        $_title = get_the_title($item);
        $_permalink = get_permalink($item);
    }
    if (is_object($item) && isset($item->term_id)) {
        $_title = $item->name;
        $_permalink = get_term_link($item);
    }

    /* Title */
    $_title = apply_filters('the_title', $_title);
    if ($title !== false) {
        $_title = $title;
    }
    $_title = apply_filters('wputh_get_share_methods__title', $_title);
    $_title = trim(strip_tags(html_entity_decode($_title)));

    /* Permalink */
    if ($permalink !== false) {
        $_permalink = $permalink;
    }
    $_permalink = apply_filters('wputh_get_share_methods__permalink', $_permalink);

    /* Image */
    $_image = '';
    if (isset($item->ID) && has_post_thumbnail($item->ID)) {
        if (function_exists('wputhumb_get_thumbnail_url')) {
            $_image = urlencode(wputhumb_get_thumbnail_url('thumbnail', $item->ID));
        } else {
            $_image = wp_get_attachment_url(get_post_thumbnail_id($item->ID));
        }
    }
    if ($image !== false) {
        $_image = $image;
    }
    $_image = apply_filters('wputh_get_share_methods__image', $_image);

    /* Twitter */
    $_via_user = get_option('social_twitter_username');
    $_via_user = trim(str_replace('@', '', $_via_user));
    $_via = !empty($_via_user) ? ' via @' . $_via_user : '';
    $_twitter_text = get_option('social_twitter_share_text');
    if (!$_twitter_text) {
        $_twitter_text = $_title;
    }
    $_twitter_text = wputh_truncate($_twitter_text, 100 - strlen($_via));

    $_methods = array(
        'bluesky' => array(
            'name' => 'BlueSky',
            'url' => 'https://bsky.app/intent/compose?text=' . urlencode($_title . ' ' . $_permalink)
        ),
        'email' => array(
            'name' => 'Email',
            'url' => str_replace('+', '%20', 'mailto:?subject=' . urlencode($_title) . '&body=' . urlencode($_title . ' ' . $_permalink))
        ),
        'facebook' => array(
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($_permalink)
        ),
        'googleplus' => array(
            'name' => 'Google Plus',
            'url' => 'https://plus.google.com/share?url=' . urlencode($_permalink)
        ),
        'linkedin' => array(
            'name' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($_permalink)
        ),
        'pinterest' => array(
            'name' => 'Pinterest',
            'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($_permalink) . (!empty($_image) ? '&media=' . $_image : '') . '&description=' . urlencode($_title)
        ),
        'threads' => array(
            'name' => 'Threads',
            'url' => 'https://www.threads.net/intent/post?url=' . urlencode($_permalink) . '&text=' . urlencode($_title)
        ),
        'twitter' => array(
            'name' => 'X',
            'url' => 'https://x.com/intent/post?text=' . urlencode($_twitter_text . ' ' . $_permalink . $_via),
            'datas' => array(
                'via' => $_via_user
            )
        ),
        'whatsapp' => array(
            'name' => 'Whatsapp',
            'url' => 'whatsapp://send?text=' . urlencode($_title . ' ' . $_permalink)
        )
    );

    if (apply_filters('wputh_common_libraries__clipboard', false)) {
        $_methods['clipboard'] = array(
            'name' => __('Copy link', 'wputh'),
            'url' => $_permalink,
            'attributes' => array(
                'data-clipboard-text' => $_permalink
            )
        );
    }
    $_methods['print'] = array(
        'name' => __('Print', 'wputh'),
        'url' => '#',
        'attributes' => array(
            'onclick' => 'window.print();return false;'
        )
    );

    $_methods['sharesheet'] = array(
        'name' => __('Share link', 'wputh'),
        'url' => $_permalink,
        'attributes' => array(
            'data-share-title' => $_title,
            'data-share-url' => $_permalink
        )
    );

    foreach ($_methods as $_id => $_method) {
        if (!isset($_methods[$_id]['datas'])) {
            $_methods[$_id]['datas'] = array();
        }
        if (!isset($_methods[$_id]['attributes'])) {
            $_methods[$_id]['attributes'] = array();
        }
        $_methods[$_id]['datas']['permalink'] = $_permalink;
        $_methods[$_id]['datas']['title'] = $_title;
        $_methods[$_id]['datas']['image'] = $_image;
    }

    return apply_filters('wputheme_share_methods', $_methods, $_title, $_permalink, $_image, $_via_user);
}

/**
 * Get a HTML list of shared methods
 * @param  mixed   $post       post object or post ID
 * @param  string  $list_type  text or icon
 * @return string              HTML List
 */
function wputh_get_share_methods__list_html($post = false, $list_type = 'text', $template = '') {
    if (!$post) {
        $post = get_the_ID();
    }
    $_methods = wputh_get_share_methods($post);
    if (!$template) {
        $template = '<i aria-hidden="true" class="icon icon_%s"></i>';
    }
    $html = '';
    $html .= '<ul class="share-list">';
    foreach ($_methods as $_id => $_method) {
        $attributes = '';
        if ($_id == 'sharesheet') {
            $attributes .= ' style="display:none;"';
        }
        $html .= '<li data-share-method="' . $_id . '" ' . $attributes . '>';
        $html .= '<a rel="noreferrer noopener" target="_blank"';
        foreach ($_method['attributes'] as $key => $var) {
            $html .= ' ' . $key . '="' . esc_attr($var) . '"';
        }
        $html .= ' href="' . $_method['url'] . '" class="' . $_id . '">';
        switch ($list_type) {
        case 'custom':
            $html .= str_replace('%s', $_id, $template);
            break;
        case 'icon':
            $_icon_id = isset($_method['icon']) && $_method['icon'] ? $_method['icon'] : $_id;
            $html .= '<i aria-hidden="true" class="icon icon_' . esc_attr($_icon_id) . '"></i>';
            break;
        default:
            $html .= $_method['name'];
        }
        $html .= '</a>';
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
