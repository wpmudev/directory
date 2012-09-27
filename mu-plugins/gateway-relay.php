<?php
/*
Plugin Name: Gateway Relay
Plugin URI: http://premium.wpmudev.org/project/classifieds
Description: Forwards IPN from Paypal and Silent Posts from Authorizenet to multiple new urls based on a prefix in certain fields.
Version: 1.0
Author: Arnold Bailey (Incsub)
Author URI: http://premium.wpmudev.org
License: GNU General Public License (Version 2 - GPLv2)
Network: true
*/

/**
* You must include a prefix in one of the custom fields to forward it on correctly.
* Multiple url destinations per prefix can be registered.
*
* For Paypal 'PROFILEREFERENCE' (rp_invoice_id), 'custom','INVNUM' (invoice), 'item_number' (for subscriptions) will be checked in the IPN response.
*
* For Authorize Net 'x_invoice_num', 'x_po_num', 'x_cust_id' will be checked for the prefix.
*
* @register_gateway_relay($prefix, $url, $password) registers a prefix, the url to send to and a password you can use to authenticate. Duplicate combinations of the prefix and url are forbidden.
*
* @unregister_gateway_relay($prefix, $url, $password) removes a prefix, an url and password combination.
*/

if( ! defined('GATEWAY_RELAY_SETTINGS_NAME') ):

define('GATEWAY_RELAY_SETTINGS_NAME', 'gr_settings' );
define('GATEWAY_RELAY_QUEUE_NAME', 'gr_queue' );

class Gateway_Relay{

	const PAYPAL_LIVE = "https://www.paypal.com/cgi-bin/webscr";
	const PAYPAL_SANDBOX = "https://www.sandbox.paypal.com/cgi-bin/webscr";

	public $settings = null;
	public $settings_name = GATEWAY_RELAY_SETTINGS_NAME;

	public $queue = null;
	public $queue_name = GATEWAY_RELAY_QUEUE_NAME;

	public $postdata = '';
	public $source = '';

	function Gateway_Relay(){__construct();}

	function __construct(){

		add_action( 'wp_ajax_nopriv_gateway_relay', array( &$this, 'process' ) );
		add_action( 'wp_ajax_gateway_relay', array( &$this, 'process' ) );

	}

	function process(){

		//identify the source
		if (isset($_POST['txn_id']) ) $this->source = 'paypal';
		elseif (isset($_POST['x_trans_id']) ) $this->source = 'authnet';
		else return;

		$this->postdata = http_build_query($_POST);

		switch ($this->source){
			//PAYPAL
			case'paypal': {

				//Check if from sandbox Class C IPs
				$url = ( strpos($_SERVER['REMOTE_ADDR'], '173.0.82.') === 0 ) ? self::PAYPAL_SANDBOX : self::PAYPAL_LIVE;

				$args =  array('timeout' => 90, 'sslverify' => false );

				$response = wp_remote_get( $url . "?" . $this->postdata, $args );

				if(is_wp_error($response) ){
					$this->write_to_log(print_r($response, true));
				}	else {
					$response = $response['body'];
				}

				if ( $response != "VERIFIED" ) {
					$this->write_to_log(print_r($response, true) );
					die( 'not VERIFIED' );
				}

				//Verified add to queue
				$this->add_to_queue($this->source, $this->postdata);

				break;
			}

			//AUTHORIZE NET
			case 'authnet': {
				$this->add_to_queue($this->source, $this->postdata);
				break;
			}

			//UNKNOWN
			default: {
				break;
			}
		}

		$this->write_to_log( $this->postdata, $this->source);

		for($i=0;$i <= count($this->queue); $i++){
			$this->process_queue();
		}

		exit;

	}

	function add_to_queue($source, $postdata, $retries = 5 ){
		$this->settings = get_site_option($this->settings_name);

		parse_str($postdata, $params);
		$apps = array();
		//Get the apps that match
		foreach($this->settings['apps'] as $key => $app){
			$found = false;
			if($source == 'paypal'){
				$found = (strpos($params['rp_invoice_id'], $app['prefix']) === 0
				|| strpos($params['custom'], $app['prefix']) === 0
				|| strpos($params['item_number'], $app['prefix']) === 0
				|| strpos($params['invoice'], $app['prefix']) === 0);
			}
			elseif($source == 'authnet'){
				$found = (strpos($params['x_invoice_num'], $app['prefix']) === 0
				|| strpos($params['x_po_num'], $app['prefix']) === 0
				|| strpos($params['x_cust_id'], $app['prefix']) === 0);
			}
			if($found) $apps[] = $app;
		}
		$this->queue = get_site_option($this->queue_name);
		$item = array('source' => $source, 'postdata' => $postdata, 'retries' => $retries, 'apps' => $apps );
		$this->queue[] = $item;
		update_site_option($this->queue_name, $this->queue);
		$this->queue = get_site_option($this->queue_name);
	}

	function process_queue(){
		$this->settings = get_site_option($this->settings_name);

		$this->queue = get_site_option($this->queue_name);
		if(empty($this->queue) ) return;
		$q = array_shift($this->queue);
		update_site_option($this->queue_name, $this->queue);

		foreach($q['apps'] as $key => $app){

			$args =  array('timeout' => 90, 'sslverify' => false, 'body' => $q['postdata'] . "&relay_pass={$app['password']}" );

			$response = wp_remote_post( $app['url'], $args );

			if(is_wp_error($response) ){
				$this->write_to_log(print_r($response, true));
			}	else {
				if($response['response']['code'] == 200) unset($q['apps'][$key]);
			}
		}
		$q['retries']--;
		if($q['retries'] > 0 && ! empty($q['apps']) ) {
			$this->queue = get_site_option($this->queue_name);
			$this->queue[] = $q;
			update_site_option($this->queue_name, $this->queue);
		}
	}

	function write_to_log($error, $log = 'error') {

		//create filename for each month
		$filename = __DIR__ . "/logs/{$log}_" . date('Y_m') . '.log';

		//add timestamp to error
		$message = gmdate('[Y-m-d H:i:s] ') . $error;

		//write to file
		file_put_contents($filename, $message . "\n", FILE_APPEND);
	}

}


function register_gateway_relay($prefix = '', $url='', $password=''){
	$settings = get_site_option(GATEWAY_RELAY_SETTINGS_NAME);

	$item = array('prefix' => $prefix, 'url' => $url, 'password' => $password );

	foreach($settings['apps'] as $key => $value) {
		if($value['prefix'] == $item['prefix'] && $value['url'] == $item['url']) return false;
	}
	$settings['apps'][] = $item;
	update_site_option(GATEWAY_RELAY_SETTINGS_NAME, $settings);
	return true;
}

function unregister_gateway_relay($prefix = '', $url='', $password=''){
	$settings = get_site_option(GATEWAY_RELAY_SETTINGS_NAME);
	$item = array('prefix' => $prefix, 'url' => $url);

	foreach($settings['apps'] as $key => $value) {
		if($value == $item) {
			unset($settings['apps'][$key]);
			update_site_option(GATEWAY_RELAY_SETTINGS_NAME, $settings);
			return true;
		}
	}
	return false;
}

new Gateway_Relay;

endif;
