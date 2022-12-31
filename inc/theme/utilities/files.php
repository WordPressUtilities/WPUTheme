<?php

/* ----------------------------------------------------------
  Recursive glob
  Thanks to https://stackoverflow.com/a/17161106/975337
---------------------------------------------------------- */

function wputheme_rsearch($folder, $regPattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $regPattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}
