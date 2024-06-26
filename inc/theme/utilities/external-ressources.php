<?php

/* ----------------------------------------------------------
  External ressources
---------------------------------------------------------- */

/* Metas
-------------------------- */

function wputh_get_cached_metas($url) {
    $wputheme_wpubasefilecache = wputheme_get_wpubasefilecache();

    $cache_id = 'wputh_get_cached_metas_' . md5($url);

    /* Valid metas */
    $cached_metas = $wputheme_wpubasefilecache->get_cache($cache_id, 0);
    if (is_array($cached_metas)) {
        return $cached_metas;
    }

    if (!class_exists('DOMDocument')) {
        return array();
    }

    /* Invalid response */
    $response = wp_remote_get($url);
    if (!$response) {
        return array();
    }

    /* Extract all metas */
    $cached_metas = array();
    $responseBody = wp_remote_retrieve_body($response);

    if ($responseBody) {
        $responseBody = preg_replace('/<body(.*?)<\/body>/isU', "<body></body>", $responseBody);
        $responseBody = preg_replace('/<script(.*)>([^>]*)<\/script>/isU', '', $responseBody);
        $responseBody = preg_replace('/<style(.*)>([^>]*)<\/style>/isU', '', $responseBody);
        $responseBody = preg_replace('/<link([^>]*)\/>/isU', '', $responseBody);
        $doc = new DOMDocument();
        $doc->loadHTML($responseBody);
        $meta = $doc->getElementsByTagName('meta');

        if (is_iterable($meta)) {
            foreach ($meta as $element) {
                $tag = array();
                foreach ($element->attributes as $node) {
                    $tag[$node->name] = $node->value;
                }
                $cached_metas[] = $tag;
            }
        }
    }

    /* Cache & return result */
    $wputheme_wpubasefilecache->set_cache($cache_id, $cached_metas);

    return $cached_metas;
}

/* File
-------------------------- */

function wputh_cache_get_external_file($url, $args = array()) {
    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
    if (!$extension) {
        $extension = 'file';
    }
    if (is_string($args) && strlen($args)) {
        $extension = $args;
    }

    if (!is_array($args)) {
        $args = array();
    }
    if (!isset($args['ext'])) {
        $args['ext'] = $extension;
    }

    if (!isset($args['max_age'])) {
        $args['max_age'] = 99 * YEAR_IN_SECONDS;
    }

    $upload_dir = wp_upload_dir();
    $filename = md5($url) . '.' . str_replace('.', '', $extension);
    $tmp_dir = $upload_dir['basedir'] . '/wputheme/';
    $tmp_url = $upload_dir['baseurl'] . '/wputheme/';
    $file_path = $tmp_dir . $filename;
    $file_url = $tmp_url . $filename;

    if (!is_dir($tmp_dir)) {
        mkdir($tmp_dir);
    }

    if (file_exists($file_path)) {
        $file_age = time() - filemtime($file_path);
        if ($file_age < $args['max_age']) {
            return $file_url;
        }
    }

    if (!function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $tmp_file = download_url($url);
    if (!is_wp_error($tmp_file)) {
        copy($tmp_file, $file_path);
        @unlink($tmp_file);
    } else {
        $file_url = $url;
    }

    return $file_url;
}
