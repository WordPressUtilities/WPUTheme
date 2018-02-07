<?php

$display_languages = wputh_translated_url();

if (!empty($display_languages) && count($display_languages) > 1) {
    echo '<div class="languages">';
    foreach ($display_languages as $lang) {
        echo '<a hreflang="' . $lang['name'] . '" ' . ($lang['current'] ? 'class="current"' : '') . ' href="' . $lang['url'] . '"><span>' . $lang['name'] . '</span></a>';
    }
    echo '</div>';
}
