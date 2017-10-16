<?php
global $wp_query, $wputh_query, $paged;

// Getting a real number for paged
$pagedd = max(1, $paged);
if (isset($_custom_pagedd)) {
    $pagedd = $_custom_pagedd;
}

// Getting max num pages
$max_num_pages = 1;
if (is_object($wputh_query)) {
    $max_num_pages = $wputh_query->max_num_pages;
} else {
    $max_num_pages = $wp_query->max_num_pages;
}
if (isset($_custom_max_num_pages)) {
    $max_num_pages = $_custom_max_num_pages;
}

// Setting text
if (!isset($prev_text) || empty($prev_text)) {
    $prev_text = __('« Previous', 'wputh');
}
if (!isset($next_text) || empty($next_text)) {
    $next_text = __('Next »', 'wputh');
}

// Building paginate
$big = 999999999;
$pagenum_link = get_pagenum_link($big);
if (isset($_custom_pagenum_link)) {
    $pagenum_link = $_custom_pagenum_link;
}
$paginate_args = array(
    'base' => str_replace($big, '%#%', esc_url($pagenum_link)),
    'format' => '?paged=%#%',
    'current' => $pagedd,
    'total' => $max_num_pages,
    'before_page_number' => '<span class="pagenum-content">',
    'after_page_number' => '</span>',
    'prev_text' => $prev_text,
    'next_text' => $next_text
);

// Load next page
$next_page = '';
if ($pagedd < $max_num_pages) {
    $next_page = apply_filters('wputheme_loadmore_button', get_pagenum_link($pagedd + 1));
}

if ($max_num_pages > 1) {?>
<nav class="main-pagination">
<p><?php
switch (PAGINATION_KIND) {
case 'numbers':
    echo paginate_links(apply_filters('wputheme_paginate_args', $paginate_args));
    break;
case 'load-more':
    echo $next_page;
    break;
default:
    posts_nav_link();
}
    ?></p>
</nav>
<?php
}
