<?php
/**
 * Utilities
 *
 * @package default
 */

/* ----------------------------------------------------------
  Load all utilities sections
---------------------------------------------------------- */

$_utilities_files = glob(get_template_directory() . '/inc/theme/utilities/*.php');
foreach ($_utilities_files as $filename) {
    require_once $filename;
}
