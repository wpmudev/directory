<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options('ads_settings'); ?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'ads' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Advertising Settings', $this->text_domain ); ?></h1>

	<form action="#" method="post" class="dp-ads">
		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Advertising', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="header_ad_code"><?php _e('Ad Code Header Banner', 'directory') ?></label>
						</th>
						<td>
							<textarea id="header_ad_code" name="header_ad_code" rows="15" cols="50"><?php if ( isset( $options['header_ad_code'] ) ) echo stripslashes( $options['header_ad_code'] ); ?></textarea>
							<br />
							<span class="description"><?php _e('Place ad code here!', 'directory'); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="ads_settings" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

</div>
