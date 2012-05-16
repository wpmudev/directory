<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $href_part = 'edit.php?post_type=directory_listing'; ?>

<h2 class="nav-tab-wrapper">
<?php if ( $page == 'settings' ): ?>
	<a id="dr-settings_general" class="nav-tab <?php if ( $tab == 'general' || empty( $tab ) ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'general' ); ?>"><?php _e( 'General', $this->text_domain ); ?></a>
    <a id="dr-settings_capabilities" class="nav-tab <?php if ( $tab == 'capabilities' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'capabilities' ); ?>"><?php _e( 'Capabilities', $this->text_domain ); ?></a>
    <a id="dr-settings_payments" class="nav-tab <?php if ( $tab == 'payments' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'payments' ); ?>"><?php _e( 'Payments', $this->text_domain ); ?></a>
    <a id="dr-settings_payments_type" class="nav-tab <?php if ( $tab == 'payments-type' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'payments-type' ); ?>"><?php _e( 'Payments Type', $this->text_domain ); ?></a>

    <?php if ( class_exists( 'affiliate' ) ):?>
    <a id="dr-settings_affiliate" class="nav-tab <?php if ( $tab == 'affiliate' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'affiliate' ); ?>"><?php _e( 'Affiliate', $this->text_domain ); ?></a>
    <?php endif; ?>

	<a id="dr-settings_shortcodes" class="nav-tab <?php if ( $tab == 'shortcodes' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&amp;page=%s&amp;tab=%s', $href_part, $page, 'shortcodes' ); ?>"><?php _e( 'Shortcodes', $this->text_domain ); ?></a>
<?php endif; ?>

<?php do_action('dr_render_admin_navigation_tabs', $href_part, $page, $tab ); ?>
</h2>

<div class="clear"></div>
