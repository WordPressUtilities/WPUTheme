<?php
/* Template Name: FAQ */
include __DIR__ . '/../z-protect.php';
the_post();
$content_faq = '';
ob_start();
the_content();
$content_raw = ob_get_clean();
$content = explode('<h3>', $content_raw);

foreach ($content as $faq_element) {
    if (!empty($faq_element)) {
        $faq_element = str_replace('</h3>', '</h3><div class="faq-element__content">', $faq_element);
        $content_faq .= '<div class="faq-element"><h3 class="faq-element__title">'.$faq_element.'</div></div>';
    }
}
get_header();
?>
<div class="main-content">
<article>
    <h1><?php the_title(); ?></h1>
    <div id="faq-content">
        <?php echo $content_faq; ?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
