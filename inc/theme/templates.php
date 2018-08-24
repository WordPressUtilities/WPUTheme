<?php

/* ----------------------------------------------------------
  Gallery template
---------------------------------------------------------- */

add_filter('wputh_post_metas_boxes', 'wputh_tpl_gallery__post_metas_boxes', 10, 3);
function wputh_tpl_gallery__post_metas_boxes($boxes) {
    $boxes['box_tpl_gallery'] = array(
        'name' => __('Gallery', 'wputh'),
        'context' => 'side',
        'post_type' => array('page'),
        'page_template' => array(
            'page-templates/page-gallery.php'
        )
    );
    return $boxes;
}

add_filter('wputh_post_metas_fields', 'wputh_tpl_gallery__post_metas_fields', 10, 3);
function wputh_tpl_gallery__post_metas_fields($fields) {
    $fields['wputh_gallery_orderby'] = array(
        'box' => 'box_tpl_gallery',
        'name' => __('Order by', 'wputh'),
        'type' => 'select',
        'datas' => array(
            'id' => 'ID',
            'title' => 'Title',
            'rand' => 'Rand'
        )
    );
    $fields['wputh_gallery_order'] = array(
        'box' => 'box_tpl_gallery',
        'name' => __('Order', 'wputh'),
        'type' => 'select',
        'datas' => array(
            'DESC' => 'DESC',
            'ASC' => 'ASC'
        )
    );
    $fields['wputh_gallery_backgroundmethod'] = array(
        'box' => 'box_tpl_gallery',
        'name' => __('Background', 'wputh'),
        'type' => 'select',
        'datas' => array(
            'background' => 'Classic',
            'lazy' => 'Lazy Loading'
        )
    );
    return $fields;
}

function wputh_gallery_get_attachments($post_id) {
    $order = get_post_meta($post_id, 'wputh_gallery_order', 1);
    if (!$order) {
        $order = 'DESC';
    }
    $orderby = get_post_meta($post_id, 'wputh_gallery_orderby', 1);
    if (!$orderby) {
        $orderby = 'ID';
    }
    return get_posts(array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => 'any',
        'post_mime_type' => 'image',
        'post_parent' => $post_id)
    );
}
