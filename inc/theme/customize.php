<?php
add_filter('wpu_theme_customize__sections', 'wpu_theme_customize__sections__wputheme', 10, 1);
if (!function_exists('wpu_theme_customize__sections__wputheme')) {
    function wpu_theme_customize__sections__wputheme($sections) {
        return array(
            'global' => array(
                'name' => 'Global',
                'priority' => 199
            ) ,
            'header' => array(
                'name' => 'Header',
                'priority' => 200
            )
        );
    }
}

add_filter('wpu_theme_customize__settings', 'wpu_theme_customize__settings__wputheme', 10, 1);
if (!function_exists('wpu_theme_customize__settings__wputheme')) {
    function wpu_theme_customize__settings__wputheme($settings) {
        return array(
            'wpu_link_color' => array(
                'label' => __('Link Color') ,
                'default' => '#6699CC',
                'section' => 'global',
                'css_selector' => 'a',
                'css_property' => 'color'
            ) ,
            'wpu_link_color_hover' => array(
                'label' => __('Link Color :hover') ,
                'default' => '#336699',
                'section' => 'global',
                'css_selector' => 'a:hover',
                'css_property' => 'color'
            ) ,
            'wpu_background_color' => array(
                'label' => __('Background Color') ,
                'default' => '#FFFFFF',
                'section' => 'global',
                'css_selector' => 'body',
                'css_property' => 'background-color'
            ) ,
            'wpu_background_image' => array(
                'label' => __('Background image site') ,
                'default' => '',
                'section' => 'global',
                'css_selector' => 'body',
                'css_property' => 'background-image'
            ) ,
            'wpu_text_title_align' => array(
                'label' => __('Title align') ,
                'default' => 'left',
                'section' => 'header',
                'css_selector' => '.main-title',
                'css_property' => 'text-align'
            ) ,
            'wpu_text_title_size' => array(
                'label' => __('Title size') ,
                'default' => '25px',
                'section' => 'header',
                'css_selector' => '.main-title',
                'css_property' => 'font-size'
            ) ,
        );
    }
}
