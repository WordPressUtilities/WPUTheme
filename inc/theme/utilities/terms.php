<?php

/* ----------------------------------------------------------
  Search in terms
---------------------------------------------------------- */

/**
 * Search in terms
 * @param  string $search      String to search
 * @param  string $taxonomy    Taxonomy name
 * @param  array  $args        Extra args
 * @return array               Array of terms
 */
function wputh_search_terms($search, $taxonomy, $args = array()) {

    if (!is_array($args)) {
        $args = array();
    }
    if (!isset($args['hide_empty'])) {
        $args['hide_empty'] = false;
    }

    $terms_name = wputh_search_terms__like(array(
        'taxonomy' => $taxonomy,
        'name__like' => $search,
        'hide_empty' => $args['hide_empty']
    ));

    if (!$terms_name) {
        $terms_name = array();
    }

    $terms_desc = wputh_search_terms__like(array(
        'taxonomy' => $taxonomy,
        'description__like' => $search,
        'hide_empty' => $args['hide_empty']
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

/**
 * Wrapper for get_terms allowing name__like to be an array
 * @param  array $args   Extra args
 * @return array $terms  Results
 */
function wputh_search_terms__like($args) {

    $arg_id = 'name__like';
    if(isset($args['description__like'])){
        $arg_id = 'description__like';
    }


    if (!isset($args[$arg_id])) {
        return array();
    }

    /* Ensuring the search string is correct */
    if (!is_array($args[$arg_id])) {
        /* Removing invalid chars */
        $args[$arg_id] = str_replace(array('-', ':', ';', ','), ' ', $args[$arg_id]);
        /* Converting to an array */
        $args[$arg_id] = explode(' ', $args[$arg_id]);
        /* Removing empty values */
        $args[$arg_id] = array_filter(array_map('trim', $args[$arg_id]));
    }

    /* Extracting results for each word */
    $terms_results = array();
    foreach ($args[$arg_id] as $name) {
        $new_args = $args;
        $new_args[$arg_id] = $name;
        $t = get_terms($new_args);
        if (!empty($t) && $t) {
            $terms_results[] = $t;
        }
    }

    /* Keeping only terms containing all words */
    $new_terms = array();
    foreach ($terms_results as $term_name_group) {
        if (empty($new_terms)) {
            $new_terms = $term_name_group;
        } else {
            $new_terms = array_uintersect($new_terms, $term_name_group, 'wputh_search_terms__compare_term_id');
        }
    }
    return $new_terms;
}

function wputh_search_terms__compare_term_id($val1, $val2) {
    return strcmp($val1->term_id, $val2->term_id);
}
