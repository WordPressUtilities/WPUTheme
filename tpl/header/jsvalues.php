<?php
if (!defined('WPUTH_PAGE_BODYCLASS')) {
    define('WPUTH_PAGE_BODYCLASS', implode(' ', get_body_class('cssc-is-responsive')));
}
$js_values_html_attributes = apply_filters('wputh_jsvalues', array(
    'page_title' => addslashes(trim(wp_title('|', false))),
    'body_class' => WPUTH_PAGE_BODYCLASS
));
$html_jsvalues = '';
foreach ($js_values_html_attributes as $id => $value) {
    $html_jsvalues .= ' data-' . $id . '="' . esc_attr($value) . '"';
}

?>
<div id="js-values" class="js-values" <?php echo $html_jsvalues; ?>></div>
