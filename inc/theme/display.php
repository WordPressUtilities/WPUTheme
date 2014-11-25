<?php
include dirname(__FILE__) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Doctype & HTML
---------------------------------------------------------- */

function wputh_get_doctype_html() {
?>
<!DOCTYPE HTML>
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html <?php language_attributes(); ?> class="is_ie10"><![endif]-->
<!--[if !IE]><!--> <html <?php language_attributes(); ?>><!--<![endif]-->
<?php
}
