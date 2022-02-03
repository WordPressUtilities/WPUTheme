<?php

/* ----------------------------------------------------------
  Fix locale
---------------------------------------------------------- */

/* Thx https://openclassrooms.com/forum/sujet/avoir-la-date-en-francais-grace-a-un-datetime-29453#message-5364823 */
if (class_exists('DateTime')) {
    class DateTimeFrench extends DateTime {
        public function format($format) {
            $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
            $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
            $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
            return str_replace($english_months, $french_months, str_replace($english_days, $french_days, parent::format($format)));
        }
    }
}

/* ----------------------------------------------------------
  Get current URL
---------------------------------------------------------- */

/* Thanks to http://kovshenin.com/2012/current-url-in-wordpress/ */
function wputh_get_current_url() {
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));
    if (is_singular() || is_single() || is_page()) {
        $current_url = get_permalink();
    }
    return $current_url;
}

/* ----------------------------------------------------------
  Check if internal link
---------------------------------------------------------- */

function wpu_is_internal_link($external_url) {
    $url_host = parse_url($external_url, PHP_URL_HOST);
    $base_url_host = parse_url(get_site_url(), PHP_URL_HOST);
    return ($url_host == $base_url_host || empty($url_host));
}
