<?php

/* ----------------------------------------------------------
  Oembed
---------------------------------------------------------- */

/**
 * Get oembed infos
 * Thanks to https://theshipyard.se/how-to-use-the-oembed-api-in-wordpress/
 * @param  string $url  URL to retrieve infos
 * @return object       oembed object
 */

function wputh_get_oembed_infos($url) {

    if (!$url || empty($url)) {
        return false;
    }

    $transient_id = 'wputh_oembed_cache_' . md5($url);
    if (false === ($infos = get_transient($transient_id))) {
        require_once ABSPATH . WPINC . '/class-wp-oembed.php';
        $oembed = new WP_oEmbed();
        $url = esc_url_raw($url);
        $provider = $oembed->get_provider($url);
        $infos = $oembed->fetch($provider, $url);

        /* Load biggest available thumbnail */
        $infos = wputh_get_oembed_infos__big_thumbnail($infos);

        set_transient($transient_id, $infos, MONTH_IN_SECONDS);
    }

    return $infos;

}

function wputh_get_oembed_infos__big_thumbnail($infos) {
    if (!is_object($infos)) {
        return $infos;
    }

    /* YouTube */
    if (strtolower($infos->provider_name) == 'youtube' && strpos($infos->thumbnail_url, 'hqdefault') !== false) {
        $resolution = array(
            'maxresdefault',
            'sddefault'
        );
        foreach ($resolution as $k) {
            $big_thumbnail = str_replace('hqdefault', $k, $infos->thumbnail_url);
            $big_thumbnail_resp_code = wp_remote_retrieve_response_code(wp_remote_head($big_thumbnail));
            if ($big_thumbnail_resp_code == 200) {
                $infos->thumbnail_url = $big_thumbnail;
                break;
            }
        }
    }

    /* Vimeo */
    if (strtolower($infos->provider_name) == 'vimeo') {
        $url = wp_parse_url($infos->thumbnail_url);
        $url_path_parts = explode('_', $url['path']);
        if (!isset($url_path_parts[1])) {
            $url_path_parts = array($url['path']);
        }
        $infos->thumbnail_url = $url['scheme'] . '://' . $url['host'] . $url_path_parts[0] . '_640';
    }

    return $infos;

}
