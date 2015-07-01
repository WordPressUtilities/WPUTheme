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

/* ----------------------------------------------------------
  Title tag
---------------------------------------------------------- */

add_action('wputheme_header_banner', 'wputh_display_title');
function wputh_display_title() {
    $main_tag = is_home() ? 'h1' : 'div';
    $main_tag_classname = 'h1 main-title';
    $title_content = get_bloginfo('name');
    if (has_header_image()) {
        $title_content = '<img src="' . get_header_image() . '" alt="' . esc_attr($title_content) . '" />';
        $main_tag_classname.= ' main-logo';
    }
    echo '<' . $main_tag . ' class="' . $main_tag_classname . '"><a href="' . home_url() . '">' . $title_content . '</a></' . $main_tag . '>';
}
