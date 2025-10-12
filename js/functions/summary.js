/* ----------------------------------------------------------
  Build a summary of links
---------------------------------------------------------- */

function wputheme_build_summary($headings, $links_list, $wrapper) {
    'use strict';
    if (!$headings || !$links_list) {
        return false;
    }

    var _has_summary = false;
    for (var i = 0, len = $headings.length; i < len; i++) {
        if (wputheme_build_summary_item($headings[i], i, $links_list)) {
            _has_summary = true;
        }
    }

    if ($wrapper) {
        $wrapper.setAttribute('data-has-summary', _has_summary ? 'true' : 'false');
        if (!_has_summary) {
            $wrapper.style.display = 'none';
        }
    }
}

function wputheme_build_summary_item($item, i, $links_list) {
    if (!wputheme_is_element_visible($item)) {
        return false;
    }

    /* Add an ID to this block */
    var unique_id;
    if ($item.id) {
        unique_id = $item.id;
    } else {
        unique_id = $item.textContent.replace(/[^a-z0-9\-]+/gi, '-').toLowerCase() + '-' + i;
        if (document.getElementById(unique_id)) {
            unique_id += '-' + Math.random().toString(36).substring(2, 10);
        }
        $item.id = unique_id;
    }

    /* Create a link and append it */
    var list_item = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#' + unique_id;
    link.textContent = $item.textContent;
    list_item.appendChild(link);
    $links_list.appendChild(list_item);

    return true;
}
