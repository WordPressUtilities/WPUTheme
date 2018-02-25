<?php
if (defined('TEMPLATEPATH') || defined('WPUTH_IS_Z_PROTECT')) {
    return;
}
define('WPUTH_IS_Z_PROTECT', true);

// Load base WordPress file
$bootfile = 'wp-blog-header.php';
while (!is_file($bootfile)) {
    if (is_dir('..')) {
        if (!chdir('..')) {
            exit();
        }
    }
    else {
        exit('Oops');
    }
}

include ($bootfile);

// Redirect to home page for some files
if (isset($_SERVER['REQUEST_URI'])) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (isset($url['path'])) {
        $current_filename = basename($url['path']);
        $invalid_files = array(
            'comments.php',
            'functions.php',
            'header.php',
            'footer.php'
        );

        if (in_array($current_filename, $invalid_files)) {
            wp_redirect(home_url());
            die;
        }
    }
}

// Load 404
header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

$GLOBALS['wp_query']->set_404();
include get_404_template();
die;
