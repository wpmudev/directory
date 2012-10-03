<?php

/**
* DR_Payments
* Payments Core Class. Handles requests and defines common utility functions.
*
* @uses Directory_Core
* @copyright Incsub 2007-2012 {@link http://incsub.com}
* @author Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/


class DR_Payments{

	// @private object The PayPal API Module object see __get
	private $_paypal_express_gateway = null;

	// @private object The AuthorizeNet API Module object see __get
	private $_authorizenet_gateway=null;

	public $text_domain = DR_TEXT_DOMAIN;
	public $options_name = DR_OPTIONS_NAME;
	public $plugin_dir = DR_PLUGIN_DIR;
	public $plugin_url = DR_PLUGIN_URL;

	public $use_free = false;
	public $use_paypal = false;
	public $use_authorizenet = false;

	public $paypal_options = null;
	public $authorizenet_options = null;

	public $use_credits = false;
	public $use_recurring = false;
	public $use_one_time = false;

	public $user_role = 'subscriber';

	// Empty structure to store transactions
	private $dr_struc =
	array(
	'credits' => 0,
	'credits_log' => array(),
	'order' => array('billing_type' => '', 'status' => '', 'expiration' => 0, 'order_info' => array() ),
	'paypal' => array('transactions' => array(), 'profile_id'   => ''),
	'authorizenet' => array('transactions' => array(), 'profile_id' => ''),
	);

	/**
	* Constructor.
	*/

	function DR_Payments() { __construct(); }

	function __construct() {
		global $Directory_Core;

		// Handle all requests for checkout
		add_action( 'template_redirect', array( &$this, 'handle_checkout_requests' ) );
		add_action( 'init', array( &$this, 'init' ) );
	}


	function init(){
		global $current_user;

		$current_user = wp_get_current_user();

		$options = $this->get_options();
		$this->user_role = (empty($options['general']['member_role']) ) ? get_option('default_role') :$options['general']['member_role'] ;

		//How do we sell stuff
		$options = (empty($options['payment_types'] ) ) ? array() : $options['payment_types'] ;

		$this->use_free = ( ! empty($options['use_free']));
		if (! $this->use_free) { //Can't use gateways if it's free.

			//PAYPAL
			$this->use_paypal = ( ! empty($options['use_paypal'])) && (! empty($options['paypal']['api_username'])) && (! empty($options['paypal']['api_password'])) && (! empty($options['paypal']['api_signature']));
			if ($this->use_paypal){ //make sure the api fields have something in them
				$this->paypal_options = $options['paypal'];
			}

			//AUTHORIZENET
			$this->use_authorizenet = (! empty($options['use_authorizenet'])) &&  (! empty($options['authorizenet']['api_user'])) && (! empty($options['authorizenet']['api_key']));
			if ($this->use_authorizenet){ //make sure the api fields have something in them
				$this->authorizenet_options = $options['authorizenet'];
			}

			$options = $this->get_options('payments');

			$this->use_credits = ( ! empty($options['enable_credits']));
			$this->use_recurring = ( ! empty($options['enable_recurring']));
			$this->use_one_time = ( ! empty($options['enable_one_time']));
		}
	}

	/**
	* Get plugin options.
	*
	* @param  string|NULL $key The key for that plugin option.
	* @return array $options Plugin options or empty array if no options are found
	*/
	function get_options( $key = null ) {
		$options = get_option( $this->options_name );
		$options = is_array( $options ) ? $options : array();
		/* Check if specific plugin option is requested and return it */
		if ( isset( $key ) && array_key_exists( $key, $options ) )
		return $options[$key];
		else
		return $options;
	}

	/**
	* Update user information. This method handles both add and update
	* operations.
	*
	* @param string $email
	* @param string $first_name
	* @param string $last_name
	* @param string $billing_type The billing type for the user
	* @return NULL|void
	*/
	function update_user( $user_data = '', $billing_type='', $credits='', $transaction_details = '') {
		global $current_user;

		$valid_keys = array_flip(array_merge(array('ID', 'user_email', 'user_login', 'user_pass', 'role' ), _get_additional_user_keys($current_user) ) );
		$user_update = array();

		// If user logged in update it
		if ( is_user_logged_in() ) {

			if(is_array($user_data) ) $user_update = array_intersect_key($user_data, $valid_keys); //Filter data for valid fields

			// Set the user role for directory
			$user_update['role'] = $this->user_role;
			$user_update['ID'] = get_current_user_id();
			wp_update_user( $user_update ); //Save it

			//Record the transaction
			$transactions = new DR_Transactions($user_update['ID']);

			$transactions->billing_type = $billing_type;

			if(! empty($billing_type) && $billing_type == 'credits'){
				$transactions->credits += $credits;
			}

			//AUTHORIZENET
			if ($_SESSION['payment_method'] == 'cc'){
				$transactions->authorizenet = $transaction_details;
			}
			//PAYPAL
			elseif ($_SESSION['payment_method'] == 'paypal'){
				$transactions->paypal = $transaction_details;
			}

			unset($transactions);
		}
	}


	/**
	* Set payment details for user in DB
	* ( transaction ID's and recurring payment profile ID )
	*
	* @param string|int $user_id
	* @param string $billing_type
	* @param array $transaction_details
	* @access public
	* @return void
	*/
	function update_user_payment_details( $user_id, $billing_type, $credits, $transaction_details ) {

		$transactions = new DR_Transactions;

		$transactions->billing_type = $billing_type;

		if(! empty($billing_type) && $billing_type == 'credits'){
			$transactions->credits += $credits;
		}

		//AUTHORIZENET
		if ($_SESSION['payment_method'] == 'cc'){
			$transaction->authorizenet = $transaction_details;
		}
		//PAYPAL
		elseif ($_SESSION['payment_method'] == 'paypal'){
			$transaction->paypal = $transaction_details;
		}
	}

	/**
	* Handle all checkout requests.
	*
	* @uses session_start() We need to keep track of some session variables for the checkout
	* @return NULL If the payment gateway options are not configured.
	*/
	function handle_checkout_requests() {

		global $blog_id, $current_user, $Directory_Core;

		// Only handle request if on the proper page
		if ( ! (isset($Directory_Core) && is_page($Directory_Core->signup_page_id) ) ) return;

		// Redirect if user is logged in
		if ( is_user_logged_in() ) {
			//wp_redirect( get_bloginfo('url') );
			//exit;
		}

		$options = $this->get_options();

		$this->paypal_options['return_url'] = get_permalink($Directory_Core->signup_page_id);


		// We need to use session variables during the checkout process
		if ( !session_id() ) session_start();

		// If no selected any gateway - disable the checkout process
		if( ! ($this->use_free || $this->use_paypal || $this->use_authorizenet) ) {
			// Set the proper step which will be loaded by "page-checkout.php"
			set_query_var( 'checkout_step', 'disabled' );
			return;
		}

		// If free mode
		if ( $this->use_free ) {
			// Set the proper step which will be loaded by "page-checkout.php"
			set_query_var( 'checkout_step', 'free_success' );
			return;
		}

		// If Terms and Costs step is submitted
		if ( isset( $_POST['terms_submit'] )) {

			// Validate fields
			if ( empty( $_POST['tos_agree'] ) || empty( $_POST['billing_type'] ) ) {

				if ( empty( $_POST['tos_agree'] ))
				add_action( 'tos_invalid', create_function('', 'echo "class=\"error\"";') );
				if ( empty( $_POST['billing_type'] ))
				add_action( 'billing_invalid', create_function('', 'echo "class=\"error\"";') );

				// Set the proper step which will be loaded by "page-checkout.php"
				set_query_var( 'checkout_step', 'terms' );

			} else {

				// Set session variables
				$_SESSION['billing_type'] = $_POST['billing_type'];

				if( $_SESSION['billing_type'] == 'recurring' ) {
					$_SESSION['cost']              = sprintf( "%01.2f", $_POST['recurring_cost'] );
					$_SESSION['billing_period']    = $options['payments']['billing_period'];
					$_SESSION['billing_frequency'] = $options['payments']['billing_frequency'];
					$_SESSION['billing_agreement'] = $options['payments']['billing_agreement'];
				}

				elseif($_SESSION['billing_type'] == 'one_time') {
					$_SESSION['cost'] = sprintf( "%01.2f", $_POST['one_time_cost'] );
					$_SESSION['billing_agreement'] = $options['payments']['one_time_name'];
				}

				elseif($_SESSION['billing_type'] == 'credits') {
					$_SESSION['cost'] = sprintf( "%01.2f", $_POST['credits'] * $options['payments']['cost_credit']);
					$_SESSION['credits'] = $_POST['credits'];
					$_SESSION['billing_agreement'] = $options['payments']['credits_description'];
				}

				$_SESSION['credits'] = (empty($_SESSION['credits']) ) ? '0' : $_SESSION['credits'];

				$_SESSION['CC'] = array(
				'cc_email'        => empty($current_user->cc_email) ? $current_user->user_email : $current_user->cc_email,
				'cc_firstname'    => empty($current_user->cc_firstname) ? $current_user->first_name : $current_user->cc_firstname,
				'cc_lastname'     => empty($current_user->cc_lastname) ? $current_user->last_name : $current_user->cc_lastname,
				'cc_street'       => empty($current_user->cc_street) ? '' : $current_user->cc_street,
				'cc_city'         => empty($current_user->cc_city) ? '' : $current_user->cc_city,
				'cc_state'        => empty($current_user->cc_state) ? '' : $current_user->cc_state,
				'cc_zip'          => empty($current_user->cc_zip) ? '' : $current_user->cc_zip,
				'cc_country_code' => empty($current_user->cc_country_code) ? '' : $current_user->cc_country_code,
				'total_amount' 		=> $_SESSION['cost'],
				'currancy_code'   => 'USD',
				); //For credit card details

				// Set the proper step which will be loaded by "page-checkout.php"
				set_query_var( 'checkout_step', 'payment_method' );
			}
		}

		// If payment method is selected and submitted
		elseif ( isset( $_POST['payment_method_submit'] ) ) {

			// Validate fields
			if ( empty( $_POST['payment_method'] ) ) {

				add_action( 'pm_invalid', create_function('', 'echo "class=\"error\"";') );
				// Set the proper step which will be loaded by "page-checkout.php"
				set_query_var( 'checkout_step', 'payment_method' );

			} else {

				$_SESSION['payment_method'] = $_POST['payment_method']; //Save to session

				if ( $_SESSION['payment_method'] == 'cc' ) {
					// Set the proper step which will be loaded by "page-checkout.php"
					set_query_var( 'checkout_step', 'cc_details' );
				}
				elseif ( $_SESSION['payment_method'] == 'paypal' ) {

					if ( 'recurring' == $_SESSION['billing_type'] ) {
						set_query_var( 'checkout_step', 'recurring_payment' );
					} else {
						// If recuring payment selected pass '0' so we can void the direct payment
						$cost = ($_SESSION['billing_type'] == 'recurring') ? 0 : $_SESSION['cost'];
						$billing_agreement = $_SESSION['billing_agreement']; //($_SESSION['billing_type'] == 'recurring') ? $_SESSION['billing_agreement'] : null;

						// Make API call
						$result = $this->paypal_express_gateway->call_shortcut_express_checkout(
						$cost,
						$_SESSION['billing_type'],
						$billing_agreement
						);

						// Handle Error scenarios
						if(!empty($result['CC']) ) $_SESSION['CC'] = $result['CC'];

						if ( $result['status'] == 'error' ) {
							// Set the proper step which will be loaded by "page-checkout.php"
							set_query_var( 'checkout_step', 'api_call_error' );
							// Pass error params to "page-checkout.php"
							set_query_var( 'checkout_error', $result );

							// Destroys the $_SESSION
							$this->destroy_session();
						}
					}
				}
			}
		}

		// If direct CC payment is submitted
		elseif ( isset( $_POST['direct_payment_submit'] ) ) {

			unset($_POST['direct_payment_submit']);

			//AUTHORIZE NET
			if($_SESSION['payment_method'] == 'cc' && $this->use_authorizenet) {

				$_SESSION['CC'] = $_POST;

				set_query_var( 'checkout_step', 'confirm_payment' );
			}
			//PAYPAL
			elseif($_SESSION['payment_method'] == 'paypal' && $this->use_paypal) {

				// Make API call
				$result = $this->paypal_express_gateway->direct_payment(
				$_POST['total_amount'],
				$_POST['cc_type'],
				$_POST['cc_number'],
				$_POST['exp_date_month'] . $_POST['exp_date_year'],
				$_POST['cvv2'],
				$_POST['cc_firstname'],
				$_POST['cc_lastname'],
				$_POST['cc_street'],
				$_POST['cc_city'],
				$_POST['cc_state'],
				$_POST['cc_zip'],
				$_POST['cc_country_code']
				);

				// Handle Success and Error scenarios
				if ( $result['status'] == 'success' ) {
					// Set the proper step which will be loaded by "page-checkout.php"

					// Insert/Update User
					$this->update_user( $_POST, '', '', $result);
					set_query_var( 'checkout_step', 'success' );
				} else {

					$details['cc_email']           = $_POST['cc_email'];
					$details['cc_first_name']      = $_POST['cc_first_name'];
					$details['cc_last_name']       = $_POST['cc_last_name'];
					$details['cc_street']          = $_POST['cc_street'];
					$details['cc_city']            = $_POST['cc_city'];
					$details['cc_state']           = $_POST['cc_state'];
					$details['cc_zip']             = $_POST['cc_zip'];
					$details['cc_country_code']    = $_POST['cc_country_code'];
					//$details['login']           = $_POST['login'];
					//$details['user_email']      = $_POST['user_email'];

					set_query_var( 'details', $details );
					set_query_var( 'checkout_step', 'cc_details' );

					// Pass error params to "page-checkout.php"
					set_query_var( 'checkout_error', $result );

				}
			}
		}

		// If PayPal has redirected us back with the proper TOKEN
		elseif ( isset( $_REQUEST['token'])
		&& $this->use_paypal
		&& !isset( $_POST['confirm_payment_submit'] )
		&& !isset( $_POST['redirect_my_listings'] ) ) {

			$_SESSION['token'] = $_REQUEST['token'];

			// Make API call
			$result = $this->paypal_express_gateway->get_express_checkout_details( $_SESSION['token'] );

			// Handle Success and Error scenarios
			if ( $result['status'] == 'success' ) {
				// Set the proper step which will be loaded by "page-checkout.php"
				set_query_var( 'checkout_step', 'confirm_payment' );
				// Pass transaction details params to "page-checkout.php"
				set_query_var( 'checkout_transaction_details', $result );
				$_SESSION['CC'] = $result['CC'];
			} else {
				// Set the proper step which will be loaded by "page-checkout.php"
				set_query_var( 'checkout_step', 'api_call_error' );
				// Pass error params to "page-checkout.php"
				set_query_var( 'checkout_error', $result );

				// Destroys the $_SESSION
				$this->destroy_session();
			}
		}


		// If payment confirmation is submitted
		elseif ( isset( $_POST['confirm_payment_submit'] ) ) {


			//AUTHORIZENET
			if( $_SESSION['payment_method'] == 'cc' && $this->use_authorizenet) {

				//Invoice number contains the blog id as it's about the only thing that gets passed everywhere
				$invoice_key = uniqid("LST-{$blog_id}-"); // LST is the prefix for Gateway Relay

				$args  = array(
				'refId'      => $invoice_key,
				'transactionRequest' => array(
				'transactionType' => 'authCaptureTransaction',
				'amount'     => $_POST['total_amount'],

				'payment' => array(
				'creditCard' => array(
				'cardNumber' => $_POST['cc_number'],
				'expirationDate' => $_POST['exp_date_month'] . $_POST['exp_date_year'],
				'cardCode' => $_POST['cvv2'],
				),
				),

				'order' => array(
				'invoiceNumber' => $invoice_key,
				'description' => $_SESSION['billing_agreement'],
				),

				'lineItems' => array(
				'lineItem' => array(
				0 => array(
				'itemId' => '1',
				'name' => $options['payments']['recurring_name'],
				'description' => $_SESSION['billing_agreement'],
				'quantity' => '1',
				'unitPrice' => $_SESSION['cost'],
				),
				),
				),

				'customer' => array(
				'id' =>  get_current_user_id(),
				'email' => $_POST['cc_email']
				),

				'billTo'    => array(
				'firstName' => $_POST['cc_firstname'],
				'lastName'  => $_POST['cc_lastname'],
				'company'   => '',
				'address'    => $_POST['cc_street'],
				'city'       => $_POST['cc_city'],
				'state'      => $_POST['cc_state'],
				'zip'        => $_POST['cc_zip'],
				'country'    => $_POST['cc_country_code'],
				),

				'customerIP' => $_SERVER['REMOTE_ADDR'],

				'transactionSettings' => array(
				'setting' => array(
				0 => array('settingName' => 'allowPartialAuth','settingValue' => 'false', ),
				1 => array('settingName' => 'duplicateWindow', 'settingValue' => '0', ),
				2 => array('settingName' => 'emailCustomer', 'settingValue' =>  ($this->authorizenet_options['email_customer'] == 'yes') ? 'true' : 'false' ),
				3 => array('settingName' => 'recurringBilling', 'settingValue' => 'false', ),
				4 => array('settingName' => 'testRequest', 'settingValue' => ($this->authorizenet_options['mode'] != 'live') ? 'true' : 'false',),
				5 => array('settingName' => 'headerEmailReceipt', 'settingValue' => $this->authorizenet_options['header_email_receipt'],),
				6 => array('settingName' => 'footerEmailReceipt', 'settingValue' => $this->authorizenet_options['footer_email_receipt'],),
				),
				),

				'userFields' => array(
				'userField' => array(
				0 => array( 'name' => 'blog_id', 'value' => $blog_id ),
				1 => array( 'name' => 'user_id', 'value' => get_current_user_id() ),
				2 => array( 'name' => 'billing_type', 'value' => $_SESSION['billing_type'] ),
				3 => array( 'name' => 'billing_period', 'value' => (empty($_SESSION['billing_period']) ) ? '' : $_SESSION['billing_period'] ),
				4 => array( 'name' => 'billing_frequency', 'value' => (empty($_SESSION['billing_frequency']) ) ? '' : $_SESSION['billing_frequency'] ),
				),
				),
				),
				);

				$this->authorizenet_gateway->createTransactionRequest( $args );


				if($this->authorizenet_gateway->isSuccessful()
				&& $this->authorizenet_gateway->transactionResponse->responseCode == 1){

					$this->update_user( $_POST, $_SESSION['billing_type'], $_SESSION['credits'], $this->authorizenet_gateway );
					// Set the proper step which will be loaded by "page-checkout.php"
					set_query_var( 'checkout_step', 'success' );

					if( $_SESSION['billing_type'] == 'recurring') {

						unset($this->_authorizenet_gateway);
						$this->_authorizenet_gateway = null;

						register_gateway_relay('LST-', admin_url('admin-ajax.php?action=directory_sp'), '');

						// If recurring start at the next payment
						$key = md5(
						'USD' .
						'directory_123' .
						$options['payments']['recurring_cost']
						);

						//recurring calculate the expiration
						if(! empty($_SESSION['billing_period']) && ! empty($_SESSION['billing_frequency'])) {

							switch ($_SESSION['billing_period']) {
								case 'Day' : $length = $_SESSION['billing_frequency']; $unit = 'days'; break;
								case 'Week' : $length = (7 * $_SESSION['billing_frequency']);  $unit = 'days'; break;
								case 'Month' : $length = $_SESSION['billing_frequency'];  $unit = 'months'; break;
								case 'Year' : $length = (12 * $_SESSION['billing_frequency']);  $unit = 'months'; break;
							}

							$period = 'P' . $_SESSION['billing_frequency'] . substr($_SESSION['billing_period'], 0, 1 ); //ISO interval
							$date = new DateTime;
							$date->add(new DateInterval($period) );
							$start_date = $date->format('Y-m-d');
						}

						$subscription =
						array(
						'refId'      => $args['refId'],
						'subscription' => array(
						'name' => $options['payments']['recurring_name'],

						'paymentSchedule' => array(
						'interval' => array(
						'length' => $length,
						'unit' => $unit
						),

						'startDate' => $start_date,
						'totalOccurrences' => '9999', //Forever
						'trialOccurrences' => '0'
						),

						'amount' => $_POST['total_amount'],
						'trialAmount' => '0.00',
						'payment' => $args['transactionRequest']['payment'],
						'order' => $args['transactionRequest']['order'],
						'customer' => $args['transactionRequest']['customer'],
						'billTo' => $args['transactionRequest']['billTo'],
						)
						);

						$this->authorizenet_gateway->ARBCreateSubscriptionRequest( $subscription );
						if($this->authorizenet_gateway->isSuccessful() ) {
							$this->update_user( $_POST, $_SESSION['billing_type'], $_SESSION['credits'], $this->authorizenet_gateway );
							// Set the proper step which will be loaded by "page-checkout.php"
							set_query_var( 'checkout_step', 'success' );
						}
					}

				} else {

					// Set the proper step which will be loaded by "page-checkout.php"
					set_query_var( 'checkout_step', 'api_call_error' );
					// Pass error params to "page-checkout.php"
					$result = array(
					'status'          => 'error',
					'error_call' 			=> 'AuthorizeAndCapture',
					'error_long_msg'  => $this->authorizenet_gateway->transactionResponse->message->description,
					'error_short_msg' => $this->authorizenet_gateway->messages->message->text,
					'error_code' => $this->authorizenet_gateway->messages->resultCode,
					'error_severity_code' => $this->authorizenet_gateway->transactionResponse->responseCode,
					);
					set_query_var( 'cf_error', $result );
				}
			}
			//PAYPAL
			elseif( $_SESSION['payment_method'] == 'paypal' && $this->use_paypal) {

				if( $_SESSION['billing_type'] == 'recurring' ) {

					// Make CreateRecurringPaymentsProfile API call
					$result = $this->paypal_express_gateway->create_recurring_payments_profile(
					$_SESSION['cost'],
					$_SESSION['billing_period'],
					$_SESSION['billing_frequency'],
					$_SESSION['billing_agreement']
					);

				} else {
					// Make DoExpressCheckout API call
					$result = $this->paypal_express_gateway->do_express_checkout_payment( $_POST['total_amount'] );
				}

				// Handle Success and Error scenarios
				if ( $result['status'] == 'success' ) {

					// Insert/Update User
					$this->update_user($_POST,	$_POST['billing_type'], $_POST['credits'], $result
					// $_POST['credits']
					);

					// Set the proper step which will be loaded by "page-checkout.php"
					set_query_var( 'checkout_step', 'success' );

					// Destroys the $_SESSION
					$this->destroy_session();

				} else {
					// Set the proper step which will be loaded by "page-checkout.php"
					set_query_var( 'checkout_step', 'api_call_error' );
					// Pass error params to "page-checkout.php"
					set_query_var( 'checkout_error', $result );

					// Destroys the $_SESSION
					$this->destroy_session();
				}
			}
		}
		// If login attempt is made
		elseif ( isset( $_POST['recurring_submit'] ) ) {

			//AUTHORIZENET
			if($_SESSION['payment_method'] == 'cc' && $this->use_authorizenet){

			}
			//PAYPAL
			elseif($_SESSION['payment_method'] == 'paypal') {


				$key = md5(
				$options['payment_types']['paypal']['currency'] .
				'directory_123' .
				$options['payments']['recurring_cost']
				);

				//Record transaction
				$user_id = $current_user->ID;
				$transactions = new DR_Transactions($user_id);
				$transactions->billing_type = 'recurring';
				$transactions->paypal = array('key' => $key);

				$on_payment_url = empty($options['paypal']['payment_url']) ? get_permalink($Directory_Core->my_listings_page_id) : $options['paypal']['payment_url'];
				$on_cancel_url = empty($options['paypal']['cancel_url']) ? get_option( 'siteurl' ) : $options['paypal']['cancel_url'];

				$custom .= $this->session_string($_SESSION);

				// Send Recurring payment to PayPal
				$form = '';
				if ( 'live' == $options['payment_types']['paypal']['api_url'] )
				$form .= '<form action="https://www.paypal.com/cgi-bin/webscr" name="form_id" method="post">';
				else
				$form .= '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" name="form_id" method="post">';

				$form .= '<input type="hidden" name="business" value="' . esc_attr($options['payment_types']['paypal']['business_email']) .'">';
				$form .= '<input type="hidden" name="cmd" value="_xclick-subscriptions">';
				$form .= '<input type="hidden" name="item_name" value="' . esc_attr($options['payments']['recurring_name']) . '">';
				$form .= '<input type="hidden" name="item_number" value="a" >';
				$form .= '<input type="hidden" name="invoice" value="' . uniqid("LST-{$blog_id}-") . '">'; // 'LST' is the prefix for Gateway Relay
				$form .= '<input type="hidden" name="currency_code" value="' . $options['payment_types']['paypal']['currency'] .'">';
				$form .= '<input type="hidden" name="a3" value="' . $options['payments']['recurring_cost'] . '">';
				$form .= '<input type="hidden" name="p3" value="' . $options['payments']['billing_frequency'] . '">';
				$form .= '<input type="hidden" name="t3" value="' . strtoupper( $options['payments']['billing_period'] ) . '"> <!-- Set recurring payments until canceled. -->';
				$form .= '<input type="hidden" name="custom" value="' . esc_attr($custom) . '">';
				$form .= '<input type="hidden" name="return" value="' . esc_attr($on_payment_url) . '">';
				$form .= '<input type="hidden" name="cancel_return" value="' . esc_attr($on_cancel_url) . '">';
				$form .= '<input type="hidden" name="notify_url" value="' . esc_attr(admin_url('admin-ajax.php?action=directory_ipn') ) . '">';
				$form .= '<input type="hidden" name="no_shipping" value="1">';
				$form .= '<input type="hidden" name="src" value="1">';
				$form .= '</form>';
				$form .= '<script>document.form_id.submit();</script>';

				echo $form;

				// Destroys the $_SESSION
				$this->destroy_session();
				exit;
			}
		}
		// If no requests are made load default step
		else {
			// Set the proper step which will be loaded by "page-checkout.php"
			set_query_var( 'checkout_step', 'terms' );
		}

	}

	function session_string($session = array()){
		global $blog_id;

		$data = $session;
		unset($data['CC']);
		$user_id = get_current_user_id();

		$custom = "uid={$user_id}&bid={$blog_id}&";
		$custom .= http_build_query($data);
		return $custom;
	}

	/**
	* Destroy $_SESSION
	*
	* @access public
	* @return void
	*/
	function destroy_session() {
		// Unset all of the session variables.
		$_SESSION = array();

		// Destroy the session cookie, and not just the session data!
		if ( ini_get("session.use_cookies" ) ) {
			$params = session_get_cookie_params();
			setcookie( session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
			);
		}

		// Finally, destroy the session.
		session_destroy();
	}

	public function __get($property)	{

		switch($property) {

			case 'paypal_express_gateway' :
			if( $this->_paypal_express_gateway == null){
				//Load the paypal gateway
				if($this->use_paypal){
					include_once $this->plugin_dir . 'core/paypal-express-gateway.php';
					$this->_paypal_express_gateway = new Paypal_Express_Gateway( $this->paypal_options );
				}
				return $this->_paypal_express_gateway;
			}
			break;

			case 'authorizenet_gateway' :
			if( $this->_authorizenet_gateway == null){
				//Load the paypal gateway
				if($this->use_authorizenet) {

					require_once $this->plugin_dir . 'core/AuthnetXML.class.php';

					$this->_authorizenet_gateway =
					new AuthnetXML(
					$this->authorizenet_options['api_user'],   // Add your API LOGIN ID
					$this->authorizenet_options['api_key'], // Add your API transaction key
					( ($this->authorizenet_options['mode'] == 'live') ? 0 : 1) // Set Sandbox
					);
					/*
					$this->_authorizenet_gateway->setFields(
					array(
					'delim_char' => $this->authorizenet_options['delim_char'],
					'encap_char' => $this->authorizenet_options['encap_char'],
					'delim_data' => strtoupper($this->authorizenet_options['delim_data']),
					'email_customer' => strtoupper($this->authorizenet_options['email_customer']),
					'header_email_receipt' => $this->authorizenet_options['header_email_receipt'],
					'footer_email_receipt' => $this->authorizenet_options['footer_email_receipt'],
					)
					);
					*/
				}
			}
			return $this->_authorizenet_gateway;

			break;
		}
	}

}

/* Initiate Payments */
new DR_Payments();
