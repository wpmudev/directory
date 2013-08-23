<?php

class DR_Transactions{

	public $text_domain = DR_TEXT_DOMAIN;
	public $options_name = DR_OPTIONS_NAME;
	public $plugin_dir = DR_PLUGIN_DIR;
	public $plugin_url = DR_PLUGIN_URL;

	public $user_id = 0;
	public $blog_id = 0;

	protected $_transactions = null;
	protected $_credits = 0;
	protected $_credits_log = array();
	protected $_order = 0;
	protected $_status = null;
	protected $_expires = 0;
	protected $_billing_type = '';
	protected $_ordeer_info = '';
	protected $_paypal = null;
	protected $_authorizenet = null;

	protected $struc =
	array(
	'credits' => 0,

	//credits_log - array of credit purchases.
	'credits_log' => array(),

	//order- Information about the last successful order. Use expires and status to decide wheter user can add classifieds.
	'order' => array('billing_type' => '', 'billing_frequency' => '', 'billing_period' => '','payment_method' => '', 'status' => '', 'expires' => 0, 'order_info' => array() ),

	//paypal - list of successful transaction numbers and subscription ids.
	'paypal' => array('transactions' => array(), 'profile_id'   => '', 'key' => 'x'),

	//authorizenet - list of successful transaction numbers and subscription ids.
	'authorizenet' => array('transactions' => array(), 'profile_id' => ''),
	);

	function __construct($user_id = 0, $blogid=0){
		global $blog_id;

		$this->user_id = (empty($user_id)) ? get_current_user_id() : $user_id;
		$this->blog_id = (empty($blogid)) ? $blog_id : $blogid;

		//Convert from old style
		$dr_order = get_user_meta( $this->user_id, 'dr_order', true );
		$dr_credits = get_user_meta( $this->user_id, 'dr_credits', true );
		$dr_credits_log = get_user_meta( $this->user_id, 'dr_credits_log', true );

		//Blog specfic version
		$dr_transactions = get_user_option( 'dr_transactions', $this->user_id );

		// $dr_blog has entire transaction array
		if(! empty($dr_order) || ! empty($dr_credits) || ! empty($dr_credits_log) ) { // Need to convert
			$dr_transactions = $this->struc;
			$dr_transactions['credits'] = (empty($dr_credits) ) ? 0 : $dr_credits;
			$status = (empty($dr_order['order_info']['status']) ) ? '' : $dr_order['order_info']['status'];
			$expires = (empty($dr_order['time_end_annual']) ) ? 0 : $dr_order['time_end_annual'];
			$billing = (empty($dr_order['billing']) ) ? '' : $dr_order['billing'];
			$dr_transactions['order']['status'] = $status;
			$dr_transactions['order']['expires'] = $expires;
			$dr_transactions['order']['billing_type'] = $billing;
			update_user_option($this->user_id, 'dr_transactions', $dr_transactions);

			delete_user_meta($this->user_id, 'dr_order');
			delete_user_meta($this->user_id, 'dr_credits');
			delete_user_meta($this->user_id, 'dr_credits_log');
		}

		if(! $dr_transactions ){
			$dr_transactions = $this->struc;
			$options = $this->get_options('payments');
			$dr_transactions['credits'] = (empty($options['signup_credits']) ) ? 0 : $options['signup_credits'];
			update_user_option($this->user_id, 'dr_transactions', $dr_transactions);
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

	function __get( $property = '' ){

		$this->_transactions = $this->get_transactions();

		switch ($property) {
			case 'transactions' :
			return $this->_transactions;
			break;

			case 'credits' :
			$this->_credits = (empty($this->_transactions['credits']) ) ? 0 : $this->_transactions['credits'];
			return $this->_credits;
			break;

			case 'credits_log' :
			$this->_credits_log = $this->_transactions['credits_log'];
			return $this->_credits_log;
			break;

			case 'order' :
			$this->_order = $this->_transactions['order'];
			return $this->_order;
			break;

			case 'status' :
			$this->_status = $this->_transactions['order']['status'];
			return $this->_status;
			break;

			case 'expires' :
			$this->_expires = $this->_transactions['order']['expires'];
			return $this->_expires;
			break;

			case 'order_info' :
			$this->_order_info = $this->_transactions['order']['order_info'];
			return $this->_order_info;
			break;

			case 'billing_type' :
			$this->_billing_type = $this->_transactions['order']['billing_type'];
			return $this->_billing_type;
			break;

			case 'paypal' :
			$this->_paypal = $this->_transactions['paypal'];
			return $this->_paypal;
			break;

			case 'authorizenet' :
			$this->_authorizenet = $this->_transactions['authorizenet'];
			return $this->_authorizenet;
			break;
		}
	}

	function __set($property, $value){

		$this->_transactions = $this->get_transactions();

		switch ($property) {

			case 'transactions' :
			$this->_transactions = $value;
			break;

			case 'credits' :
			$added = $value - $this->_transactions['credits'];
			$this->_transactions['credits'] = $value;
			$this->_transactions['credits_log'][] = array('credits' => $added, 'date' => time() ); //Log the change
			break;

			case 'credits_log' :
			$this->_transactions['credits_log'] = $value;
			break;

			case 'status' :
			$this->_transactions['order']['status'] = $value;
			break;

			case 'expires' :
			$this->_transactions['order']['expires'] = $value;
			break;

			case 'billing_type' :
			$this->_transactions['order']['billing_type'] = $value;
			break;

			case 'order_info' :
			$this->_transactions['order']['order_info'] = $value;
			break;

			//PAYPAL
			case 'paypal' :

			//trigger_error(print_r($value, true) );

			//Dissect whatever comes in and set the appropriate items.

			if(! empty($value['key']) ) $this->_transactions['paypal']['key'] = $value['key'];

			if(! empty($value['subscr_id']) ) $this->_transactions['paypal']['profile_id'] = $value['subscr_id'];

			if(! empty($value['PROFILEID']) ) $this->_transactions['paypal']['profile_id'] = $value['PROFILEID'];

			if(! empty($value['profile_id']) ) $this->_transactions['paypal']['profile_id'] = $value['profile_id'];

			// Set transaction ID, the different API calls return different array
			// keys so we need to check all of them
			if(! empty($value['txn_id']) ) $this->_transactions['paypal']['transactions'][] = $value['txn_id'];
			elseif(! empty($value['PAYMENTINFO_0_TRANSACTIONID']) ) $this->_transactions['paypal']['transactions'][] = $value['PAYMENTINFO_0_TRANSACTIONID'];
			elseif(! empty($value['TRANSACTIONID']) ) $this->_transactions['paypal']['transactions'][] = $value['TRANSACTIONID'];

			// Sort out stuff from a paypal IPN
			if(! empty($value['custom']) ) {
				parse_str($value['custom'], $custom);
				if (is_array($custom) ) {
					if(! empty($custom['billing_type']) ) $this->_transactions['order']['billing_type'] = $custom['billing_type'];
					if(! empty($custom['billing_period']) ) $this->_transactions['order']['billing_period'] = $custom['billing_period'];
					if(! empty($custom['billing_frequency']) ) $this->_transactions['order']['billing_frequency'] = $custom['billing_frequency'];

					//recurring calculate the expiration
					if(! empty($custom['billing_period']) && ! empty($custom['billing_frequency'])) {

						$date = new DateTime;
						if(! empty($value['subscr_date']) ) $date = $date->setTimestamp(strtotime($value['subscr_date']) );

						$expiration_date = $this->get_expiration_date($this->_transactions['order']['billing_period'], $this->_transactions['order']['billing_frequency'], $date );
						$this->_transactions['order']['expires'] = $expiration_date->getTimestamp();
					}
				}
			}

			//Set status
			if(! empty($value['payment_status']) ) $this->_transactions['order']['status'] = ($value['payment_status'] == 'Completed') ? 'success' : $value['payment_status'];
			elseif(! empty($value['PAYMENTINFO_0_ACK']) ) $this->_transactions['order']['status'] = ($value['PAYMENTINFO_0_ACK'] == 'Success') ? 'success' : $value['PAYMENTINFO_0_ACK'];

			//trigger_error(print_r($this, true) );

			if($this->_transactions['order']['status'] == 'success') {
				$this->_transactions['order']['order_info'] = $value;
				$this->_transactions['order']['payment_method'] = 'paypal';

			}

			if(! empty($value['txn_type']) && in_array( $value['txn_type'], array("subscr_cancel", "subscr_failed", "subscr_eot") ) ) {
				if  ( $value['subscr_id'] == $this->_transactions['paypal']['profile_id'] ) $this->_transactions['order']['status'] = $value['txn_type'];
			}

			if($this->_transactions['order']['status'] == 'success') {

				//for affiliate subscription
				$affiliate_settings = $this->get_options( 'affiliate_settings' );
				do_action( 'directory_set_paid_member', $affiliate_settings, $user_id, $this->_transactions['order']['billing_type'] );

				$member_role = $this->get_options('general');
				$member_role = $member_role['member_role'];
				$user = get_userdata($this->user_id);
				$user->set_role($member_role);

			}

			break;

			//AUTHORIZENET
			case 'authorizenet' :

			if(is_array($value) ){
				$this->_transactions['order']['status'] = (! empty($value['x_response_code']) && $value['x_response_code'] == '1' ) ? 'success' : 'failed';
				if($this->_transactions['order']['status'] == 'success') {
					$this->_transactions['order']['order_info'] = $value;
					$this->_transactions['order']['payment_method'] = 'authorizenet';
					if(! empty($value['x_trans_id']) ) $this->_transactions['authorizenet']['transactions'][] = $value['x_trans_id'];

					if($this->_transactions['order']['billing_type'] == 'recurring'){
						$expiration_date = $this->get_expiration_date($this->_transactions['order']['billing_period'], $this->_transactions['order']['billing_frequency'] );
						$this->_transactions['order']['expires'] = $expiration_date->getTimestamp();

						//						print_r($this->_transactions['order']['expires']);
					}

				}
			}


			//As XML object
			if( is_a($value, 'AuthnetXML') !== false) { //First is it from AuthNet XML?
				//Dissect whatever comes in and set the appropriate items.

				if((string)$value->transactionResponse->responseCode ){ //AIM

					$this->_transactions['order']['status'] = ($value->isSuccessful() && (string)$value->transactionResponse->responseCode == '1' ) ? 'success' : 'failed';

					if($this->_transactions['order']['status'] == 'success') {
						$this->_transactions['order']['order_info'] = (string)$value;
						$this->_transactions['order']['payment_method'] = 'authorizenet';
						$this->_transactions['authorizenet']['transactions'][] = (string)$value->transactionResponse->transId;
						$this->_transactions['authorizenet']['key'] = (string)$value->refId; //Invoice number

					}

					foreach($value->transactionResponse->userFields->userField as $userField){
						$this->_transactions['order'][(string)$userField->name] = (string)$userField->value;
					}

					if($this->_transactions['order']['billing_type'] == 'recurring'){
						//This is the first charge on the subscription
						//expiration date calculated from today

						$expiration_date = $this->get_expiration_date($this->_transactions['order']['billing_period'], $this->_transactions['order']['billing_frequency'] );
						$this->_transactions['order']['expires'] = $expiration_date->getTimestamp();

					}
				} elseif((string)$value->subscriptionId ){ //ARB
					$this->_transactions['order']['status'] = ($value->isSuccessful() && (string)$value->subscriptionId) ? 'success' : 'failed';

					if($this->_transactions['order']['status'] == 'success') {
						$this->_transactions['authorizenet']['profile_id'] = (string)$value->subscriptionId; //Subscription ID
					}

				}
			}

			if($this->_transactions['order']['status'] == 'success') {

				//for affiliate subscription
				$affiliate_settings = $this->get_options( 'affiliate_settings' );
				do_action( 'directory_set_paid_member', $affiliate_settings, $user_id, $this->_transactions['order']['billing_type'] );

				$member_role = $this->get_options('general');
				$member_role = $member_role['member_role'];
				$user = get_userdata($this->user_id);
				$user->set_role($member_role);

			}

			//			print_r($value.'');

			break;

		}
		return $this->update_transactions($this->_transactions);
	}

	function get_expiration_date($billing_period, $billing_frequency, $from_date = null){
		if(empty($from_date) ) $from_date = new DateTime(); // assume now.
		
		$from_date->modify("+{$billing_frequency} {$billing_period}" );

		//$period = 'P' . $billing_frequency . substr($billing_period, 0, 1 ); //ISO interval
		//$from_date->add(new DateInterval($period) );
		//$from_date->add(new DateInterval('P3D') ); // 3 day grace period
		return $from_date;
	}

	function __isset($property){
		return ( in_array($property, array(
		'transactions',
		'credits',
		'credits_log',
		'order',
		'order_info',
		'status',
		'expires',
		'billing_type',
		'billing_period',
		'billing_frequency',
		'paypal',
		'authorizenet',
		)
		) );
	}
	/**
	* Get Directory Transactions
	*
	*/

	protected function get_transactions(){
		return get_user_option( 'dr_transactions', $this->user_id );
	}

	/**
	* Update Directory Transactions
	*
	*/
	protected function update_transactions($transactions= null){
		if(empty($transactions) ) return;
		return update_user_option($this->user_id, 'dr_transactions', $transactions );
	}
}
