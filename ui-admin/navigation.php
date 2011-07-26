<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $href_part = 'edit.php?post_type=directory_listing'; ?>

<h2>
<?php if ( $page == 'settings' ): ?>
	<a class="nav-tab <?php if ( $tab == 'general' || empty( $tab ) ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'general' ); ?>"><?php _e( 'General', $this->text_domain ); ?></a>
	<a class="nav-tab <?php if ( $tab == 'payments' ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, 'payments' ); ?>"><?php _e( 'Payments', $this->text_domain ); ?></a>
<?php endif; ?>

<?php do_action('dr_render_admin_navigation_tabs', $href_part, $page, $tab ); ?>
</h2>

<?php if ( $page == 'settings' && $tab == 'general' || empty( $tab ) ): ?>
<ul>
    <li class="subsubsub"><h3><a class="<?php if ( $sub == 'general' || empty( $sub ) ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, 'general', 'general' ); ?>"><?php _e( 'General Settings', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( $sub == 'capabilities' || empty( $sub ) ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, 'general', 'capabilities' ); ?>"><?php _e( 'Capabilities', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( $sub == 'ads' ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, 'general', 'ads' ); ?>"><?php _e( 'Advertising', $this->text_domain ); ?></a></h3></li>
</ul>
<?php endif; ?>

<?php if ( $page == 'settings' && $tab == 'payments' ): ?>
<ul>
	<li class="subsubsub"><h3><a class="<?php if ( $sub == 'checkout' || empty( $sub ) ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, 'payments', 'checkout' ); ?>"><?php _e( 'Payment Settings', $this->text_domain ); ?></a> | </h3></li>
	<li class="subsubsub"><h3><a class="<?php echo $sub == 'type' ? 'current' : ''; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, 'payments', 'type' ); ?>"><?php _e( 'Payment Type', $this->text_domain ); ?></a></h3></li>
</ul>
<?php endif; ?>

<?php do_action('dr_render_admin_navigation_subs', $href_part, $page, $tab, $sub ); ?>

<div class="clear"></div>
