<?php
/* Template Name: Forgotten Password */
include dirname(__FILE__) . '/../z-protect.php';

if (is_user_logged_in()) {
    wp_redirect(apply_filters('wputh_forgottenpassword_url_logged_in', site_url()));
    die;
}

get_header();
the_post();

$redirect_to = add_query_arg('success', '1', get_page_link(get_the_ID()));
$message_success = apply_filters('wputh_forgottenpassword_message', __('Check your email for the confirmation link.'));
?>
<div class="main-content">
<article>
    <h1 class="main-content__title"><?php the_title();?></h1>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
        <div class="messages">
            <ul>
                <li class="success-msg"><?php echo $message_success; ?></li>
            </ul>
        </div>
    <?php endif;?>
    <?php do_action('wputh_forgottenpassword_before_form'); ?>
    <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url(network_site_url('wp-login.php?action=lostpassword', 'login_post')); ?>" method="post">
        <p>
            <label for="user_login" ><?php _e('Username or Email Address');?><br />
            <input type="text" name="user_login" id="user_login" class="input" value="" size="20" autocapitalize="off" /></label>
        </p>
        <?php do_action('lostpassword_form');?>
        <?php do_action('wputh_forgottenpassword_form');?>
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Get New Password');?>" /></p>
    </form>
    <?php do_action('wputh_forgottenpassword_after_form'); ?>
</article>
</div>
<?php
get_sidebar();
get_footer();
