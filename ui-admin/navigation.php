<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $href_part = 'edit.php?post_type=directory_listing'; ?>

<h2 class="nav-tab-wrapper">
<?php if ( $page == 'settings' ): ?>
	<a class="nav-tab <?php if ( $tab == 'general' || empty( $tab ) ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'general' ); ?>"><?php _e( 'General', $this->text_domain ); ?></a>
    <a class="nav-tab <?php if ( $tab == 'capabilities' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'capabilities' ); ?>"><?php _e( 'Capabilities', $this->text_domain ); ?></a>
    <?php if ( class_exists( 'DR_Theme_Core' ) ):?>
    <a class="nav-tab <?php if ( $tab == 'ads' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'ads' ); ?>"><?php _e( 'Advertising', $this->text_domain ); ?></a>
    <?php endif; ?>
    <a class="nav-tab <?php if ( $tab == 'payments' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'payments' ); ?>"><?php _e( 'Payments', $this->text_domain ); ?></a>
    <a class="nav-tab <?php if ( $tab == 'payments-type' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'payments-type' ); ?>"><?php _e( 'Payments Type', $this->text_domain ); ?></a>
	<a class="nav-tab <?php if ( $tab == 'shortcodes' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'shortcodes' ); ?>"><?php _e( 'Shortcodes', $this->text_domain ); ?></a>
<?php endif; ?>

<?php do_action('dr_render_admin_navigation_tabs', $href_part, $page, $tab ); ?>
</h2>

<div class="clear"></div>
