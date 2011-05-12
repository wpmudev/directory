<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $href_part = 'edit.php?post_type=listing'; ?>

<h2>
<?php if ( $page == 'settings' ): ?>
	<a class="nav-tab <?php if ( $tab == 'general' || empty( $tab ) ) echo 'nav-tab-active'; ?>" href="<?php printf( '%s&page=%s&tab=%s', $href_part, $page, $tab ); ?>"><?php _e( 'General', $this->text_domain ); ?></a>
<?php endif; ?>

<?php do_action('directory_render_admin_navigation_tabs', $href_part, $page, $tab ); ?>
</h2>

<?php if ( $page == 'settings' && $tab == 'general' || empty( $tab ) ): ?>
<ul>
    <li class="subsubsub"><h3><a class="<?php if ( $sub == 'general' || empty( $sub ) ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, $tab, 'general' ); ?>"><?php _e( 'General Settings', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( $sub == 'ads' ) echo 'current'; ?>" href="<?php printf( '%s&page=%s&tab=%s&sub=%s', $href_part, $page, $tab, 'ads' ); ?>"><?php _e( 'Advertising', $this->text_domain ); ?></a></h3></li>
</ul>
<?php endif; ?>

<?php do_action('directory_render_admin_navigation_subs', $href_part, $page, $tab, $sub ); ?>

<div class="clear"></div>
