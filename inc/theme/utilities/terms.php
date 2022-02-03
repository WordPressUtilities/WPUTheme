<?php

/* ----------------------------------------------------------
  Search in terms
---------------------------------------------------------- */

/**
 * Search in terms
 * @param  string $search   String to search
 * @param  string $taxonomy Taxonomy name
 * @return array            Array of terms
 */
function wputh_search_terms($search, $taxonomy) {
    $terms_name = get_terms(array(
        'taxonomy' => $taxonomy,
        'name__like' => $search,
        'hide_empty' => false
    ));

    if (!$terms_name) {
        $terms_name = array();
    }

    $terms_desc = get_terms(array(
        'taxonomy' => $taxonomy,
        'description__like' => $search,
        'hide_empty' => false
    ));

    if (!$terms_desc) {
        $terms_desc = array();
    }

    /* Merge the two results */
    $terms = array_merge($terms_name, $terms_desc);

    /* De-duplicate */
    /* Thx to https://stackoverflow.com/a/946300 */
    $terms = array_map("unserialize", array_unique(array_map("serialize", $terms)));

    return $terms;
}