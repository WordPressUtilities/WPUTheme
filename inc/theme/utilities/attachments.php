<?php

/**
 * Get Thumbnail URL
 *
 * @param string  $format
 * @return string
 */
function wputh_get_thumbnail_url($format) {
    global $post;
    $returnUrl = get_template_directory_uri() . '/images/thumbnails/' . $format . '.jpg';
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $format);
    return $image ? $image : $returnUrl;
}

/**
 * Get Thumbnail URL with specific parameters
 *
 * @param  int $id                       Post ID
 * @param  mixed (string|array) $format  Thumb format
 * @param  boolean $crop                 Crop to format
 * @return string                        Image URL
 */
function wputh_get_thumb_url($id = false, $format = 'thumbnail', $crop = false) {
    if (!$id) {
        $id = get_the_ID();
    }
    $format_txt = $format;
    if (is_array($format)) {
        $format_txt = implode('x', $format);
    }
    $returnUrl = get_theme_file_uri('/assets/images/thumbnails/' . $format_txt . '.jpg');
    $thumb_id = get_post_thumbnail_id($id);
    if (!$thumb_id) {
        return $returnUrl;
    }
    $image = wputh_get_attachment_image_src($thumb_id, $format, $crop);
    return $image ? $image : $returnUrl;
}

/**
 * Get an attachment URL and dynamically create intermediate sizes if needed
 * @param  int $id        ID of the attachment
 * @param  mixed $format  ID of an image format, or array of dimensions.
 * @return mixed          URL if success, false if not found
 */
function wputh_get_attachment_image_src($id, $format = 'thumbnail', $crop = false, $image_quality = false) {
    $cache_duration = 60 * 60;
    $cache_id = 'wputhattimgsrc_' . $id;
    $cache_id .= '_' . (is_array($format) ? md5(json_encode($format)) : $format);
    $cache_id .= '_' . ($crop ? 1 : 0);
    $cache_id .= '_' . ($image_quality ? $image_quality : 0);

    // GET CACHED VALUE
    $result = wp_cache_get($cache_id);
    if ($result !== false) {
        return $result;
    }
    $image = wp_get_attachment_image_src($id, $format);

    /* If format is an array of sizes : generate an intermediate size */
    if (is_array($format) && isset($image[0])) {
        $upload_dir = wp_get_upload_dir();
        $base_image_path = wp_get_attachment_image_src($id, 'thumbnail');

        /* Get thumbnail path */
        $thumbnail_dimensions = $base_image_path[1] . 'x' . $base_image_path[2];
        $new_dimensions = $format[0] . 'x' . $format[1];
        $source_image = $base_image_path[0];
        $new_image = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $source_image);
        $new_image = str_replace($thumbnail_dimensions, $new_dimensions, $new_image);
        $new_image_source = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_image);

        /* If file exists : return URL */
        if ($new_image_source != $source_image && file_exists($new_image)) {
            wp_cache_set($cache_id, $new_image_source, '', $cache_duration);
            return $new_image_source;
        }

        /* Resize image and return */
        $image = wp_get_image_editor(get_attached_file($id));
        $new_img_path = false;
        if (!is_wp_error($image)) {
            if (isset($image_quality)) {
                $image->set_quality($image_quality);
            }
            $image->resize($format[0], $format[1], $crop);
            $new_img_path = $image->generate_filename();
            $image->save($new_img_path);
        }
        if (file_exists($new_img_path)) {
            $image_src = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_img_path);
            wp_cache_set($cache_id, $image_src, '', $cache_duration);
            return $image_src;
        }
    }

    if (!is_wp_error($image) && is_array($image) && isset($image[0])) {
        wp_cache_set($cache_id, $image[0], '', $cache_duration);
        return $image[0];
    }

    wp_cache_set($cache_id, '', '', $cache_duration);

    return false;
}

/**
 * Get attachments - images
 *
 * @param int     $postID
 * @param string  $format (optional)
 * @return array
 */
function wputh_get_attachments_images($postID = false, $format = 'medium', $settings = array()) {
    global $post;
    if ($postID === false) {
        if (isset($post->ID)) {
            $postID = $post->ID;
        } else {
            return array();
        }
    }

    $default_settings = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_parent' => $postID
    );

    $args = array_merge($default_settings, $settings);

    $images = array();
    $attachments = get_posts($args);
    foreach ($attachments as $attachment) {
        $image = wp_get_attachment_image_src($attachment->ID, $format);
        if (isset($image[0])) {
            $images[$attachment->ID] = $image;
        }
    }
    return $images;
}

/**
 * Get attachment detail
 *
 * via http://wordpress.org/ideas/topic/functions-to-get-an-attachments-caption-title-alt-description
 *
 * @param int     $postID
 * @return array
 */
function wputh_get_attachment($attachment_id) {
    $attachment = get_post($attachment_id);
    return array(
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink($attachment->ID),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}

/* ----------------------------------------------------------
  SVG
---------------------------------------------------------- */

/**
 * Get SVG attachment image
 * @param  int    $image_id   Attachment ID
 * @param  string $image_size Image size if it could be a simple image
 * @return string             SVG Code or IMG Tag.
 */
function wputh_get_svg_attachment_image($image_id, $image_size = 'medium') {
    $image_src = wputh_get_svg_src(get_attached_file($image_id));
    if (!$image_src) {
        $image_src = wp_get_attachment_image($image_id, $image_size);
    }
    return $image_src;
}

/**
 * Get SVG code from a file
 * @param  [type] $image_path [description]
 * @return [type]             [description]
 */
function wputh_get_svg_src($image_path) {
    if (!$image_path || !file_exists($image_path)) {
        return '';
    }
    $image_src = '';
    $path_parts = pathinfo($image_path);
    if (isset($path_parts['extension']) && $path_parts['extension'] == 'svg') {
        $image_src = file_get_contents($image_path);
        /* Remove useless attributes */
        $image_src = preg_replace('/version="([0-9\.]*)"/isU', '', $image_src);
        $image_src = preg_replace('/xmlns\:([a-z]*)="([^"]*)"/isU', '', $image_src);
        /* Remove comments */
        $image_src = preg_replace('/<!--(.*)-->/isU', '', $image_src);
        $image_src = preg_replace('/<\?xml(.*)\?>/isU', '', $image_src);
        /* Remove scripts */
        $image_src = preg_replace('/<script(.*?)>(.*?)<\/script>/is', '', $image_src);
        $image_src = str_replace('onload=', 'data-onload=', $image_src);
        $image_src = str_replace('javascript:', 'data-javascript:', $image_src);

    }
    return $image_src;
}
