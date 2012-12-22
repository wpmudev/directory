<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>
<?php
$dr_labels_txt = array (
'recurring' => __( 'Affiliate payment credited for signed member (recurring payments):', $this->text_domain ),
'one_time'  => __( 'Affiliate payment credited for permanent member (one-time payments):', $this->text_domain ),
);

$payment_settings   = $this->get_options( 'payments' );

$affiliate_settings['payment_settings']['recurring_cost']   = $payment_settings['recurring_cost'];
$affiliate_settings['payment_settings']['one_time_cost']   = $payment_settings['one_time_cost'];
$affiliate_settings['dr_labels_txt']                        = $dr_labels_txt;
$affiliate_settings['cost']                                 = $this->get_options( 'affiliate_settings' );
?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'affiliate' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Affiliate settings', $this->text_domain ); ?></h1>
	<p class="description">
		<?php _e( 'Here you can set reward for your affiliates.', $this->text_domain ) ?>
	</p>
	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'Affiliate', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<?php if ( !class_exists( 'affiliateadmin' ) || !defined( 'AFF_DIRECTORY_ADDON' ) ): ?>
			<p>
				<?php _e( 'This feature will be available only after installation the <b>Affiliate plugin</b>  and activation the <b>Directory add-on</b> there.', $this->text_domain ) ?>
				<br />
				<?php printf ( __( 'More information about the Affiliate plugin you can get <a href="%s" target="_blank">here</a>.', $this->text_domain ), 'http://premium.wpmudev.org/project/wordpress-mu-affiliate/' ); ?>
				<br /><br />

				<?php _e( 'Please activate:', $this->text_domain ) ?>
				<br />
				<?php _e( '1. The <b>Affiliate plugin</b>', $this->text_domain ) ?>
				<?php if ( class_exists( 'affiliate' ) ) _e( ' - <i>Completed</i>', $this->text_domain ); ?>
				<br />
				<?php _e( '2. The <b>Directory add-on</b> in the Affiliate plugin', $this->text_domain ) ?>
			</p>
			<?php endif;?>

			<?php do_action( 'directory_affiliate_settings', $affiliate_settings ); ?>

		</div>
	</div>

</div>
