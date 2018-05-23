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

echo '<ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
foreach ($elements_ariane as $id => $element) {
    $last = (isset($element['last']) && $element['last'] == 1);
    $itemAttributes = 'itemprop="item" class="element-ariane element-ariane--' . $id . ' ' . ($last ? 'is-last' : '') . '"';
    echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    if (isset($element['link'])) {
        echo '<a href="' . $element['link'] . '" ' . $itemAttributes . '>' . $element['name'] . '</a>';
    } else {
        echo '<strong ' . $itemAttributes . '>' . $element['name'] . '</strong>';
    }
    echo '</li>';
}
echo '</ul>';
