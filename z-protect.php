<?php
if (defined('TEMPLATEPATH') || defined('WPUTH_IS_Z_PROTECT')) {
    return;
}
define('WPUTH_IS_Z_PROTECT', true);

// Load base WordPress file
$bootfile = 'wp-load.php';
while (!is_file($bootfile)) {
    if (is_dir('..')) {
        chdir('..');
    } else {
        exit('Oups');
    }
}
require_once ($bootfile);

// Load 404
header( $_SERVER["SERVER_PROTOCOL"]." 404 Not Found" );
include get_404_template();
die;
