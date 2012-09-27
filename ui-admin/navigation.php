<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $href_part = 'admin.php?page=directory_settings'; ?>

<?php screen_icon('options-general'); ?>
<h2><?php echo sprintf( __('Directory Settings %s', $this->text_domain), DR_VERSION );?></h2>

<h2 class="nav-tab-wrapper">
	<?php if ( $page == 'directory_settings' ): ?>
	<a id="dr-settings_general" class="nav-tab <?php if ( $tab == 'general' || empty( $tab ) ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=general');?>"><?php _e( 'General', $this->text_domain ); ?></a>
	<a id="dr-settings_capabilities" class="nav-tab <?php if ( $tab == 'capabilities' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=capabilities');?>"><?php _e( 'Capabilities', $this->text_domain ); ?></a>
	<a id="dr-settings_payments" class="nav-tab <?php if ( $tab == 'payments' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=payments');?>"><?php _e( 'Payments', $this->text_domain ); ?></a>
	<a id="dr-settings_payments_type" class="nav-tab <?php if ( $tab == 'payment-types' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=payment-types');?>"><?php _e( 'Payment Types', $this->text_domain ); ?></a>

	<?php if ( class_exists( 'affiliateadmin' ) ):?>
	<a id="dr-settings_affiliate" class="nav-tab <?php if ( $tab == 'affiliate' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=affiliate');?>"><?php _e( 'Affiliate', $this->text_domain ); ?></a>
	<?php endif; ?>

	<a id="dr-settings_shortcodes" class="nav-tab <?php if ( $tab == 'shortcodes' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_settings&tab=shortcodes');?>"><?php _e( 'Shortcodes', $this->text_domain ); ?></a>
	<?php endif; ?>

	<?php if( $page == 'directory_credits'):?>
	<a class="nav-tab <?php if ( $tab == 'my-credits' || empty( $tab) )  echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_credits&tab=my-credits'); ?>" ><?php _e( 'My Credits', $this->text_domain ); ?></a>
	<?php
	$current_user = wp_get_current_user();
	if(! empty($current_user->ID) && $current_user->has_cap('manage_options')): ?>
	<a class="nav-tab <?php if ( $tab == 'send-credits' || empty( $tab) )  echo 'nav-tab-active'; ?>" href="<?php echo esc_attr('admin.php?page=directory_credits&tab=send-credits'); ?>" ><?php _e( 'Send Credits', $this->text_domain ); ?></a>
	<?php endif; ?>

	<?php endif; ?>

	<?php do_action('dr_render_admin_navigation_tabs', $href_part, $page, $tab ); ?>
</h2>

<div class="clear"></div>
