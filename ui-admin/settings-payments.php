<?php if (!defined('ABSPATH')) die('No direct access allowed!'); 

$options = $this->get_options('payments');

?>

<!--
<script type="text/javascript">
(function($) {
	$( document ).ready( function() {

		$("#recurring_table input" ).prop( "readonly" , ! $( "#enable_recurring" ).prop( "checked" ) );
		$("#one_time_table input" ).prop( "readonly" , ! $( "#enable_one_time" ).prop( "checked" ) );
		$("#credits_table input" ).prop( "readonly" , ! $( "#enable_credits" ).prop( "checked" ) );

		$( "#enable_recurring" ).change( function () {$("#recurring_table input" ).prop( "readonly" , ! $( "#enable_recurring" ).prop( "checked" ) ); });
		$( "#enable_one_time" ).change( function () {$("#one_time_table input" ).prop( "readonly" , ! $( "#enable_one_time" ).prop( "checked" ) ); });
		$( "#enable_credits" ).change( function () {$("#credits_table input, #credits_table textarea" ).prop( "readonly" , ! $( "#enable_credits" ).prop( "checked" ) ); });

		$( "#payment_settings" ).submit( function () {
			if ( $( "#enable_recurring" ).prop( "checked" ) 
			|| $( "#enable_one_time").prop( "checked" )
			|| $( "#enable_credits" ).prop( "checked" ) ) {
				return true;
			} else {
				$( "#enable_recurring_tr" ).css( "background-color", "#FFEBE8" );
				$( "#enable_recurring" ).focus();
				$( "#one_time_cost_tr" ).css( "background-color", "#FFEBE8" );
				$( "#credits_tr" ).css( "background-color", "#FFEBE8" );
			}
			return false;
		});
	});
	
})(jQuery);
</script>
-->

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'payments' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Payments Settings', $this->text_domain ); ?></h1>
	<p class="description">
		<?php _e( 'Here you can set price that users should pay for capability the creation listings on your site. If you want that users can create listings for free, select "Free Listings" on the "Payments Type" page.', $this->text_domain ) ?>
	</p>

	<br />
	<form method="post" class="dp-payments" id="payment_settings" action="#">
		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Recurring Payments', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table" id="recurring_table">
					<tr id="enable_recurring_tr">
						<th>
							<label for "enable_recurring"><?php _e( 'Enable Recurring Payments', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="checkbox" id="enable_recurring" name="enable_recurring" value="1" <?php checked( ! empty($options['enable_recurring'] ) ); ?> />
							<label for="enable_recurring"><?php _e('Use recurring payments', $this->text_domain) ?></label>
						</td>
					</tr>
					<tr>
						<th>
							<label for="recurring_cost"><?php _e('Cost of Service', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" class="small-text" id="recurring_cost" name="recurring_cost" value="<?php echo ( empty( $options['recurring_cost'] ) ) ? '0.00' : $options['recurring_cost']; ?>" />
							<span class="description"><?php _e('Amount to bill for each billing cycle.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="recurring_name"><?php _e('Name of Service', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" name="recurring_name" id="recurring_name" value="<?php echo ( empty( $options['recurring_name'] ) ) ? '' : $options['recurring_name']; ?>" />
							<span class="description"><?php _e('Name of the service.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="billing_period"><?php _e('Billing Period', $this->text_domain) ?></label>
						</th>
						<td>
							<select id="billing_period" name="billing_period"  >
								<option value="Day" <?php selected( isset( $options['billing_period'] ) && $options['billing_period'] == 'Day' ); ?>><?php _e( 'Day', $this->text_domain ); ?></option>
								<option value="Week" <?php selected( isset( $options['billing_period'] ) && $options['billing_period'] == 'Week' ); ?>><?php _e( 'Week', $this->text_domain ); ?></option>
<!--
								<option value="SemiMonth" <?php selected( isset( $options['billing_period'] ) && $options['billing_period'] == 'SemiMonth' ); ?>><?php _e( 'SemiMonth', $this->text_domain ); ?></option>
-->		
								<option value="Month" <?php selected( isset( $options['billing_period'] ) && $options['billing_period'] == 'Month' ); ?>><?php _e( 'Month', $this->text_domain ); ?></option>
								<option value="Year" <?php selected( isset( $options['billing_period'] ) && $options['billing_period'] == 'Year' ); ?>><?php _e( 'Year', $this->text_domain ); ?></option>
							</select>
							<span class="description"><?php _e('The unit of measure for the billing cycle.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="billing_frequency"><?php _e('Billing Frequency', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" class="small-text" id="billing_frequency" name="billing_frequency" value="<?php echo ( empty( $options['billing_frequency'] ) ) ? '0' : $options['billing_frequency']; ?>" />
							<span class="description"><?php _e('Number of billing periods that make up one billing cycle. The combination of billing frequency and billing period must be less than or equal to one year.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="billing_agreement"><?php _e('Billing Agreement', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" name="billing_agreement" id="billing_agreement" size="100" value="<?php echo ( empty( $options['billing_agreement'] ) ) ? '' : $options['billing_agreement']; ?>" />
							<br /><span class="description"><?php _e('The description of the goods or services associated with that billing agreement. PayPal recommends that the description contain a brief summary of the billing agreement terms and conditions. For example, customer will be billed at "$9.99 per month for 2 years."', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>


		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'One-time Payments', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table" id="one_time_table">
					<tr id="one_time_cost_tr">
						<th>
							<label for"enable_one_time"><?php _e( 'Enable One-time Payments', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="checkbox" id="enable_one_time" name="enable_one_time" value="1" <?php checked( ! empty( $options['enable_one_time'] )); ?> />
							<label for="enable_one_time"><?php _e('Use one-time payments', $this->text_domain) ?></label>
						</td>
					</tr>
					<tr>
						<th>
							<label for="one_time_cost"><?php _e('Cost of Service', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" id="one_time_cost" name="one_time_cost" value="<?php echo  (empty( $options['one_time_cost'] ) ) ? '0.00' : $options['one_time_cost']; ?>" />
							<span class="description"><?php _e('Cost of service.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="one_time_name"><?php _e('Name of Service', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="text" name="one_time_name" id="one_time_name" value="<?php echo ( empty( $options['one_time_name'] ) ) ? '' : $options['one_time_name']; ?>" />
							<span class="description"><?php _e('Name of the service.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>


		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Use Credits', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table" id="credits_table">
					<tr id="credits_tr">
						<th><label for="enable_credits"><?php _e( 'Enable Credits', $this->text_domain ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="enable_credits" name="enable_credits" value="1" <?php checked( ! empty( $options['enable_credits'] ) );  ?> />
								<?php _e( 'Enable credits for publishing a Listing.', $this->text_domain ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="cost_credit"><?php _e( 'Cost Per Credit', $this->text_domain ); ?></label></th>
						<td>
							<input type="text" id="cost_credit" name="cost_credit" value="<?php echo ( empty( $options['cost_credit'] ) ) ? '0.00' : $options['cost_credit']; ?>" class="small-text" />
							<span class="description"><?php _e( 'How much a credit should cost.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="credits_per_week"><?php _e( 'Credits Per Listing', $this->text_domain ); ?></label></th>
						<td>
							<input type="text" id="credits_per_listing" name="credits_per_listing" value="<?php echo ( empty( $options['credits_per_listing'] ) ) ? '0' : $options['credits_per_listing']; ?>" class="small-text" />
							<span class="description"><?php _e( 'How many credits you need to publish a Listing.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="signup_credits"><?php _e( 'Signup Credits', $this->text_domain ); ?></label></th>
						<td>
							<input type="text" id="signup_credits" name="signup_credits" value="<?php echo ( empty( $options['signup_credits'] ) ) ? '0' : $options['signup_credits']; ?>" class="small-text" />
							<span class="description"><?php _e( 'How many credits a user should receive for signing up.', $this->text_domain ); ?></span>
						</td>
					</tr>
					<tr>
						<th><label for="credits_description"><?php _e( 'Description', $this->text_domain ); ?></label></th>
						<td>
							<textarea id="credits_description" name="credits_description" rows="1" cols="55"><?php echo ( empty( $options['credits_description'] ) ) ? '' : sanitize_text_field($options['credits_description']); ?></textarea>
							<br />
							<span class="description"><?php _e( 'Description of the costs and durations associated with publishing an ad. Will be displayed in the admin area.', $this->text_domain ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Terms of Service', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="tos_content"><?php _e('Terms of Service', $this->text_domain) ?></label>
						</th>
						<td>
							<textarea name="tos_content" id="tos_content" rows="15" cols="125"><?php echo ( empty( $options['tos_content'] ) ) ? '' : sanitize_text_field($options['tos_content']); ?></textarea>
							<br />
							<span class="description"><?php _e('Text for "Terms of Service"'); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="payments" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>
</div>
