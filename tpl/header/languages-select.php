<?php

$display_languages = wputh_translated_url(1);
if (empty($display_languages) || count($display_languages) <= 1) {
    return;
}
echo '<div class="switch-lang">';
echo '<select id="switch-lang-select" name="switch-lang-select" class="languages wpu-pll-lang">';
foreach ($display_languages as $id_lang => $lang) {
    echo '<option ' . ($lang['current'] ? 'selected="selected"' : '') . ' value="' . $lang['url'] . '" data-lang="' . $id_lang . '">' . $lang['name'] . '</option>';
}
echo '</select>';
echo '</div>';
