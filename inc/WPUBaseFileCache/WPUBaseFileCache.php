<?php
namespace WPUTheme;

/*
Class Name: WPU Base File Cache
Description: A class to handle basic file cache
Version: 0.4.0
Author: Darklg
Author URI: https://darklg.me/
License: MIT License
License URI: https://opensource.org/licenses/MIT
*/

defined('ABSPATH') || die;

class WPUBaseFileCache {
    private $cache_dir = '';
    private $settings = array();
    private $default_args = array(
        'autopurge_delay' => 0
    );
    private $excluded_files = array('.', '..', '.htaccess', 'index.html');
    public function __construct($cache_dir = '', $args = array()) {
        if (!$cache_dir) {
            $cache_dir = __NAMESPACE__;
        }
        $this->cache_dir = $cache_dir;
        $this->settings = array_merge($this->default_args, is_array($args) ? $args : array());
        $this->get_cache_dir();
        $this->maybe_autopurge();
    }

    public function maybe_autopurge() {
        $transient_key = 'wpu_filecache_purge_' . sanitize_key($this->cache_dir);
        if (get_transient($transient_key)) {
            return;
        }
        set_transient($transient_key, 1, HOUR_IN_SECONDS);
        $this->autopurge();
    }

    public function autopurge() {
        $cache_dir = $this->get_cache_dir();
        $threshold = time() - $this->settings['autopurge_delay'];
        if ($handle = opendir($cache_dir)) {
            while (false !== ($file = readdir($handle))) {
                if (in_array($file, $this->excluded_files) || is_dir($cache_dir . DIRECTORY_SEPARATOR . $file)) {
                    continue;
                }
                if (filemtime($cache_dir . DIRECTORY_SEPARATOR . $file) < $threshold) {
                    unlink($cache_dir . DIRECTORY_SEPARATOR . $file);
                }
            }
            closedir($handle);
        }
    }

    public function get_cache_dir() {
        $root_cache_dir = WP_CONTENT_DIR . '/cache';
        if (!is_dir($root_cache_dir) && !mkdir($root_cache_dir)) {
            return false;
        }
        if (is_multisite()) {
            $root_cache_dir .= '/site_' . get_current_blog_id();
            if (!is_dir($root_cache_dir) && !mkdir($root_cache_dir)) {
                return false;
            }
        }
        $cache_dir = $root_cache_dir . '/' . $this->cache_dir . '/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir);
            chmod($cache_dir, 0775);
            $this->file_put_contents($cache_dir . 'index.html', '');
            $this->file_put_contents($cache_dir . '.htaccess', 'deny from all');
        }
        return $cache_dir;
    }

    public function purge_cache_dir() {
        $upload_dir = $this->get_cache_dir();
        if ($handle = opendir($upload_dir)) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $this->excluded_files) && !is_dir($upload_dir . DIRECTORY_SEPARATOR . $file)) {
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
        $this->file_put_contents($cached_file, serialize($content));

        return true;
    }

    /**
     * Writes content to a file and sets the file permissions.
     */
    public function file_put_contents($file, $content = '') {
        file_put_contents($file, $content);
        chmod($file, 0664);
    }

}
