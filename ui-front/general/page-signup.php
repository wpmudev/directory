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

$step        = get_query_var('checkout_step') ? get_query_var('checkout_step') : null;
$options     = get_option( DR_OPTIONS_NAME );
$opset		 = $options['payment_settings'];
$oppay		 = $options['paypal'];
$text_domain = DR_TEXT_DOMAIN;
$plugin_url  = DR_PLUGIN_URL;
$error       = get_query_var('checkout_error');

?>


<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
    <div class="entry-content">

    <?php $error = get_query_var('checkout_error'); ?>


    <ul id="error_msg_box">
        <?php if ( $error ): ?>
        <li><?php echo $error['error_long_msg']; ?></li>
        <?php endif; ?>
    </ul>


        <script language="JavaScript">
            jQuery( document ).ready( function() {

                var errorLogin  = 0;
                var errorEmail  = 0;
                var regEmail    = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

                jQuery( "#login" ).blur( function () {
                    var login = jQuery( "#login" ).val();
                    if ( "" != login ) {
                        jQuery( "#status_login" ).attr( "class", "field_checking" );
                        jQuery( "#status_login" ).html( "<?php _e( 'Checking...', $text_domain ); ?>" );
                        jQuery( "#status_login" ).show( 400 );

                        jQuery.ajax({
                            type: "POST",
                            url: "<?php echo get_option( 'siteurl' );?>/wp-admin/admin-ajax.php",
                            data: "action=check_login&type=login&login=" + login,
                            success: function( html ){
                                if ( "yes" == html ) {
                                    errorLogin = 1;
                                    jQuery( "#login_error td" ).attr( "class", "error" );
                                    jQuery( "#status_login" ).attr( "class", "field_exists" );
                                    jQuery( "#status_login" ).html( "<?php _e( 'Exists!', $text_domain ); ?>" );

                                } else {
                                    errorLogin =0;
                                    jQuery( "#login_error td" ).attr( "class", "" );
                                    jQuery( "#status_login" ).attr( "class", "field_available" );
                                    jQuery( "#status_login" ).html( "<?php _e( 'Available', $text_domain ); ?>" );
                                }
                            }
                        });
                    }

                });
                jQuery( "#login" ).blur();

                jQuery( "#email" ).blur( function () {
                    var email = jQuery( "#email" ).val();
                    if ( "" != email && false != regEmail.test( jQuery( "#email" ).val() ) ) {
                        jQuery( "#status_email" ).attr( "class", "field_checking" );
                        jQuery( "#status_email" ).html( "<?php _e( 'Checking...', $text_domain ); ?>" );
                        jQuery( "#status_email" ).show( 400 );

                        jQuery.ajax({
                            type: "POST",
                            url: "<?php echo get_option( 'siteurl' );?>/wp-admin/admin-ajax.php",
                            data: "action=check_login&type=email&email=" + email,
                            success: function( html ){
                                if ( "yes" == html ) {
                                    errorEmail = 1;
                                    jQuery( "#email_error td" ).attr( "class", "error" );
                                    jQuery( "#status_email" ).attr( "class", "field_exists" );
                                    jQuery( "#status_email" ).html( "<?php _e( 'Exists!', $text_domain ); ?>" );

                                } else {
                                    errorEmail =0;
                                    jQuery( "#email_error td" ).attr( "class", "" );
                                    jQuery( "#status_email" ).attr( "class", "field_available" );
                                    jQuery( "#status_email" ).html( "<?php _e( 'Available', $text_domain ); ?>" );

                                }
                            }
                        });
                    } else {
                        jQuery( "#status_email" ).html( "" );
                    }

                });
                jQuery( "#email" ).blur();


                jQuery( "#confirm_payment" ).submit( function () {
                    var pass        = jQuery( "#password" ).val();
                    var cpass       = jQuery( "#cpassword" ).val();
                    var errorMsg    = '';
                    var valid       = true;

                    //
                    jQuery( ".error" ).attr( "class", "" );



                    <?php if ( $step == 'cc_details' ): ?>
                    /*  CC validation*/
                    if ( "" == jQuery( "#cc_email" ).val() ) {
                        jQuery( "#cc_email" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the email!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( false == regEmail.test( jQuery( "#cc_email" ).val() ) ) {
                        jQuery( "#cc_email" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Invalid Email Address', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#first-name" ).val() ) {
                        jQuery( "#first-name" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the first name!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#last-name" ).val() ) {
                        jQuery( "#last-name" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the last name!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#street" ).val() ) {
                        jQuery( "#street" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the street!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#city" ).val() ) {
                        jQuery( "#city" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the city!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#state" ).val() ) {
                        jQuery( "#state" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the state!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == jQuery( "#zip" ).val() ) {
                        jQuery( "#zip" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the ZIP!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if (  "" == jQuery( "#country" ).val() || "-" == jQuery( "#country" ).val() ) {
                        jQuery( "#country" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please select the country!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if (  "" == jQuery( "#cc_number" ).val() ) {
                        jQuery( "#cc_number" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the card number!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if (  "" == jQuery( "#cvv2" ).val() ) {
                        jQuery( "#cvv2" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the CVV2!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    /* END CC validation*/
                    <?php endif; ?>



                    /* Registration Details validation*/
                    if ( "" == jQuery( "#login" ).val() ) {
                        jQuery( "#login" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the login!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( 1 == errorLogin ) {
                        jQuery( "#login" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'The login already exist!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == pass ) {
                        jQuery( "#password" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the password!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( "" == cpass ) {
                        jQuery( "#cpassword" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please confirm the password!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( pass != cpass ) {
                        jQuery( "#password" ).parent().parent().attr( "class", "error" );
                        jQuery( "#cpassword" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'The confirm password is incorrect!', $text_domain ); ?></li>';
                        valid = false;
                    }


                    if ( "" == jQuery( "#email" ).val() || false == regEmail.test( jQuery( "#email" ).val() ) ) {
                        jQuery( "#email" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'Please write the user email!', $text_domain ); ?></li>';
                        valid = false;
                    }

                    if ( 1 == errorEmail ) {
                        jQuery( "#email" ).parent().parent().attr( "class", "error" );
                        errorMsg = errorMsg + '<li><?php _e( 'The user email already exist!', $text_domain ); ?></li>';
                        valid = false;
                    }
                    /* END Registration Details validation*/



                    jQuery( "#error_msg_box" ).html( errorMsg );


                    return valid;
                });

            });
        </script>



    <?php if ( $step == 'disabled' ): ?>

        <?php _e( 'This feature is currently disabled by the system administrator.', $text_domain ); ?>

    <?php elseif ( $step == 'terms' ): ?>

        <form action="" method="post"  class="cf-checkout">
            <strong><?php _e( 'Cost of Service', $text_domain ); ?></strong>
            <table id="billing-type" >
                <?php /*
                <tr>
                    <td><label for="billing"><?php _e( 'Buy Credits', $text_domain ) ?></label></td>
                    <td>
                        <input type="radio" name="billing" value="credits" <?php if ( isset( $_POST['billing_type'] ) && $_POST['billing_type'] == 'credits' ) echo 'checked="checked"'; ?> />
                        <select name="credits_cost">
                            <?php for ( $i = 1; $i <= 10; $i++ ): ?>
                            <?php $credits = 10 * $i; ?>
                            <?php $amount = $credits * ( isset( $options['credits']['cost_credit'] ) ) ? $options['credits']['cost_credit'] : 0; ?>
                            <option value="<?php echo $amount; ?>" <?php if ( isset( $_POST['credits_cost'] ) && $_POST['credits_cost'] == $amount ) echo 'selected="selected"'; ?> ><?php echo $credits; ?><?php _e( 'Credits for', $text_domain ); ?> <?php echo $amount . ' ' . $options['paypal']['currency']; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
                 */ ?>
                <?php if ( isset( $opset['enable_recurring'] ) && '1' == $opset['enable_recurring'] ) : ?>
                    <tr>
                        <td <?php do_action( 'billing_invalid' ); ?>>
                            <label for="type_recurring"><?php if ( isset( $opset['recurring_name'] ) ) echo $opset['recurring_name']; ?></label>
                            <input type="radio" name="billing_type" id="type_recurring" value="recurring" <?php if ( isset( $_POST['billing_type'] ) && $_POST['billing_type'] == 'recurring' ) echo 'checked="checked"'; ?> />
                            <span>
                            <?php
                            $bastr    = isset( $opset['recurring_cost'] ) ? $opset['recurring_cost'] . ' ' : '';
                            $bastr .= $oppay['currency'];
                            $bastr .= __( ' per ', $text_domain );
                            $bastr .= ( !empty( $opset['billing_frequency'] ) && $opset['billing_frequency'] != 1 )
                                        ? $opset['billing_frequency'] . ' ' : '';
                            $bastr .= !empty( $opset['billing_period'] ) ? $opset['billing_period'] : '';
                            echo $bastr;
                            ?>
                            <span>
                            <input type="hidden" name="recurring_cost" value="<?php if ( isset( $opset['recurring_cost'] ) ) echo $opset['recurring_cost']; ?>" />
                            <input type="hidden" name="billing_agreement" value="<?php if ( isset( $opset['billing_agreement'] ) ) echo $opset['billing_agreement']; ?>" />
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ( isset( $opset['enable_one_time'] ) && '1' == $opset['enable_one_time'] ) : ?>
                    <tr>
                        <td <?php do_action( 'billing_invalid' ); ?>>
                            <label for="type_one_time"><?php if ( isset( $opset['one_time_name'] ) ) echo $opset['one_time_name']; ?></label>
                            <input type="radio" name="billing_type" id="type_one_time"  value="one_time" <?php if ( isset( $_POST['billing_type'] ) && $_POST['billing_type'] == 'one_time' ) echo 'checked="checked"'; ?> /> <?php if ( isset( $opset['one_time_cost'] ) ) echo $opset['one_time_cost']; ?> <?php echo $oppay['currency']; ?>
                            <input type="hidden" name="one_time_cost" value="<?php if ( isset( $opset['one_time_cost'] ) ) echo $opset['one_time_cost']; ?>" />
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
            <br />

            <strong><?php _e( 'Terms of Service', $text_domain ); ?></strong>
            <table id="tos">
                <tr>
                    <td><div class="terms">
                    <?php
                    if ( isset( $opset['tos_content'] ) )
                        echo nl2br( $opset['tos_content'] );
                    ?>
                    </div></td>
                </tr>
            </table>
            <br />

            <table id="tos-agree" >
                <tr>
					<td <?php do_action( 'tos_invalid' ); ?> >
						<label for="tos_agree"><?php _e( 'I agree with the Terms of Service', $text_domain ); ?></label>
						<input type="checkbox" id="tos_agree" name="tos_agree" value="1" <?php if ( isset( $_POST['tos_agree'] ) ) echo 'checked="checked"'; ?> />
					</td>
                </tr>
            </table>

            <div class="submit">
                <input type="submit" name="terms_submit" value="<?php _e( 'Continue', $text_domain ); ?>" />
            </div>
        </form>

		<?php /*
        <form action="" method="post" class="checkout-login">
            <strong><?php _e( 'Existing client', $text_domain ); ?></strong>
            <table  <?php do_action( 'login_invalid' ); ?>>
                <tr>
                    <td><label for="username"><?php _e( 'Username', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="username" name="username" /></td>
                </tr>
                <tr>
                    <td><label for="password"><?php _e( 'Password', $text_domain ); ?>:</label></td>
                    <td><input type="password" id="password" name="password" /></td>
                </tr>
            </table>

            <div class="clear"></div>

            <div class="submit">
                <input type="submit" name="login_submit" value="<?php _e( 'Continue', $text_domain ); ?>" />
            </div>
        </form>
		 */ ?>

        <?php if ( !empty( $error ) ): ?>
            <div class="invalid-login"><?php echo $error; ?></div>
        <?php endif; ?>

    <?php /* Payment Method Selection */ ?>
    <?php elseif( $step == 'payment_method' ): ?>

        <form action="" method="post"  class="checkout">
            <strong><?php _e('Choose Payment Method', $text_domain ); ?></strong>
            <table id="payment-method">
                <tr>
					<td <?php do_action('pm_invalid'); ?>>

						<label for="pmethod_paypal"><?php _e( 'PayPal', $text_domain ); ?></label>
                        <input type="radio" name="payment_method" id="pmethod_paypal" value="paypal"/>
                        <img  src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" border="0" alt="Acceptance Mark">
                    </td>
                </tr>

                <?php if ( 'recurring' != $_SESSION['billing_type'] ): ?>
                    <tr>
						<td <?php do_action('pm_invalid'); ?>>
							<label for="pmethod_cc"><?php _e( 'Credit Card', $text_domain ); ?></label>
                            <input type="radio" name="payment_method" id="pmethod_cc" value="cc" />
                            <img  src="<?php echo $plugin_url; ?>ui-front/images/cc-logos-small.jpg" border="0" alt="Solution Graphics">
                        </td>
                    </tr>
                <?php endif; ?>

            </table>

            <div class="submit">
                <input type="submit" name="payment_method_submit" value="<?php _e( 'Continue', $text_domain ); ?>" />
            </div>
        </form>

    <?php /* Credit Card Details */ ?>
    <?php elseif ( $step == 'cc_details' ):

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

        ?>
        <?php if ( $details['confirm_error'] ): ?>
            <span style="color: red;"><?php echo $details['confirm_error']; ?></span>
            <div class="clear"></div>
            <br clear="all" />
        <?php endif; ?>
        <form action="" method="post" id="confirm_payment" class="checkout">
            <strong><?php _e( 'Payment Details', $text_domain ); ?></strong>
            <div class="clear"></div>
            <table id="cc-user-details">
                <tr>
                    <td><label for="cc_email"><?php _e( 'Email Address', $text_domain ); ?>:</label></td>
                    <td><input type="text" name="email" id="cc_email" value="<?php echo $details['email']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="first-name"><?php _e( 'First Name', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="first-name" name="first_name" value="<?php echo $details['first_name']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="last-name"><?php _e( 'Last Name', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="last-name" name="last_name" value="<?php echo $details['last_name']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="street"><?php _e( 'Street', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="street" name="street" value="<?php echo $details['street']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="city"><?php _e( 'City', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="city" name="city" value="<?php echo $details['city']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="state"><?php _e( 'State', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="state" name="state" value="<?php echo $details['state']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="zip"><?php _e( 'ZIP', $text_domain ); ?>:</label></td>
                    <td><input type="text" id="zip" name="zip" value="<?php echo $details['zip']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="country"><?php _e( 'Country', $text_domain ); ?>:</label></td>
                    <td>
                        <select id="country" name="country_code">
                            <?php foreach ( $countries as $key => $value ) { ?>
                            <option value="<?php echo $key; ?>" <?php echo ( isset( $details['country_code'] ) && $key == $details['country_code'] ) ? 'selected' : ''; ?>  ><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Total Amount', $text_domain ); ?>:</td>
                    <td>
                        <strong><?php if ( isset( $_SESSION['cost'] ) ) echo $_SESSION['cost']; ?> <?php echo $oppay['currency']; ?></strong>
                        <input type="hidden" name="total_amount" value="<?php if ( isset( $_SESSION['cost'] ) ) echo $_SESSION['cost']; ?>" />
                    </td>
                </tr>
            </table>

            <table id="cc-card-details">
                <tr>
                    <td><label for="cc_type"><?php _e( 'Credit Card Type', $text_domain ); ?>:</label></td>
                    <td>
                        <select name="cc_type" id="cc_type">
                            <option><?php _e( 'Visa', $text_domain ); ?></option>
                            <option><?php _e( 'MasterCard', $text_domain ); ?></option>
                            <option><?php _e( 'Amex', $text_domain ); ?></option>
                            <option><?php _e( 'Discover', $text_domain ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="cc_number"><?php _e( 'Credit Card Number', $text_domain ); ?>:</label></td>
                    <td><input type="text" name="cc_number" id="cc_number" /></td>
                </tr>
                <tr>
                    <td><label for="exp_date"><?php _e( 'Expiration Date', $text_domain ); ?>:</label></td>
                    <td>
                        <select name="exp_date_month" id="exp_date">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>

                        <select name="exp_date_year">
                            <?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; $i++ ) { ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="cvv2"><?php _e( 'CVV2', $text_domain ); ?>:</label></td>
                    <td><input type="text" name="cvv2" id="cvv2" /></td>
                </tr>
            </table>
            <div class="clear"></div>
            <div class="clear"></div>
            <strong><?php _e( 'Registration Details', $text_domain ); ?></strong>
            <div class="clear"></div>
            <table id="cc-user-details">
                <tr id="login_error">
                    <td width="110"><label for="login"><?php _e( 'Login', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="login" id="login" value="<?php echo $details['login']; ?>"/>
                        <span id="status_login" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="password"><?php _e( 'Password', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="password" id="password" value=""/></td>
                </tr>
                <tr>
                    <td><label for="cpassword"><?php _e( 'Confirm Pass', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="cpassword" id="cpassword" value=""/></td>
                </tr>
                <tr id="email_error">
                    <td><label for="email"><?php _e( 'User Email', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="user_email" id="email" value="<?php echo $details['user_email']; ?>" />
                        <span id="status_email" style="display: none;"></span>
                    </td>
                </tr>
            </table>



            <div class="clear"></div>
            <div class="submit">
                <input type="submit" name="direct_payment_submit" value="Continue" />
            </div>

        </form>

    <?php /* Confirm Payment Step */ ?>
    <?php elseif ( $step == 'confirm_payment' ): ?>

        <?php $transaction_details = get_query_var('checkout_transaction_details'); ?>

        <form action="" method="post" name="confirm_payment" id="confirm_payment" class="checkout">
            <strong><?php _e( 'Confirm Payment', $text_domain ); ?></strong>
            <?php if ( $transaction_details['confirm_error'] ): ?>
                <center>
                    <span style="color: red;"><?php echo $transaction_details['confirm_error']; ?></span>
                </center>
            <?php endif; ?>
            <table id="confirm-payment">
                <tr id="login_error">
                    <td><label for="login"><?php _e( 'Login', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="login" id="login" value="<?php echo $transaction_details['login']; ?>"/>
                        <span id="status_login" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="password"><?php _e( 'Password', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="password" id="password" value=""/></td>
                </tr>
                <tr>
                    <td><label for="cpassword"><?php _e( 'Password Confirm', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="cpassword" id="cpassword" value=""/></td>
                </tr>
                <tr id="email_error">
                    <td><label for="email"><?php _e( 'User Email', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="email" id="email" value="<?php echo $transaction_details['EMAIL']; ?>" size="50"/>
                        <span id="status_email" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label><?php _e( 'Name', $text_domain ); ?>:</label></td>
                    <td><?php echo $transaction_details['FIRSTNAME'] . ' ' . $transaction_details['LASTNAME']; ?></td>
                </tr>
                <?php if ( $_SESSION['billing_type'] == 'recurring' ): ?>
                <tr>
                    <td><label><?php _e( 'Billing Agreement', $text_domain ); ?>:</label></td>
                    <td><?php echo $_SESSION['billing_agreement']; ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><label><?php _e('Total Amount', $text_domain); ?>:</label></td>
                    <td>
                        <strong><?php if ( isset( $_SESSION['cost'] ) ) echo $_SESSION['cost'] . ' ' . $transaction_details['CURRENCYCODE']; else echo $transaction_details['AMT'] . ' ' . $transaction_details['CURRENCYCODE']; ?></strong>
                        <input type="hidden" name="total_amount" value="<?php if ( isset( $_SESSION['recurring_cost'] ) ) echo $_SESSION['recurring_cost']; else echo $transaction_details['AMT']; ?>" />
                    </td>
                </tr>
            </table>

            <div class="submit">
                <input type="hidden" name="result" value="<?php echo base64_encode( serialize( $transaction_details ) ); ?>" />
                <input type="hidden" name="first_name" value="<?php echo $transaction_details['FIRSTNAME']; ?>" />
                <input type="hidden" name="last_name" value="<?php echo $transaction_details['LASTNAME']; ?>" />
                <input type="hidden" name="billing_type" value="<?php echo $_SESSION['billing_type']; ?>" />
                <input type="hidden" name="no_shipping" value="1" />
                <?php /* <input type="hidden" name="credits" value="<?php echo $_SESSION['credits']; ?>" /> */ ?>
                <input type="submit" name="confirm_payment_submit" value="Confirm Payment" />
            </div>

        </form>

    <?php /* API Call Error */ ?>
    <?php elseif ( $step == 'api_call_error' ): ?>

        <?php $error = get_query_var('checkout_error'); ?>

        <ul>
            <li><?php echo $error['error_call'] . ' API call failed.'; ?></li>
            <li><?php echo 'Detailed Error Message: ' . $error['error_long_msg']; ?></li>
            <li><?php echo 'Short Error Message: '    . $error['error_short_msg']; ?></li>
            <li><?php echo 'Error Code: '             . $error['error_code']; ?></li>
            <li><?php echo 'Error Severity Code: '    . $error['error_severity_code']; ?></li>
        </ul>

    <?php /* Success */ ?>
    <?php elseif ( $step == 'success' ): ?>

        <div class="dp-submit-txt"><?php _e( 'Thank you for your business. Transaction processed successfully!', $text_domain ); ?></div>
        <span class="dp-submit-txt"><?php _e( 'You can go to your profile and review/change your personal information, or you can go straight to the directory listing submission page.', $text_domain ); ?></span>
        <br />

        <form id="add-listing-su" action="" method="post">
            <input type="submit" name="redirect_listing" value="Add Listing" />
        </form>
        <form id="go-to-profile-su" action="" method="post">
            <input type="submit" name="redirect_profile" value="Go To Profile" />
        </form>
        <br class="clear" />


    <?php /* Free mode */ ?>
    <?php elseif ( $step == 'free' ): ?>

        <form action="" method="post" name="confirm_payment" id="confirm_payment" class="checkout">
            <strong><?php _e( 'Registration', $text_domain ); ?></strong>
            <table id="confirm-payment">
                <tr id="login_error">
                    <td><label for="login"><?php _e( 'Login', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="login" id="login" value="<?php echo $transaction_details['login']; ?>"/>
                        <span id="status_login" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="password"><?php _e( 'Password', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="password" id="password" value=""/></td>
                </tr>
                <tr>
                    <td><label for="cpassword"><?php _e( 'Password Confirm', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="cpassword" id="cpassword" value=""/></td>
                </tr>
                <tr id="email_error">
                    <td><label for="email"><?php _e( 'User Email', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="email" id="email" value="<?php echo $transaction_details['EMAIL']; ?>" size="50"/>
                        <span id="status_email" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="first_name"><?php _e( 'Name', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="first_name" id="first_name" value="<?php echo $transaction_details['first_name']; ?>" size="50"/>
                    </td>
                </tr>
            </table>

            <div class="submit">
                <input type="submit" name="free_submit" value="<?php _e( 'Confirm data', $text_domain ); ?>" />
            </div>

        </form>


    <?php /* Free Success */ ?>
    <?php elseif ( $step == 'free_success' ): ?>

        <div class="dp-submit-txt"><?php _e( 'The registration is completed successfully!', $text_domain ); ?></div>
        <span class="dp-submit-txt"><?php _e( 'You can go to your profile and review/change your personal information, or you can go straight to the directory listing submission page.', $text_domain ); ?></span>
        <br />

        <form id="add-listing-su" action="" method="post">
            <input type="submit" name="redirect_listing" value="Add Listing" />
        </form>
        <form id="go-to-profile-su" action="" method="post">
            <input type="submit" name="redirect_profile" value="Go To Profile" />
        </form>
        <br class="clear" />


    <?php /* Recurring payment */ ?>
    <?php elseif ( $step == 'recurring_payment' ): ?>

        <?php $transaction_details = get_query_var('checkout_transaction_details'); ?>

        <form action="" method="post" name="confirm_payment" id="confirm_payment" class="checkout">
            <strong><?php _e( 'Registration details', $text_domain ); ?></strong>
            <table id="confirm-payment">
                <tr id="login_error">
                    <td><label for="login"><?php _e( 'Login', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="login" id="login" value="<?php echo $transaction_details['login']; ?>"/>
                        <span id="status_login" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="password"><?php _e( 'Password', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="password" id="password" value=""/></td>
                </tr>
                <tr>
                    <td><label for="cpassword"><?php _e( 'Password Confirm', $text_domain ); ?>:</label></td>
                    <td><input type="password" name="cpassword" id="cpassword" value=""/></td>
                </tr>
                <tr id="email_error">
                    <td><label for="email"><?php _e( 'User Email', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="email" id="email" value="<?php echo $transaction_details['email']; ?>" size="50"/>
                        <span id="status_email" style="display: none;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="first_name"><?php _e( 'Name', $text_domain ); ?>:</label></td>
                    <td>
                        <input type="text" name="first_name" id="first_name" value="<?php echo $transaction_details['first_name']; ?>" size="50"/>
                    </td>
                </tr>
            </table>

            <div class="submit">
                <input type="submit" name="recurring_submit" value="<?php _e( 'Confirm data', $text_domain ); ?>" />
            </div>

        </form>
    <?php endif; ?>

</div><!-- #post-## -->