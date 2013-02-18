<?php

/**
* The template for displaying the Checkout page.
* You can override this file in your active theme.
*
* @package Payments
* @version 1.0.0
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

global $current_user, $wp_query;

$current_user = wp_get_current_user();

$step        = get_query_var('checkout_step') ? get_query_var('checkout_step') : null;
$options     = get_option( DR_OPTIONS_NAME );
$opset       = $options['payments'];
$oppaypal		 = $options['payment_types']['paypal'];
$opauthnet	 = $options['payment_types']['authorizenet'];

$error       = get_query_var('checkout_error');

?>

<h2 class="entry-title">
	<?php _e('Signup', DR_TEXT_DOMAIN ); ?>
	<?php if ( $step == 'free' ) echo ' ( step 1 of 1 ) '; ?>
	<?php if ( $step == 'terms' ) echo ' ( step 1 of 3 ) '; ?>
	<?php if ( $step == 'payment_method' ) echo ' ( step 2 of 3 ) '; ?>
	<?php if ( $step == 'recurring_payment' ) echo ' ( step 3 of 3 ) '; ?>
	<?php if ( $step == 'confirm_payment' ) echo ' ( step 3 of 3 ) '; ?>
	<?php if ( $step == 'cc_details' ) echo ' ( step 3 of 3 ) '; ?>
	<?php if ( $step == 'success' ) echo ' ( complete ) '; ?>
	<?php if ( $step == 'free_success' ) echo ' ( complete ) '; ?>
	<?php if ( $step == 'api_call_error' ) echo ' ( error ) '; ?>
</h2>

<ul id="error_msg_box">
	<?php if ( $error ): ?>
	<li><?php echo $error['error_long_msg']; ?></li>
	<?php endif; ?>
</ul>

<script language="JavaScript">
	(function($) {
		$( document ).ready( function() {
			$('#confirm_payment').validate();
		});
	})(jQuery);
</script>

<?php if ( $step == 'disabled' ): ?>

<?php _e( 'This feature is currently disabled by the system administrator.', $this->text_domain ); ?>

<?php elseif ( $step == 'terms' || $step == null ): ?>

<form action="#" method="post"  class="dr-checkout">
	<strong><?php _e( 'Cost of Service', $this->text_domain ); ?></strong>
	<table id="billing-type" class="form-table">
		<?php if($this->use_credits && ! $this->is_full_access() ): ?>
		<tr>
			<th <?php do_action( 'billing_invalid' ); ?>><label for="billing"><?php _e( 'Buy Credits', $this->text_domain ) ?></label></th>
			<td>
				<input type="radio" name="billing_type" value="credits" checked="checked" />
				<select name="credits">
					<?php
					for ( $i = 1; $i <= 10; $i++ ):
					$credits = 10 * $i;
					$amount = $credits * $options['payments']['cost_credit'];
					?>
					<option value="<?php echo $credits; ?>" <?php selected(! empty($_POST['credits_cost'] ) && $_POST['credits_cost'] == $amount ); ?> >
						<?php echo $credits; ?> Credits for <?php echo sprintf( "%01.2f", $amount) . ' ' . $options['payment_types']['paypal']['currency']; ?>
					</option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<?php endif; ?>
		<?php if ( ! empty( $opset['enable_recurring'] ) ) : ?>
		<tr>
			<th <?php do_action( 'billing_invalid' ); ?>>

			<label for="type_recurring"><?php echo (empty( $opset['recurring_name'] ) ) ? '' :$opset['recurring_name']; ?></label></th>
				<td>
					<input type="radio" name="billing_type" id="type_recurring" value="recurring" <?php checked( ! empty($_POST['billing_type'] ) && $_POST['billing_type'] == 'recurring' ); ?> />
					<span>
						<?php
						$bastr    = empty( $opset['recurring_cost'] ) ? '' : $opset['recurring_cost'] . ' ';
						$bastr .= $oppaypal['currency'];
						$bastr .= __( ' per ', $this->text_domain );
						$bastr .= ( ! empty( $opset['billing_frequency'] ) && $opset['billing_frequency'] != 1 ) ? $opset['billing_frequency'] . ' ' : '';
						$bastr .= empty( $opset['billing_period'] ) ? '' : $opset['billing_period'];
						echo $bastr;
						?>
					</span>
					<input type="hidden" name="recurring_cost" value="<?php echo ( empty( $opset['recurring_cost'] ) ) ? '0' : $opset['recurring_cost']; ?>" />
					<input type="hidden" name="billing_agreement" value="<?php echo ( empty( $opset['billing_agreement'] ) ) ? '' : $opset['billing_agreement']; ?>" />
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( ! empty( $opset['enable_one_time'] ) ) : ?>
			<tr>
				<th <?php do_action( 'billing_invalid' ); ?>>
				<label for="type_one_time"><?php echo ( empty( $opset['one_time_name'] ) ) ? '' : $opset['one_time_name']; ?></label></th>
					<td>
						<input type="radio" name="billing_type" id="type_one_time"  value="one_time" <?php checked( ! empty( $_POST['billing_type'] ) && $_POST['billing_type'] == 'one_time' ); ?> /> <?php echo ( empty( $opset['one_time_cost'] ) ) ? '' : $opset['one_time_cost']; ?> <?php echo $oppaypal['currency']; ?>
						<input type="hidden" name="one_time_cost" value="<?php echo ( empty( $opset['one_time_cost'] ) ) ? '' : $opset['one_time_cost']; ?>" />
					</td>
				</tr>
				<?php endif; ?>
			</table>
			<br />

			<?php if(! empty($opset['tos_content'])): ?>
			<strong><?php _e( 'Terms of Service', $this->text_domain ); ?></strong>
			<table id="tos">
				<tr>
					<td>
						<div class="terms">
							<?php
							if ( ! empty( $opset['tos_content'] ) )
							echo nl2br( $opset['tos_content'] );
							?>
						</div>
					</td>
				</tr>
			</table>
			<br />


			<table id="tos-agree" >
				<tr>
					<td <?php do_action( 'tos_invalid' ); ?> >
						<label for="tos_agree">
							<input type="checkbox" id="tos_agree" name="tos_agree" value="1" <?php checked( ! empty( $_POST['tos_agree'] ) ); ?>  />
							<?php _e( 'I agree with the Terms of Service', $this->text_domain ); ?>
						</label>
					</td>
				</tr>
			</table>

			<?php else: ?>
			<input type="hidden" id="tos_agree" name="tos_agree" value="1" />
			<?php endif; ?>

			<div class="submit">
				<input type="submit" name="terms_submit" value="<?php _e( 'Continue', $this->text_domain ); ?>" />
			</div>
		</form>

		<?php if ( !empty( $error ) ): ?>
		<div class="invalid-login"><?php echo $error; ?></div>
		<?php endif; ?>
		<!-- End Terms -->










		<?php elseif( $step == 'payment_method' ): ?>
		<!-- Begin Payment Method -->

		<?php if( $this->use_free ): ?>
		<strong><?php _e( 'Posting Listings is Free when Logged In' ); ?></strong>
		<?php else: ?>
		<form action="#" method="post"  class="checkout">

			<strong><?php _e('Choose Payment Method', $this->text_domain ); ?></strong>

			<table class="form-table">
				<?php if( $this->use_paypal ): ?>
				<tr>
					<th <?php do_action('pm_invalid'); ?>>

						<label for="pmethod_paypal"><?php _e( 'PayPal', $this->text_domain ); ?></label>
					</th>
					<td>
						<input type="radio" name="payment_method" id="pmethod_paypal" value="paypal"/>
						<img  src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" border="0" alt="Acceptance Mark">
					</td>
				</tr>
				<?php endif; ?>
				<?php if( $this->use_authorizenet ): ?>
				<tr>
					<th <?php do_action('pm_invalid'); ?>>
						<label for="pmethod_cc"><?php _e( 'Credit Card', $this->text_domain ); ?></label>
					</th>
					<td>
						<input type="radio" name="payment_method" id="pmethod_cc" value="cc" />
						<img  src="<?php echo $this->plugin_url; ?>ui-front/images/cc-logos-small.jpg" border="0" alt="Solution Graphics">
					</td>
				</tr>
				<?php endif; ?>

			</table>

			<div class="submit">
				<input type="submit" name="payment_method_submit" value="<?php _e( 'Continue', $this->text_domain ); ?>" />
			</div>
		</form>
		<?php endif; ?>
		<!--End Payment Method -->









		<?php elseif ( $step == 'cc_details' ): ?>
		<!--Begin CC Details -->

		<?php
		$countries = array (
		"" => "Select One",
		"US" => "United States",
		"CA" => "Canada",
		"-" => "----------",
		"AF" => "Afghanistan",
		"AL" => "Albania",
		"DZ" => "Algeria",
		"AS" => "American Samoa",
		"AD" => "Andorra",
		"AO" => "Angola",
		"AI" => "Anguilla",
		"AQ" => "Antarctica",
		"AG" => "Antigua and Barbuda",
		"AR" => "Argentina",
		"AM" => "Armenia",
		"AW" => "Aruba",
		"AU" => "Australia",
		"AT" => "Austria",
		"AZ" => "Azerbaidjan",
		"BS" => "Bahamas",
		"BH" => "Bahrain",
		"BD" => "Bangladesh",
		"BB" => "Barbados",
		"BY" => "Belarus",
		"BE" => "Belgium",
		"BZ" => "Belize",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BT" => "Bhutan",
		"BO" => "Bolivia",
		"BA" => "Bosnia-Herzegovina",
		"BW" => "Botswana",
		"BV" => "Bouvet Island",
		"BR" => "Brazil",
		"IO" => "British Indian Ocean Territory",
		"BN" => "Brunei Darussalam",
		"BG" => "Bulgaria",
		"BF" => "Burkina Faso",
		"BI" => "Burundi",
		"KH" => "Cambodia",
		"CM" => "Cameroon",
		"CV" => "Cape Verde",
		"KY" => "Cayman Islands",
		"CF" => "Central African Republic",
		"TD" => "Chad",
		"CL" => "Chile",
		"CN" => "China",
		"CX" => "Christmas Island",
		"CC" => "Cocos (Keeling) Islands",
		"CO" => "Colombia",
		"KM" => "Comoros",
		"CG" => "Congo",
		"CK" => "Cook Islands",
		"CR" => "Costa Rica",
		"HR" => "Croatia",
		"CU" => "Cuba",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DK" => "Denmark",
		"DJ" => "Djibouti",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"TP" => "East Timor",
		"EC" => "Ecuador",
		"EG" => "Egypt",
		"SV" => "El Salvador",
		"GQ" => "Equatorial Guinea",
		"ER" => "Eritrea",
		"EE" => "Estonia",
		"ET" => "Ethiopia",
		"FK" => "Falkland Islands",
		"FO" => "Faroe Islands",
		"FJ" => "Fiji",
		"FI" => "Finland",
		"CS" => "Former Czechoslovakia",
		"SU" => "Former USSR",
		"FR" => "France",
		"FX" => "France (European Territory)",
		"GF" => "French Guyana",
		"TF" => "French Southern Territories",
		"GA" => "Gabon",
		"GM" => "Gambia",
		"GE" => "Georgia",
		"DE" => "Germany",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GB" => "Great Britain",
		"GR" => "Greece",
		"GL" => "Greenland",
		"GD" => "Grenada",
		"GP" => "Guadeloupe (French)",
		"GU" => "Guam (USA)",
		"GT" => "Guatemala",
		"GN" => "Guinea",
		"GW" => "Guinea Bissau",
		"GY" => "Guyana",
		"HT" => "Haiti",
		"HM" => "Heard and McDonald Islands",
		"HN" => "Honduras",
		"HK" => "Hong Kong",
		"HU" => "Hungary",
		"IS" => "Iceland",
		"IN" => "India",
		"ID" => "Indonesia",
		"INT" => "International",
		"IR" => "Iran",
		"IQ" => "Iraq",
		"IE" => "Ireland",
		"IL" => "Israel",
		"IT" => "Italy",
		"CI" => "Ivory Coast (Cote D&#39;Ivoire)",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Laos",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macau",
		"MK" => "Macedonia",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique (French)",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"MX" => "Mexico",
		"FM" => "Micronesia",
		"MD" => "Moldavia",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"AN" => "Netherlands Antilles",
		"NT" => "Neutral Zone",
		"NC" => "New Caledonia (French)",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"KP" => "North Korea",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PA" => "Panama",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn Island",
		"PL" => "Poland",
		"PF" => "Polynesia (French)",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RE" => "Reunion (French)",
		"RO" => "Romania",
		"RU" => "Russian Federation",
		"RW" => "Rwanda",
		"GS" => "S. Georgia & S. Sandwich Isls.",
		"SH" => "Saint Helena",
		"KN" => "Saint Kitts & Nevis Anguilla",
		"LC" => "Saint Lucia",
		"PM" => "Saint Pierre and Miquelon",
		"ST" => "Saint Tome (Sao Tome) and Principe",
		"VC" => "Saint Vincent & Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SK" => "Slovak Republic",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"KR" => "South Korea",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen Islands",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syria",
		"TJ" => "Tadjikistan",
		"TW" => "Taiwan",
		"TZ" => "Tanzania",
		"TH" => "Thailand",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"UY" => "Uruguay",
		"MIL" => "USA Military",
		"UM" => "USA Minor Outlying Islands",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VA" => "Vatican City State",
		"VE" => "Venezuela",
		"VN" => "Vietnam",
		"VG" => "Virgin Islands (British)",
		"VI" => "Virgin Islands (USA)",
		"WF" => "Wallis and Futuna Islands",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"YU" => "Yugoslavia",
		"ZR" => "Zaire",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe",
		);

		$details = get_query_var('details');


		if ( ! empty($details['confirm_error']) ):
		?>
		<span style="color: red;"><?php echo $details['confirm_error']; ?></span>
		<div class="clear"></div>
		<br clear="all" />
		<?php endif; ?>

		<form action="#" method="post" class="checkout" id="confirm_payment">

			<strong><?php _e( 'Payment Details', $this->text_domain ); ?></strong>
			<div class="clear"></div>
			<table id="cc-user-details" class="form-table">
				<tr>
					<td><label for="cc_email"><?php _e( 'Email Address for Credit Card', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="cc_email" name="cc_email" value="<?php echo empty($current_user->cc_email) ? esc_attr($current_user->user_email) : esc_attr($current_user->cc_email); ?>" class="required email" /></td>
				</tr>
				<tr>
					<td><label for="first-name"><?php _e( 'First Name', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="first-name" name="cc_firstname" value="<?php echo empty($current_user->cc_firstname) ? esc_attr($current_user->first_name) : esc_attr($current_user->cc_firstname); ?>" class="required"  /></td>
				</tr>
				<tr>
					<td><label for="last-name"><?php _e( 'Last Name', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="last-name" name="cc_lastname" value="<?php echo empty($current_user->cc_lastname) ? esc_attr($current_user->last_name) : esc_attr($current_user->cc_lastname); ?>" class="required"  /></td>
				</tr>
				<tr>
					<td><label for="street"><?php _e( 'Street', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="street" name="cc_street" value="<?php echo empty($current_user->cc_street) ? '' : esc_attr($current_user->cc_street); ?>" class="required" /></td>
				</tr>
				<tr>
					<td><label for="city"><?php _e( 'City', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="city" name="cc_city" value="<?php echo empty($current_user->cc_city) ? '' : esc_attr($current_user->cc_city); ?>" class="required" /></td>
				</tr>
				<tr>
					<td><label for="state"><?php _e( 'State', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="state" name="cc_state" value="<?php echo empty($current_user->cc_state) ? '' : esc_attr($current_user->cc_state); ?>" class="required" /></td>
				</tr>
				<tr>
					<td><label for="zip"><?php _e( 'Postal Code', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="zip" name="cc_zip" value="<?php echo empty($current_user->cc_zip) ? '' : esc_attr($current_user->cc_zip); ?>" class="required" /></td>
				</tr>
				<tr>
					<td><label for="country"><?php _e( 'Country', $this->text_domain ); ?>:</label></td>
					<td>
						<select id="country" name="cc_country_code"  class="required">
							<?php foreach ( $countries as $key => $value ) : ?>
							<option value="<?php echo $key; ?>" <?php selected( ! empty( $current_user->cc_country_code ) && $key == $current_user->cc_country_code ); ?>  ><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<?php if(! $this->use_free): ?>

				<tr>
					<td><?php _e( 'Total Amount', $this->text_domain ); ?>:</td>
					<td>
						<strong><?php echo $_SESSION['cost']; ?> <?php echo (empty($oppaypal['currency']) ) ? 'USD' : $oppaypal['currency']; ?></strong>
						<input type="hidden" name="total_amount" value="<?php echo $_SESSION['cost']; ?>" />
					</td>
				</tr>

				<tr>
					<td><label for="cc_type"><?php _e( 'Credit Card Type', $this->text_domain ); ?>:</label></td>
					<td>
						<select name="cc_type">
							<option><?php _e( 'Visa', $this->text_domain ); ?></option>
							<option><?php _e( 'MasterCard', $this->text_domain ); ?></option>
							<option><?php _e( 'Amex', $this->text_domain ); ?></option>
							<option><?php _e( 'Discover', $this->text_domain ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cc_number"><?php _e( 'Credit Card Number', $this->text_domain ); ?>:</label></td>
					<td><input type="text" name="cc_number" class="required creditcard"/></td>
				</tr>
				<tr>
					<td><label for="exp_date"><?php _e( 'Expiration Date', $this->text_domain ); ?>:</label></td>
					<td>
						<select name="exp_date_month" id="exp_date" class="required" >
							<option value="01"><?php _e('01 Jan', $this->text_domain); ?></option>
							<option value="02"><?php _e('02 Feb', $this->text_domain); ?></option>
							<option value="03"><?php _e('03 Mar', $this->text_domain); ?></option>
							<option value="04"><?php _e('04 Apr', $this->text_domain); ?></option>
							<option value="05"><?php _e('05 May', $this->text_domain); ?></option>
							<option value="06"><?php _e('06 Jun', $this->text_domain); ?></option>
							<option value="07"><?php _e('07 Jul', $this->text_domain); ?></option>
							<option value="08"><?php _e('08 Aug', $this->text_domain); ?></option>
							<option value="09"><?php _e('09 Sep', $this->text_domain); ?></option>
							<option value="10"><?php _e('10 Oct', $this->text_domain); ?></option>
							<option value="11"><?php _e('11 Nov', $this->text_domain); ?></option>
							<option value="12"><?php _e('12 Dec', $this->text_domain); ?></option>
						</select>

						<select name="exp_date_year" class="required" >
							<?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; $i++ ) { ?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<!--
				<tr>
				<td><label for="exp_date"><?php _e( 'Expiration Date (mm/yy)', $this->text_domain ); ?>:</label></td>
				<td><input type="text" name="exp_date" class="required" /></td>
				</tr>
				-->
				<tr>
					<td><label for="cvv2"><?php _e( 'CVV2', $this->text_domain ); ?>:</label></td>
					<td><input type="text" name="cvv2" class="required" /></td>
				</tr>
				<?php endif; ?>

			</table>

			<div class="clear"></div>
			<div class="submit">
				<input type="submit" name="direct_payment_submit" value="Continue" />
			</div>

		</form>
		<!-- End CC Details -->




		<?php elseif ( $step == 'confirm_payment' ): ?>
		<!-- Confirm -->
		<form action="" method="post" class="checkout">
			<?php

			unset($_POST['direct_payment_submit']); //don't pass it again

			$cc = $_SESSION['CC'];

			foreach($cc as $key => $value) :
			?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value;?>" />
			<?php endforeach; ?>

			<input type="hidden" name="billing_type" value="<?php echo $_SESSION['billing_type']; ?>" />
			<input type="hidden" name="credits" value="<?php echo (empty($_SESSION['credits']) ) ? 0 : $_SESSION['credits']; ?>" />


			<strong><?php _e( 'Confirm Payment', $this->text_domain ); ?></strong>
			<table>
				<tr>
					<td><label><?php _e( 'Email Address', $this->text_domain ); ?>:</label></td>
					<td><?php echo $cc['cc_email']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Name', $this->text_domain ); ?>:</label></td>
					<td><?php echo $cc['cc_firstname']; ?> <?php echo $cc['cc_lastname']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Address', $this->text_domain ); ?>:</label></td>
					<td>
						<?php echo trim( sprintf( __ ( '%1$s, %2$s, %3$s %4$s %5$s', $this->text_domain), $cc['cc_street'], $cc['cc_city'], $cc['cc_state'], $cc['cc_zip'], $cc['cc_country_code']), ', ') ; ?>
					</td>
				</tr>

				<?php if ( $_SESSION['billing_type'] == 'recurring' ): ?>
				<tr>
					<td><label><?php _e( 'Billing Agreement', $this->text_domain ); ?>:</label></td>
					<td><?php echo $_SESSION['billing_agreement']; ?></td>
				</tr>
				<?php endif; ?>
				<tr>
					<td><label><?php _e('Total Amount', $this->text_domain); ?>:</label></td>
					<td>
						<strong><?php echo $cc['total_amount']; ?> <?php echo (empty($cc['currency_code']) ) ? 'USD' : $oppaypal['currency_code']; ?></strong>
					</td>
				</tr>
			</table>

			<div class="submit">
				<input type="submit" name="confirm_payment_submit" value="Confirm Payment" />
			</div>

		</form>
		<!--End Confirm-->






		<?php elseif ( $step == 'api_call_error' ): ?>
		<!--Begin Call Error -->

		<ul>
			<li><?php echo $error['error_call'] . ' API call failed.'; ?></li>
			<li><?php echo 'Detailed Error Message: ' . $error['error_long_msg']; ?></li>
			<li><?php echo 'Short Error Message: '    . $error['error_short_msg']; ?></li>
			<li><?php echo 'Error Code: '             . $error['error_code']; ?></li>
			<li><?php echo 'Error Severity Code: '    . $error['error_severity_code']; ?></li>
		</ul>
		<!-- End Call Error-->

		
		
<?php /* Success */ ?>
		<?php elseif ( $step == 'success' ): ?>
		<!--Begin Success -->

		<div class="dp-submit-txt"><?php _e( 'Thank you for your business. Transaction processed successfully!', $this->text_domain ); ?></div>
		<span class="dp-submit-txt"><?php _e( 'You can go to your profile and review/change your personal information, or you can go straight to the directory listing submission page.', $this->text_domain ); ?></span>
		<br />

		<?php echo do_shortcode('[dr_add_listing_btn]'); ?>
		<?php echo do_shortcode('[dr_profile_btn]'); ?>
		<br class="clear" />
		<!--End Success -->






		<?php /* Free Success */ ?>
		<?php elseif ( $step == 'free_success' ): ?>

		<div class="dp-submit-txt"><?php _e( 'The registration is completed successfully!', $this->text_domain ); ?></div>
		<span class="dp-submit-txt"><?php _e( 'You can go to your profile and review/change your personal information, or you can go straight to the directory listing submission page.', $this->text_domain ); ?></span>
		<br />

		<?php echo do_shortcode('[dr_my_listings_btn text="' . __('Proceed to your Listings', $this->text_domain) . '" view="always"]'); ?>


		<form id="go-to-profile-su" action="#" method="post">
			<input type="submit" name="redirect_profile" value="Go To Profile" />
		</form>
		<br class="clear" />


		<?php /* Recurring payment */ ?>
		<?php elseif ( $step == 'recurring_payment' ): ?>

		<?php $transaction_details = get_query_var('checkout_transaction_details'); ?>

		<form action="" method="post" class="checkout">
			<?php

			unset($_POST['payment_method_submit']); //don't pass it again

			$cc = $_SESSION['CC'];
			foreach($cc as $key => $value) :
			?>
			<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value;?>" />
			<?php endforeach; ?>

			<input type="hidden" name="billing_type" value="<?php echo $_SESSION['billing_type']; ?>" />
			<input type="hidden" name="credits" value="<?php echo empty($_SESSION['credits']) ? 0 : $_SESSION['credits']; ?>" />


			<strong><?php _e( 'Confirm Payment', $this-->text_domain ); ?></strong>
			<table>
				<tr>
					<td><label><?php _e( 'Email Address', $this->text_domain ); ?>:</label></td>
					<td><?php echo empty($cc['cc_email']) ? $current_user->user_email : $cc['cc_email']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Name', $this->text_domain ); ?>:</label></td>
					<td><?php echo empty($cc['cc_firstname']) ? $current_user->first_name : $cc['cc_firstname']; ?> <?php echo empty($cc['cc_lastname']) ? $current_user->last_name : $cc['cc_lastname']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Address', $this->text_domain ); ?>:</label></td>
					<td>
						<?php echo trim( sprintf( __ ( '%1$s, %2$s, %3$s %4$s %5$s', $this->text_domain), $cc['cc_street'], $cc['cc_city'], $cc['cc_state'], $cc['cc_zip'], $cc['cc_country_code']), ', ') ; ?>
					</td>
				</tr>
				<tr>
					<td><label><?php _e('Total Amount', $this->text_domain); ?>:</label></td>
					<td>
						<strong><?php echo $cc['total_amount']; ?> <?php echo (empty($cc['currency_code']) ) ? 'USD' : $oppaypal['currency_code']; ?></strong>
					</td>
				</tr>
			</table>
			<div class="submit">
				<input type="submit" name="recurring_submit" value="<?php _e( 'Confirm data', $this->text_domain ); ?>" />
			</div>

		</form>

		<?php endif; ?>
		<div class="clear"></div><br />

		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
		<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
