<?php
namespace WPUTheme;

/*
Class Name: WPU Base File Cache
Description: A class to handle basic file cache
Version: 0.1.2
Author: Darklg
Author URI: https://darklg.me/
License: MIT License
License URI: https://opensource.org/licenses/MIT
*/

defined('ABSPATH') || die;

class WPUBaseFileCache {
    private $cache_dir;
    public function __construct($cache_dir) {
        $this->cache_dir = $cache_dir;
        $this->get_cache_dir();
    }

    public function get_cache_dir() {
        $root_cache_dir = WP_CONTENT_DIR . '/cache';
        if (!is_dir($root_cache_dir) && !mkdir($root_cache_dir)) {
            return false;
        }
        $cache_dir = $root_cache_dir . '/' . $this->cache_dir . '/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir);
            file_put_contents($cache_dir . 'index.html', '');
            file_put_contents($cache_dir . '.htaccess', 'deny from all');
        }
        return $cache_dir;
    }

    public function purge_cache_dir() {
        $upload_dir = $this->get_cache_dir();
        if ($handle = opendir($upload_dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != ".htaccess" && !is_dir($upload_dir . DIRECTORY_SEPARATOR . $file)) {
                    unlink($upload_dir . DIRECTORY_SEPARATOR . $file);
                }
            }
            closedir($handle);
        }
    }

    public function get_cache($cache_id, $expiration = 3600) {
        $cached_file = $this->get_cache_dir() . $cache_id;
        if (!file_exists($cached_file)) {
            return false;
        }
        if ($expiration && filemtime($cached_file) + $expiration < time()) {
            return false;
        }

        return unserialize(file_get_contents($cached_file));
    }

    public function set_cache($cache_id, $content = '') {
        $cached_file = $this->get_cache_dir() . '/' . $cache_id;
        file_put_contents($cached_file, serialize($content));

        return true;
    }

}
