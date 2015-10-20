<?php
global $wp_query, $wputh_query, $paged;

$next_page = '';

// Getting a real number for paged
$pagedd = max(1, $paged);

// Getting the good query
$pagi_query = $wp_query;
if (is_object($wputh_query)) {
    $pagi_query = $wputh_query;
}

// Default parameters
$display_pagination = true;

// need an unlikely integer
$big = 999999999;

if (!isset($prev_text) || empty($prev_text)) {
    $prev_text = __('« Previous', 'wputh');
}
if (!isset($next_text) || empty($next_text)) {
    $next_text = __('Next »', 'wputh');
}

$paginate_args = array(
    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))) ,
    'format' => '?paged=%#%',
    'current' => $pagedd,
    'total' => $pagi_query->max_num_pages,
    'prev_text' => $prev_text,
    'next_text' => $next_text,
);

// load next page
if ($pagedd < $pagi_query->max_num_pages) {
    $next_page = apply_filters('wputheme_loadmore_button', get_pagenum_link($pagedd + 1));
}

// Hiding pagination if not enough pages
if ($pagi_query->max_num_pages == 1) {
    $display_pagination = false;
}

if ($display_pagination) { ?>
<nav class="main-pagination">
    <p><?php
    switch (PAGINATION_KIND) {
        case 'numbers':
            echo paginate_links($paginate_args);
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

unset($pagi_query);
