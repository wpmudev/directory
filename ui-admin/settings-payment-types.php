<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php

$options = $this->get_options('payment_types');

//PayPal gateway
$paypal = (empty($options['paypal']) ) ? array() : $options['paypal'];

//Authorizenet gateway
$authorizenet = (empty($options['authorizenet']) ) ? array() : $options['authorizenet'];

?>
<script language="JavaScript">
	(function($) {
		$(document).ready(function() {
			$("#gateways input[type='checkbox']" ).change( function () {
				if ('use_free' == $(this).attr( 'id' ) ) {
					checked = $(this).prop('checked');
					$("#gateways input[type='checkbox']" ).prop( 'checked', false );
					$(this).prop('checked', checked );
				} else {
					$("#use_free").prop( 'checked', false );
				}
				$("#save").click();
				return false;
			});
		});
	}) (jQuery);
</script>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings','tab' => 'payment-types' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Payments Types', $this->text_domain ); ?></h1>
	<form id="payment_type" action="#" method="post" class="dp-payments">

		<div id="gateways" class="postbox">
			<h3 class='hndle'><span><?php _e( 'Payment Gateway Settings', $this->text_domain ) ?></span></h3>
			<div class="inside">

				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Select Payment Gateway(s)', $this->text_domain ) ?></th>
						<td>
							<p>
								<label>
									<input type="checkbox" class="cf_allowed_gateways" name="use_free" id="use_free" value="1" <?php checked( ! empty($options['use_free']) ); ?> />
									<?php _e( 'Free Listings', $this->text_domain ) ?>
									<span class="description"><?php _e( '(logged users can create listings for free).', $this->text_domain ); ?></span>
								</label>
							</p>
							<p>
								<label>
									<input type="checkbox" class="cf_allowed_gateways" name="use_paypal" id="use_paypal" value="1" <?php checked( ! empty($options['use_paypal']) ); ?> />
									<?php _e( 'PayPal', $this->text_domain ) ?>
								</label>
							</p>

							<p>
								<label>
									<input type="checkbox" class="cf_allowed_gateways" name="use_authorizenet" id="use_authorizenet" value="1" <?php echo checked( ! empty($options['use_authorizenet']) ); ?> />
									<?php _e( 'AuthorizeNet', $this->text_domain ) ?>
								</label>
							</p>

						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php
		if( empty( $options['use_paypal']) ):
		//Remember if prevoously set.
		foreach($paypal as $key => $value){
			echo '<input type="hidden" name="paypal[' . $key . ']" value="' . esc_attr($value) .'" />';
		}
		else:
		?>
		<div id="pane_paypal" class="postbox" >
			<h3 class='hndle'><span><?php _e( 'PayPal Settings', $this->text_domain ) ?></span></h3>
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
							<?php $api_url = (empty($paypal['api_url']) ? 'sandbox' : $paypal['api_url'] )?>
							<select id="api_url" name="paypal[api_url]" style="width:100px" >
								<option value="sandbox" <?php selected($api_url == 'sandbox' ); ?>><?php _e( 'Sandbox', $this->text_domain ); ?></option>
								<option value="live"    <?php selected($api_url == 'live' ); ?>><?php _e( 'Live', $this->text_domain ); ?></option>
							</select>
							<br /><span class="description"><?php _e( 'Choose between PayPal Sandbox and PayPal Live.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="business_email"><?php _e( 'PayPal Business Email', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" id="business_email" name="paypal[business_email]" value="<?php echo ( empty( $paypal['business_email'] ) ) ? '' : $paypal['business_email']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'Your PayPal business email for Recurring Payments.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="api_username"><?php _e( 'API Username', $this->text_domain ) ?></label>
						</th>
						<td>
							<p>
								<span class="description">
									<?php _e( 'You must login to PayPal and create an API signature to get your credentials. ', $this->text_domain ) ?>
									<a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&amp;content_ID=developer/e_howto_api_ECAPICredentials" target="_blank"><?php _e( 'Instructions', $this->text_domain ) ?></a>
								</span>
							</p>
							<input type="text" id="api_username" name="paypal[api_username]" value="<?php echo (empty($paypal['api_username']) ) ? '' : $paypal['api_username']; ?>" size="50"/>
							<br /><span class="description"><?php _e( 'Your PayPal API Username.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="api_password"><?php _e( 'API Password', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" id="api_password" name="paypal[api_password]" value="<?php echo (empty($paypal['api_password']) ) ? '' : $paypal['api_password']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'Your PayPal API Password.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="api_signature"><?php _e( 'API Signature', $this->text_domain ) ?></label>
						</th>
						<td>
							<textarea rows="1" cols="55" id="api_signature" name="paypal[api_signature]"><?php echo (empty($paypal['api_signature']) ) ? '' : $paypal['api_signature']; ?></textarea>
							<br /><span class="description"><?php _e( 'Your PayPal API Signature.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="currency"><?php _e( 'Currency', $this->text_domain ) ?></label>
						</th>
						<td>
							<?php $currency = (empty($paypal['currency']) ? 'USD' : $paypal['currency']); ?>
							<select id="currency" name="paypal[currency]" style="width:100px">
								<option value="USD" <?php selected( $currency == 'USD' ); ?>><?php _e( 'U.S. Dollar', $this->text_domain ) ?></option>
								<option value="EUR" <?php selected( $currency == 'EUR' ); ?>><?php _e( 'Euro', $this->text_domain ) ?></option>
								<option value="GBP" <?php selected( $currency == 'GBP' ); ?>><?php _e( 'Pound Sterling', $this->text_domain ) ?></option>
								<option value="CAD" <?php selected( $currency == 'CAD' ); ?>><?php _e( 'Canadian Dollar', $this->text_domain ) ?></option>
								<option value="AUD" <?php selected( $currency == 'AUD' ); ?>><?php _e( 'Australian Dollar', $this->text_domain ) ?></option>
								<option value="JPY" <?php selected( $currency == 'JPY' ); ?>><?php _e( 'Japanese Yen', $this->text_domain ) ?></option>
								<option value="CHF" <?php selected( $currency == 'CHF' ); ?>><?php _e( 'Swiss Franc', $this->text_domain ) ?></option>
								<option value="SGD" <?php selected( $currency == 'SGD' ); ?>><?php _e( 'Singapore Dollar', $this->text_domain ) ?></option>
								<option value="NZD" <?php selected( $currency == 'NZD' ); ?>><?php _e( 'New Zealand Dollar', $this->text_domain ) ?></option>
								<option value="SEK" <?php selected( $currency == 'SEK' ); ?>><?php _e( 'Swedish Krona', $this->text_domain ) ?></option>
								<option value="DKK" <?php selected( $currency == 'DKK' ); ?>><?php _e( 'Danish Krone', $this->text_domain ) ?></option>
								<option value="NOK" <?php selected( $currency == 'NOK' ); ?>><?php _e( 'Norwegian Krone', $this->text_domain ) ?></option>
								<option value="CZK" <?php selected( $currency == 'CZK' ); ?>><?php _e( 'Czech Koruna', $this->text_domain ) ?></option>
								<option value="HUF" <?php selected( $currency == 'HUF' ); ?>><?php _e( 'Hungarian Forint', $this->text_domain ) ?></option>
								<option value="PLN" <?php selected( $currency == 'PLN' ); ?>><?php _e( 'Polish Zloty', $this->text_domain ) ?></option>
							</select>
							<br /><span class="description"><?php _e( 'The currency in which you want to process payments.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="pp_payment_url"><?php _e( 'Redirect URL on Success:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="payapl[payment_url]" id="pp_payment_url" value="<?php echo (empty($paypal['payment_url']) ) ? '' : $paypal['payment_url']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'by default to internal success page', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="pp_cancel_url"><?php _e( 'Redirect URL on Cancel:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="paypal[cancel_url]" id="pp_cancel_url" value="<?php echo (empty($paypal['cancel_url']) ) ? '' : $paypal['cancel_url']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php endif; ?>

		<?php
		if( empty( $options['use_authorizenet']) ):
		//Remember if previously set.
		foreach($authorizenet as $key => $value){
			echo '<input type="hidden" name="authorizenet[' . $key . ']" value="' . esc_attr($value) .'" />';
		}
		else:
		?>
		<!-- **Authorize.Net** -->
		<div id="pane_authorizenet" class="postbox" <?php if( empty($options['use_authorizenet']) ) echo 'style="display: none;"'; ?>>
			<h3 class='hndle'><span><?php _e('Authorize.net AIM Settings', $this->text_domain); ?></span></h3>
			<div class="inside">
				<span class="description"><?php _e('Authorize.net AIM is a customizable payment processing solution that gives the merchant control over all the steps in processing a transaction. An SSL certificate is required to use this gateway. USD is the only currency supported by this gateway.', $this->text_domain) ?></span>
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e('Mode', $this->text_domain) ?></th>
						<td>
							<p>
								<?php $mode = (empty($authorizenet['mode']) ? 'sandbox' : $authorizenet['mode']); ?>
								<select name="authorizenet[mode]"  style="width:100px">
									<option value="sandbox" <?php selected( $mode == 'sandbox') ?>><?php _e('Sandbox', $this->text_domain) ?></option>
									<option value="live" <?php selected( $mode == 'live') ?>><?php _e('Live', $this->text_domain) ?></option>
								</select>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Gateway Credentials', $this->text_domain) ?></th>
						<td>
							<span class="description"><?php print sprintf(__('You must login to Authorize.net merchant dashboard to obtain the API login ID and API transaction key. <a target="_blank" href="%s">Instructions &raquo;</a>', $this->text_domain), "http://www.authorize.net/support/merchant/Integration_Settings/Access_Settings.htm"); ?></span>
							<p>
								<label><?php _e('Login ID', $this->text_domain) ?><br />
									<input value="<?php echo (empty($authorizenet['api_user']) ) ? '' : esc_attr($authorizenet['api_user']); ?>" size="50" name="authorizenet[api_user]" type="text" />
								</label>
							</p>
							<p>
								<label><?php _e('Transaction Key', $this->text_domain) ?><br />
									<input value="<?php echo (empty($authorizenet['api_key']) ) ? '' : esc_attr($authorizenet['api_key']); ?>" size="50" name="authorizenet[api_key]" type="text" />
								</label>
							</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="an_payment_url"><?php _e( 'Redirect URL on Success:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="authorizenet[payment_url]" id="an_payment_url" value="<?php echo (empty($authorizenet['payment_url']) ) ? '' : $authorizenet['payment_url']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'by default to internal success page', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="an_cancel_url"><?php _e( 'Redirect URL on Cancel:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="authorizenet[cancel_url]" id="an_cancel_url" value="<?php echo (empty($authorizenet['cancel_url']) ) ? '' : $authorizenet['cancel_url']; ?>" size="50" />
							<br /><span class="description"><?php _e( 'by default to Home page', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Advanced Settings', $this->text_domain) ?></th>
						<td>
							<span class="description"><?php _e('Optional settings to control advanced options', $this->text_domain) ?></span>
							<!--
							<p>
							<label><a title="<?php _e('Authorize.net default is \',\'. Otherwise, get this from your credit card processor. If the transactions are not going through, this character is most likely wrong.', $this->text_domain); ?>"><?php _e('Delimiter Character', $this->text_domain); ?></a><br />
							<input value="<?php echo (empty($authorizenet['delim_char']))?",":esc_attr($authorizenet['delim_char']); ?>" size="2" name="authorizenet[delim_char]" type="text" />
							</label>
							</p>

							<p>
							<label><a title="<?php _e('Authorize.net default is blank. Otherwise, get this from your credit card processor. If the transactions are going through, but getting strange responses, this character is most likely wrong.', $this->text_domain); ?>"><?php _e('Encapsulation Character', $this->text_domain); ?></a><br />
							<input value="<?php echo (empty($authorizenet['encap_char']) ) ? '' : esc_attr($authorizenet['encap_char']); ?>" size="2" name="authorizenet[encap_char]" type="text" />
							</label>
							</p>
							-->
							<p>
								<label><?php _e('Email Customer (on success):', $this->text_domain); ?><br />
									<?php $email_customer = (empty($authorizenet['email_customer']) ? '' : $authorizenet['email_customer']); ?>
									<select name="authorizenet[email_customer]" style="width:100px">
										<option value="yes" <?php selected($email_customer == 'yes') ?>><?php _e('Yes', $this->text_domain) ?></option>
										<option value="no" <?php selected($email_customer == 'no') ?>><?php _e('No', $this->text_domain) ?></option>
									</select>
								</label>
							</p>

							<p>
								<label><a title="<?php _e('This text will appear as the header of the email receipt sent to the customer.', $this->text_domain); ?>"><?php _e('Customer Receipt Email Header', $this->text_domain); ?></a><br/>
									<input value="<?php echo empty($authorizenet['header_email_receipt'])?__('Thanks for your payment!', $this->text_domain):esc_attr($authorizenet['header_email_receipt']); ?>" size="50" name="authorizenet[header_email_receipt]" type="text" />
								</label>
							</p>

							<p>
								<label><a title="<?php _e('This text will appear as the footer on the email receipt sent to the customer.', $this->text_domain); ?>"><?php _e('Customer Receipt Email Footer', $this->text_domain); ?></a><br/>
									<input value="<?php echo empty($authorizenet['footer_email_receipt']) ? '' : esc_attr($authorizenet['footer_email_receipt']); ?>" size="50" name="authorizenet[footer_email_receipt]" type="text" />
								</label>
							</p>

							<p>
								<label><a title="<?php _e('The payment gateway generated MD5 hash value that can be used to authenticate the transaction response. Not needed because responses are returned using an SSL connection.', $this->text_domain); ?>"><?php _e('Security: MD5 Hash', $this->text_domain); ?></a><br/>
									<input value="<?php echo (empty($authorizenet['md5_hash']) ) ? '' : esc_attr($authorizenet['md5_hash']); ?>" size="50" name="authorizenet[md5_hash]" type="text" />
								</label>
							</p>

							<p>
								<label><a title="<?php _e('Request a delimited response from the payment gateway.', $this->text_domain); ?>"><?php _e('Delim Data:', $this->text_domain); ?></a><br/>
									<?php $delim_data = (empty($authorizenet['delim_data']) ? '' : $authorizenet['delim_data']); ?>
									<select name="authorizenet[delim_data]" style="width:100px">
										<option value="yes" <?php selected($delim_data == 'yes') ?>><?php _e('Yes', $this->text_domain) ?></option>
										<option value="no" <?php selected($delim_data == 'no') ?>><?php _e('No', $this->text_domain) ?></option>
									</select>
								</label>
							</p>
							<!--
							<p>
							<label><a title="<?php _e('Many other gateways have Authorize.net API emulators. To use one of these gateways input their API post url here.', $this->text_domain); ?>"><?php _e('Custom API URL', $this->text_domain) ?></a><br />
							<input value="<?php echo (empty($authorizenet['custom_api']) ) ? '' : esc_attr($authorizenet['custom_api']); ?>" size="50" name="authorizenet[custom_api]" type="text" />
							</label>
							</p>
							-->
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php endif; ?>

		<?php wp_nonce_field('verify'); ?>
		<input type="hidden" name="key" value="payment_types" />
		<p class="submit">
			<input type="submit" class="button-primary" id="save" name="save" value="<?php _e( 'Save Changes', $this->text_domain ); ?>">
		</p>


	</form>
</div>
