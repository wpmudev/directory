<?php
if (!defined('ABSPATH')) die('No direct access allowed!');

$options = $this->get_options('paypal');
$gateways = $this->get_options('gateways');

?>


<script language="JavaScript">

	jQuery( document ).ready( function() {

		//Creating tabs
		jQuery( function() {
			jQuery( "#tabs" ).tabs();
		});

		jQuery( "#gateways input[type='checkbox']" ).change( function () {
			if ( 'payment_free' == jQuery( this ).attr( 'id' ) ) {
				jQuery( "#gateways input[type='checkbox']" ).attr( 'checked', false );
				jQuery( this ).attr( 'checked', true );
			} else {
				jQuery( "#payment_free" ).attr( 'checked', false );
			}
			jQuery( "#payment_type" ).submit();
			return false;
		});

	});

</script>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'payments-type' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Payments Type', $this->text_domain ); ?></h1>
	<p class="description">
		<?php _e( 'Here you can set how users can pay to you for capability the creation listings on your site.', $this->text_domain ) ?>
	</p>

	<br />
	<div id="gateways" class="postbox">
		<h3 class='hndle'><span><?php _e( 'Gateway(s)', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<form action="#" method="post" name="payment_type" id="payment_type" class="dp-payments">
				<input type="hidden"  name="save" value="save">
				<input type="hidden" name="key" value="gateways" />
				<?php wp_nonce_field('verify'); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Select Payment Gateway(s)', $this->text_domain ) ?></th>
						<td>
							<p>
								<label>
									<input type="checkbox" class="mp_allowed_gateways" name="free" id="payment_free" value="1" <?php checked( ! empty($gateways['free'])); ?> /> <?php _e( 'Free Listings', $this->text_domain ); ?>
									<span class="description"><?php _e( '(logged users can create listings for free).', $this->text_domain ); ?></span>
								</label>
							</p>
							<p>
								<label>
									<input type="checkbox" class="mp_allowed_gateways" name="paypal" value="1" <?php checked( ! empty($gateways['paypal'])); ?> /> <?php _e( 'PayPal', $this->text_domain ); ?>
								</label>
							</p>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>

	<br />

	<?php if ( isset( $gateways ) && empty($gateways['free'] ) ): ?>
	<form action="#" method="post" name="paypal_" class="dp-payments">

		<?php if ( 1 == $gateways['paypal'] ): ?>
		<div class="postbox">
			<h3 class='hndle'><span><b><?php _e( 'PayPal Settings', $this->text_domain ) ?></b></span></h3>
			<div class="inside">
				<p class="description">
					<?php _e( "Express Checkout is PayPal's premier checkout solution, which streamlines the checkout process for buyers and keeps them on your site after making a purchase. Unlike PayPal Pro, there are no additional fees to use Express Checkout, though you may need to do a free upgrade to a business account.", $this->text_domain ) ?>
					<a href="https://cms.paypal.com/us/cgi-bin/?&amp;cmd=_render-content&amp;content_ID=developer/e_howto_api_ECGettingStarted" target="_blank"><?php _e( 'More Info', $this->text_domain ) ?></a>
				</p>
				<table class="form-table">
					<tr>
						<th>
							<label for="api_url"><?php _e('PayPal API Calls URL', $this->text_domain ) ?></label>
						</th>
						<td>
							<select id="api_url" name="api_url">
								<option value="sandbox" <?php if ( isset( $options['api_url'] ) && $options['api_url'] == 'sandbox' ) echo 'selected="selected"' ?>><?php _e( 'Sandbox', $this->text_domain ); ?></option>
								<option value="live"    <?php if ( isset( $options['api_url'] ) && $options['api_url'] == 'live' )    echo 'selected="selected"' ?>><?php _e( 'Live', $this->text_domain ); ?></option>
							</select>
							<span class="description"><?php _e( 'Choose between PayPal Snadbox and PayPal Live.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="business_email"><?php _e( 'PayPal Business Email', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" id="business_email" name="business_email" value="<?php if ( isset( $options['business_email'] ) ) echo $options['business_email']; ?>" size="40" />
							<span class="description"><?php _e( 'Your PayPal business email for Recurring Payments.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e( 'PayPal API Credentials', $this->text_domain ) ?></label>
						</th>
						<td>
							<span class="description">
								<?php _e( 'You must login to PayPal and create an API signature to get your credentials. ', $this->text_domain ) ?>
								<a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&amp;content_ID=developer/e_howto_api_ECAPICredentials" target="_blank"><?php _e( 'Instructions', $this->text_domain ) ?></a>
							</span>
							<p>
								<label for="api_username"><?php _e( 'API Username', $this->text_domain ) ?></label>
								<br />
								<input type="text" id="api_username" name="api_username" value="<?php if ( isset( $options['api_username'] ) ) echo $options['api_username']; ?>" size="40" />
							</p>
							<p>
								<label for="api_password"><?php _e( 'API Password', $this->text_domain ) ?></label>
								<br />
								<input type="text" id="api_password" name="api_password" value="<?php if ( isset( $options['api_password'] ) ) echo $options['api_password']; ?>" size="40" />

							</p>
							<p>
								<label for="api_signature"><?php _e( 'API Signature', $this->text_domain ) ?></label>
								<br />
								<textarea rows="2" cols="55" id="api_signature" name="api_signature"><?php if ( isset( $options['api_signature'] ) ) echo $options['api_signature']; ?></textarea>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="currency"><?php _e( 'Currency', $this->text_domain ) ?></label>
						</th>
						<td>
							<select id="currency" name="currency">
								<option value="USD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'USD' ) echo 'selected="selected"' ?>><?php _e( 'U.S. Dollar', $this->text_domain ) ?></option>
								<option value="EUR" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'EUR' ) echo 'selected="selected"' ?>><?php _e( 'Euro', $this->text_domain ) ?></option>
								<option value="GBP" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'GBP' ) echo 'selected="selected"' ?>><?php _e( 'Pound Sterling', $this->text_domain ) ?></option>
								<option value="CAD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CAD' ) echo 'selected="selected"' ?>><?php _e( 'Canadian Dollar', $this->text_domain ) ?></option>
								<option value="AUD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'AUD' ) echo 'selected="selected"' ?>><?php _e( 'Australian Dollar', $this->text_domain ) ?></option>
								<option value="JPY" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'JPY' ) echo 'selected="selected"' ?>><?php _e( 'Japanese Yen', $this->text_domain ) ?></option>
								<option value="CHF" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CHF' ) echo 'selected="selected"' ?>><?php _e( 'Swiss Franc', $this->text_domain ) ?></option>
								<option value="SGD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'SGD' ) echo 'selected="selected"' ?>><?php _e( 'Singapore Dollar', $this->text_domain ) ?></option>
								<option value="NZD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'NZD' ) echo 'selected="selected"' ?>><?php _e( 'New Zealand Dollar', $this->text_domain ) ?></option>
								<option value="SEK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'SEK' ) echo 'selected="selected"' ?>><?php _e( 'Swedish Krona', $this->text_domain ) ?></option>
								<option value="DKK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'DKK' ) echo 'selected="selected"' ?>><?php _e( 'Danish Krone', $this->text_domain ) ?></option>
								<option value="NOK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'NOK' ) echo 'selected="selected"' ?>><?php _e( 'Norwegian Krone', $this->text_domain ) ?></option>
								<option value="CZK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CZK' ) echo 'selected="selected"' ?>><?php _e( 'Czech Koruna', $this->text_domain ) ?></option>
								<option value="HUF" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'HUF' ) echo 'selected="selected"' ?>><?php _e( 'Hungarian Forint', $this->text_domain ) ?></option>
								<option value="PLN" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'PLN' ) echo 'selected="selected"' ?>><?php _e( 'Polish Zloty', $this->text_domain ) ?></option>
							</select>
							<span class="description"><?php _e( 'The currency in which you want to process payments.', $this->text_domain ); ?></span>
						</td>
					</tr>
				</table>

			</div>
		</div>
		<?php endif;?>

		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="paypal" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

	<?php endif; ?>

</div>
