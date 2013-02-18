<?php
/**
* Load core DB data. Only loaded during Activation
* 
*/


if ( !class_exists('Directory_Core_Data') ):

class Directory_Core_Data {

	/**
	* Load initial Content Types Directory data for plugin
	*
	* @return void
	*/

	function __construct() {
		//Load default data if object instatiated. Set to only happen during plugin Activate
		add_action( 'init', array( &$this, 'load_data' ) );
		add_action( 'init', array( &$this, 'load_payment_data' ) );
		add_action( 'init', array( &$this, 'load_mu_plugins' ) );
	}

	/**
	* Load initial Content Types data for plugin
	*
	* @return void
	*/
	function load_data() {
		/* Get setting options. If empty return an array */
		$options = ( get_site_option( DR_OPTIONS_NAME ) ) ? get_site_option( DR_OPTIONS_NAME ) : array();

		// Check whether post types are loaded

		if (! post_type_exists('directory_listing') ){

			$directory_listing_default =
			array (
			'can_export' => true,
			'capability_type' => 'listing',
			'description' => 'Directory Listing post type.',
			'has_archive' => 'listings',
			'hierarchical' => false,
			'map_meta_cap' => true,
			'menu_position' => '',
			'public' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'listing', 'with_front' => false ),

			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', /*'post-formats'*/ ),

			'labels' => array(
			'name'          => __('Listings', DR_TEXT_DOMAIN ),
			'singular_name' => __('Listing', DR_TEXT_DOMAIN ),
			'add_new'       => __('Add New', DR_TEXT_DOMAIN ),
			'add_new_item'  => __('Add New Listing', DR_TEXT_DOMAIN ),
			'edit_item'     => __('Edit Listing', DR_TEXT_DOMAIN ),
			'new_item'      => __('New Listing', DR_TEXT_DOMAIN ),
			'view_item'     => __('View Listing', DR_TEXT_DOMAIN ),
			'search_items'  => __('Search Listings', DR_TEXT_DOMAIN ),
			'not_found'     => __('No listings found', DR_TEXT_DOMAIN ),
			'not_found_in_trash' => __('No listings found in trash', DR_TEXT_DOMAIN ),
			)
			);

			//Update custom post types
			if(is_network_admin()){
				$ct_custom_post_types = get_site_option( 'ct_custom_post_types' );
				$ct_custom_post_types['directory_listing'] = $directory_listing_default;
				update_site_option( 'ct_custom_post_types', $ct_custom_post_types );
			} else {
				$ct_custom_post_types = get_option( 'ct_custom_post_types' );
				$ct_custom_post_types['directory_listing'] = $directory_listing_default;
				update_option( 'ct_custom_post_types', $ct_custom_post_types );
			}

			// Update post types and delete tmp options
			flush_network_rewrite_rules();
		}

		/* Check whether taxonomies data is loaded */

		if ( ! taxonomy_exists('listing_tag')){

			$listing_tags_default = array();
			$listing_tags_default['object_type'] = array ( 'directory_listing');
			$listing_tags_default['args'] = array(
			'public' => true,
			'hierarchical' => false,
			'rewrite'       => array( 'slug' => 'listings-tag', 'with_front' => false, 'hierarchical' => false ),
			'query_var' => true,
			'capabilities'  => array( 'assign_terms' => 'edit_listings' ),

			'labels'        => array(
			'name'          => __( 'Listing Tags', DR_TEXT_DOMAIN ),
			'singular_name' => __( 'Listing Tag', DR_TEXT_DOMAIN ),
			'search_items'  => __( 'Search Listing Tags', DR_TEXT_DOMAIN ),
			'popular_items' => __( 'Popular Listing Tags', DR_TEXT_DOMAIN ),
			'all_items'     => __( 'All Listing Tags', DR_TEXT_DOMAIN ),
			'edit_item'     => __( 'Edit Listing Tag', DR_TEXT_DOMAIN ),
			'update_item'   => __( 'Update Listing Tag', DR_TEXT_DOMAIN ),
			'add_new_item'  => __( 'Add New Listing Tag', DR_TEXT_DOMAIN ),
			'new_item_name' => __( 'New Listing Tag Name', DR_TEXT_DOMAIN ),
			'add_or_remove_items' => __( 'Add or remove listing tags', DR_TEXT_DOMAIN ),
			'choose_from_most_used' => __( 'Choose from the most used listing tags', DR_TEXT_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate listing tags with commas', DR_TEXT_DOMAIN ),
			)
			);

			if(is_network_admin()){
				$ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['listing_tag'] = $listing_tags_default;
				update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			} else {
				$ct_custom_taxonomies = get_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['listing_tag'] = $listing_tags_default;
				update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			}

			// Update post types and delete tmp options
			flush_network_rewrite_rules();

		}

		if ( ! taxonomy_exists('listing_category')){

			$listing_category_default = array();
			$listing_category_default['object_type'] = array ('directory_listing');
			$listing_category_default['args'] = array(
			'public' => true,
			'hierarchical'  => true,
			'rewrite'       => array( 'slug' => 'listings-category', 'with_front' => false, 'hierarchical' => true ),
			'query_var' => true,
			'capabilities'  => array( 'assign_terms' => 'edit_listings' ),

			'labels' => array(
			'name'          => __( 'Listing Categories', DR_TEXT_DOMAIN ),
			'singular_name' => __( 'Listing Category', DR_TEXT_DOMAIN ),
			'search_items'  => __( 'Search Listing Categories', DR_TEXT_DOMAIN ),
			'popular_items' => __( 'Popular Listing Categories', DR_TEXT_DOMAIN ),
			'all_items'     => __( 'All Listing Categories', DR_TEXT_DOMAIN ),
			'parent_item'   => __( 'Parent Category', DR_TEXT_DOMAIN ),
			'edit_item'     => __( 'Edit Listing Category', DR_TEXT_DOMAIN ),
			'update_item'   => __( 'Update Listing Category', DR_TEXT_DOMAIN ),
			'add_new_item'  => __( 'Add New Listing Category', DR_TEXT_DOMAIN ),
			'new_item_name' => __( 'New Listing Category', DR_TEXT_DOMAIN ),
			'parent_item_colon'   => __( 'Parent Category:', DR_TEXT_DOMAIN ),
			'add_or_remove_items' => __( 'Add or remove listing categories', DR_TEXT_DOMAIN ),
			)
			);

			if(is_network_admin()){
				$ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['listing_category'] = $listing_category_default;
				update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			} else {
				$ct_custom_taxonomies = get_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['listing_category'] = $listing_category_default;
				update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			}

			flush_network_rewrite_rules();
		}

		//Custompress specfic
		if(is_multisite()){
			update_site_option( 'allow_per_site_content_types', true );
			update_site_option( 'display_network_content_types', true );

		}

		flush_network_rewrite_rules();

	}

	function load_payment_data() {

		$options = get_option( DR_OPTIONS_NAME );
		$options = ( is_array($options) ) ? $options : array();

		//General default
		if(empty($options['general']) ){
			$options['general'] = array(
			'member_role'             => 'subscriber',
			'moderation'              => array('publish' => 1, 'pending' => 1, 'draft' => 1 ),
			'custom_fields_structure' => 'table',
			'welcome_redirect'        => 'true',
			'key'                     => 'general'
			);
		}

		//Update from older version
		if (! empty($options['general_settings']) ) {
			$options['general'] = array_merge($options['general'], $options['general_settings']);
			unset($options['general_settings']);
		}

		//Default Payments settings
		if ( empty( $options['payments'] ) ) {
			$options['payments'] = array(
			'enable_recurring'    => '1',
			'recurring_cost'      => '9.99',
			'recurring_name'      => 'Subscription',
			'billing_period'      => 'Month',
			'billing_frequency'   => '1',
			'billing_agreement'   => 'Customer will be billed at “9.99 per month for 2 years”',
			'enable_one_time'     => '1',
			'one_time_cost'       => '99.99',
			'one_time_name'       => 'One Time Only',
			'enable_credits'      => '1',
			'cost_credit'         => '.99',
			'credits_per_listing' => 1,
			'signup_credits'      => 0,
			'credits_description' => '',
			'tos_content'       => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.',
			'key'               => 'payments'
			);
		}

		if (! empty($options['payment_settings']) ) {
			$options['payments'] = array_merge($options['payments'], $options['payment_settings'] );
			unset($options['payment_settings']);
		}

		if(empty($options['payment_types']) ) {
			$options['payment_types'] = array(
			'use_free'         => 1,
			'use_paypal'       => 0,
			'use_authorizenet' => 0,
			'paypal'           => array('api_url' => 'sandbox', 'api_username' => '', 'api_password' => '', 'api_signature' => '', 'currency' => 'USD'),
			'authorizenet'     => array('mode' => 'sandbox', 'delim_char' => ',', 'encap_char' => '', 'email_customer' => 'yes', 'header_email_receipt' => 'Thanks for your payment!', 'delim_data' => 'yes'),
			);
		}

		if ( ! empty($options['paypal']) ){
			$options['payment_types']['paypal'] = array_merge($options['payment_types']['paypal'], $options['paypal']);
			unset($options['paypal']);
		}

		update_option( DR_OPTIONS_NAME, $options );
	}
	
	function load_mu_plugins(){

	if(!is_dir(WPMU_PLUGIN_DIR . '/logs')):
		mkdir(WPMU_PLUGIN_DIR . '/logs', 0755, true);
	endif;
		
	copy(	DR_PLUGIN_DIR . 'mu-plugins/gateway-relay.php', WPMU_PLUGIN_DIR .'/gateway-relay.php');
	copy(	DR_PLUGIN_DIR . 'mu-plugins/wpmu-assist.php', WPMU_PLUGIN_DIR .'/wpmu-assist.php');
		
	}
	
}

endif;
