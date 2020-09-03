<?php

/**
 * Breadcrumbs
 *
 * @package default
 */

if (apply_filters('wputheme_hide_breadcrumbs_page', false)) {
    return;
}

if (!isset($elements_ariane)) {
    $elements_ariane = wputh_get_breadcrumbs();
}

if (empty($elements_ariane)) {
    return;
}

echo wputh_get_breadcrumbs_html($elements_ariane);
