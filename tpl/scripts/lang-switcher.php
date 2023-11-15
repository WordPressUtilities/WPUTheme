<?php

$display_languages = wputh_translated_url(true);
if (empty($display_languages) || count($display_languages) <= 1) {
    return;
}
echo '<ul class="languages-switcher">';
foreach ($display_languages as $slug => $lang) {
    echo '<li><a hreflang="' . $slug . '" ' . ($lang['current'] ? 'class="current"' : '') . ' href="' . $lang['url'] . '">';
    if (isset($lang['flag'])) {
        echo '<span class="image"><img src="' . esc_url($lang['flag']) . '" alt="" /></span>';
    }
    echo '<span class="name">' . $lang['name'] . '</span>';
    echo '</a></li>';
}
echo '</ul>';
