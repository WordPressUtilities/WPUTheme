
/* ----------------------------------------------------------
  Build a summary of links
---------------------------------------------------------- */

function wputheme_build_summary($headings, $links_list) {
    'use strict';
    if (!$headings || !$links_list) {
        return false;
    }
    /* $headings - i */
    for (var i = 0, len = $headings.length; i < len; i++) {
        wputheme_build_summary_item($headings[i], i, $links_list);
    }
}

function wputheme_build_summary_item($item, i, $links_list) {
    if (!wputheme_is_element_visible($item)) {
        return;
    }

    /* Add an ID to this block */
    var unique_id;
    if ($item.id) {
        unique_id = $item.id;
    } else {
        unique_id = $item.textContent.replace(/\s+/g, '-').toLowerCase() + '-' + i;
        $item.id = unique_id;
    }

    /* Create a link and append it */
    var list_item = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#' + unique_id;
    link.textContent = $item.textContent;
    list_item.appendChild(link);
    $links_list.appendChild(list_item);
}
