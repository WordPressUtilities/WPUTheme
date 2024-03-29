<?php
$_min_count = apply_filters('wputh_searchform_datalist_mincount', 3);
include __DIR__ . '/../../z-protect.php';
?>
<div class="search search--header header-search">
  <form role="search" method="get" id="header-search" class="search__form" action="<?php echo home_url(); ?>">
      <div class="search__inner">
          <label class="cssc-remove-element" for="s"><?php _e('Search for:', 'wputh');?></label>
          <input list="header-search-list" type="text" value="" name="s" id="s" class="search__input" placeholder="<?php echo esc_attr__('Enter your keywords...', 'wputh'); ?>" title="<?php echo esc_attr__('Search by keywords', 'wputh'); ?>" />
          <button type="submit" class="search__submit cssc-button cssc-button--default" id="search_submit" title="<?php echo sprintf(__('Search on %s', 'wputh'), get_bloginfo('name')); ?>"><?php _e('Search', 'wputh');?></button>
      </div>
  </form>
</div>
<?php
$all_terms = array();
$terms = get_terms(array(
    'taxonomy' => array(
        'post_tag',
        'category'
    )
));
foreach ($terms as $item) {
    if ($item->count < $_min_count) {
        continue;
    }
    $all_terms[$item->slug] = $item->name;
}
asort($all_terms);

if (!empty($all_terms)):
    echo '<datalist id="header-search-list">';
    foreach ($all_terms as $term):
        echo '<option value="' . $term . '"></option>';
    endforeach;
    echo '</datalist>';
endif;
