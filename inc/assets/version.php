<?php

/**
 * Use uploads/version.txt content as the assets version.
 */
add_filter('wputh_assets_version', 'wputh_set_assets_version', 10, 1);
if (!function_exists('wputh_set_assets_version')) {
    function wputh_set_assets_version($version) {
        $upload_dir = wp_upload_dir();
        $assets_version_file = $upload_dir['basedir'] . '/version.txt';
        if (file_exists($assets_version_file)) {
            $version_txt = trim(file_get_contents($assets_version_file));
            if(!empty($version_txt)){
                return $version_txt;
            }
        }
        return $version;
    }
}
