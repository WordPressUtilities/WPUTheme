<?php
if (!defined('WPUTH_PAGE_BODYCLASS')) {
    define('WPUTH_PAGE_BODYCLASS', implode(' ', get_body_class('cssc-is-responsive')));
}
/* Base values */
$js_values_html_attributes = array(
    'page_title' => addslashes(trim(wp_title('|', false))),
    'body_class' => WPUTH_PAGE_BODYCLASS
);

/* Languages */
$display_languages = wputh_translated_url();
if (count($display_languages) > 1) {
    $js_values_html_attributes['lang'] = implode(',', array_keys($display_languages));
    foreach ($display_languages as $lang) {
        $js_values_html_attributes['lang-' . $lang['name']] = $lang['url'];
    }
}

/* Filter values */
$js_values_html_attributes = apply_filters('wputh_jsvalues', $js_values_html_attributes);

/* Convert to HTML */
$html_jsvalues = '';
foreach ($js_values_html_attributes as $id => $value) {
    $html_jsvalues .= ' data-' . $id . '="' . esc_attr($value) . '"';
}

?>
<div id="js-values" class="js-values" <?php echo $html_jsvalues; ?>></div>
