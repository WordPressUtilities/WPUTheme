<?php

/* ----------------------------------------------------------
  Remove AJAX parameter from URL
---------------------------------------------------------- */

function wputh_clean_ajax_parameter_from_url($url = '') {
    $ajax_param = 'ajax=1';
    /* First parameter of multiple parameters */
    $url = str_replace('?' . $ajax_param . '&', '?', $url);
    /* One parameter of multiple parameters */
    $url = str_replace('&' . $ajax_param, '', $url);
    /* Only parameter */
    $url = str_replace('?' . $ajax_param, '', $url);
    return $url;
}

/* Clean from pagenum */
add_filter('get_pagenum_link', 'wputh_clean_get_pagenum_link', 10, 1);
function wputh_clean_get_pagenum_link($url) {
    return wputh_clean_ajax_parameter_from_url($url);
}

/* ----------------------------------------------------------
  Get translated URL
---------------------------------------------------------- */

/**
 * Translate current URL with Qtranslate-slug
 * @param  text $lang   textual id for language (ex:fr)
 * @return mixed        translated URL or FALSE if error.
 */
function wputh_qtranslate_slug_get_current_url($lang) {
    global $qtranslate_slug;
    if (!isset($qtranslate_slug) || !is_object($qtranslate_slug)) {
        return false;
    }
    $url = $qtranslate_slug->get_current_url($lang);

    /* Base prefix for URL : http://example.com/fr/ */
    $base_url = get_site_url() . '/';
    $base_lang_url = $base_url . $lang . '/';

    /* If url does not start with base lang url */
    $url_root = substr($url, 0, strlen($base_lang_url));
    if (strlen($url) > strlen($url_root) && $url_root != $base_lang_url) {
        $url = str_replace(get_site_url() . '/', $base_lang_url, $url);
    }
    return wputh_clean_ajax_parameter_from_url($url);
}

function wputh_translated_url($use_full_lang_name = false) {
    $display_languages = array();
    $current_lang = '';
    $current_url = wputh_get_current_url();

    // Obtaining from Qtranslate
    if (function_exists('qtrans_getSortedLanguages') && function_exists('qtrans_getLanguage') && function_exists('qtrans_convertURL')) {
        $current_lang = qtrans_getLanguage();
        $languages = qtrans_getSortedLanguages();
        foreach ($languages as $lang) {
            $display_languages[$lang] = array(
                'name' => $lang,
                'current' => $lang == $current_lang,
                'url' => qtrans_convertURL($current_url, $lang, 0, 1)
            );
        }
        return $display_languages;
    }

    // Obtaining from Qtranslate X
    if (function_exists('qtranxf_getLanguage') && function_exists('qtranxf_getSortedLanguages') && function_exists('qtranxf_convertURL')) {
        global $q_config;
        $current_lang = qtranxf_getLanguage();
        $languages = qtranxf_getSortedLanguages();

        foreach ($languages as $lang) {
            if (class_exists('QtranslateSlug')) {
                /* Qtranslate slug needs a fix to force URL lang */
                $url = wputh_qtranslate_slug_get_current_url($lang);
            } else {
                $url = qtranxf_convertURL($current_url, $lang, 0, 1);
            }
            $full_name = $lang;
            if ($use_full_lang_name && isset($q_config['language_name'][$lang])) {
                $full_name = $q_config['language_name'][$lang];
            }
            $display_languages[$lang] = array(
                'name' => $full_name,
                'current' => $lang == $current_lang,
                'url' => $url
            );
        }
        return $display_languages;
    }

    // Obtaining from Polylang
    if (function_exists('pll_current_language')) {
        $current_lang = pll_current_language();
        $poly_langs = pll_the_languages(array(
            'raw' => 1,
            'echo' => 0
        ));

        if (is_array($poly_langs)) {
            usort($poly_langs, function ($a, $b) {
                return $a['order'] - $b['order'];
            });
            foreach ($poly_langs as $lang) {
                $full_name = $lang['slug'];
                if ($use_full_lang_name && isset($lang['name'])) {
                    $full_name = $lang['name'];
                }
                $display_languages[$lang['slug']] = array(
                    'flag' => $lang['flag'],
                    'name' => $full_name,
                    'current' => $lang['slug'] == $current_lang,
                    'url' => wputh_clean_ajax_parameter_from_url($lang['url'])
                );
            }
        }
    }

    if (!function_exists('pll_current_language') && defined('ICL_LANGUAGE_CODE') && function_exists('icl_get_languages')) {
        $current_lang = ICL_LANGUAGE_CODE;
        $wpml_langs = icl_get_languages();
        if (is_array($wpml_langs)) {
            foreach ($wpml_langs as $lang) {
                $full_name = $lang['code'];
                if ($use_full_lang_name && isset($lang['translated_name'])) {
                    $full_name = $lang['translated_name'];
                }
                $display_languages[$lang['code']] = array(
                    'name' => $full_name,
                    'current' => $lang['code'] == $current_lang,
                    'url' => wputh_clean_ajax_parameter_from_url($lang['url'])
                );
            }
        }
    }

    $display_languages = apply_filters('wputh_translated_url', $display_languages, $current_url);

    return $display_languages;
}
