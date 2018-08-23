<?php

/* ----------------------------------------------------------
  Gallery template
---------------------------------------------------------- */

add_filter('wputh_post_metas_boxes', 'wputh_tpl_gallery__post_metas_boxes', 10, 3);
function wputh_tpl_gallery__post_metas_boxes($boxes) {
    $boxes['box_tpl_gallery'] = array(
        'name' => 'Gallery',
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
    $fields['wputh_gallery_order'] = array(
        'box' => 'box_tpl_gallery',
        'name' => 'Order',
        'type' => 'select',
        'datas' => array(
            'DESC' => 'DESC',
            'ASC' => 'ASC'
        )
    );
    $fields['wputh_gallery_orderby'] = array(
        'box' => 'box_tpl_gallery',
        'name' => 'Order by',
        'type' => 'select',
        'datas' => array(
            'id' => 'ID',
            'title' => 'Title',
            'rand' => 'Rand'
        )
    );
    $fields['wputh_gallery_backgroundmethod'] = array(
        'box' => 'box_tpl_gallery',
        'name' => 'Background',
        'type' => 'select',
        'datas' => array(
            'background' => 'Classic',
            'lazy' => 'Lazy Loading'
        )
    );
    return $fields;
}
