<?php

/**
* Directory_Core
*
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub) {@link http://premium.wpmudev.org}
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

if (! class_exists('Directory_Core')):

class Directory_Core {

	//** @public string $plugin_url Plugin URL */
	public $plugin_url = DR_PLUGIN_URL;
	/** @public string $plugin_dir Path to plugin directory */
	public $plugin_dir = DR_PLUGIN_DIR;
	//** @public string $text_domain The text domain for strings localization */
	public $text_domain = DR_TEXT_DOMAIN;
	/** @public string Name of options DB entry */
	public $options_name = DR_OPTIONS_NAME;

	public $is_directory_page = false;
	public $directory_template;
	public $dr_first_thumbnail;

	//** @public string post_name of Listing Page
	public $directory_page_id = 0;
	//** @public int post_id of Listing Page
	public $directory_page_slug = '';

	//** @public string post_name of Add Listing Page
	public $add_listing_page_id = 0;
	//** @public int post_id of Add Listing Page
	public $add_listing_page_slug = '';

	//** @public string post_name of Edit Listing Page
	public $edit_listing_page_id = 0;
	//** @public int post_id of Edit Listing Page
	public $edit_listing_page_slug = '';

	//** @public string post_name of Add Listing Page
	public $my_listings_page_id = 0;
	//** @public int post_id of Add Listing Page
	public $my_listings_page_slug = '';

	//** @public string post_name of Signup Page
	public $signup_page_id = 0;
	//** @public int post_id of Signup Page
	public $signup_page_slug = '';

	//** @public string post_name of Signin Page
	public $signin_page_id = 0;
	//** @public int post_id of Signin Page
	public $signin_page_slug = '';


	/**
	* Constructor.
	*/
	function Directory_Core() { __construct(); }

	function __construct(){

		register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
		register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );

		//show "Directory Theme" only for old installation
		if ( 'Directory Theme' == get_current_theme() )
		{
			register_theme_directory( $this->plugin_dir . 'themes' );
		}

		/* Create neccessary pages */
		add_action( 'wp_loaded', array( &$this, 'create_default_pages' ) );
		add_action( 'parse_request', array( &$this, 'on_parse_request' ) );
		add_action( 'parse_query', array( &$this, 'on_parse_query' ) );

		add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( &$this, 'init' ) );
		add_action('after_setup_theme', array(&$this, 'thumbnail_support') );

		add_filter('comment_form_defaults', array($this,'review_defaults'));

		//Shortcodes
		add_shortcode( 'dr_list_categories', array( &$this, 'list_categories_sc' ) );
		add_shortcode( 'dr_listings_btn', array( &$this, 'listings_btn_sc' ) );
		add_shortcode( 'dr_add_listing_btn', array( &$this, 'add_listing_btn_sc' ) );
		add_shortcode( 'dr_my_listings_btn', array( &$this, 'my_listings_btn_sc' ) );
		add_shortcode( 'dr_profile_btn', array( &$this, 'profile_btn_sc' ) );
		add_shortcode( 'dr_logout_btn', array( &$this, 'logout_btn_sc' ) );
		add_shortcode( 'dr_signin_btn', array( &$this, 'signin_btn_sc' ) );
		add_shortcode( 'dr_signup_btn', array( &$this, 'signup_btn_sc' ) );


		if ( is_admin() )	return;


		/* Enqueue styles */
		add_action( 'wp_enqueue_scripts', array( &$this, 'add_js_css' ) );

		add_action( 'template_redirect', array( &$this, 'template_redirect' ),12 );

		/* Handle requests for plugin pages */
		add_action( 'template_redirect', array( &$this, 'handle_page_requests' ) );


		add_action( 'template_redirect', array( &$this, 'load_directory_templates' ),14 );

		//hide some menu pages
		add_filter( 'wp_page_menu_args', array( &$this, 'hide_menu_pages' ), 99 );

		//add menu items
		add_filter( 'wp_list_pages', array( &$this, 'filter_list_pages' ), 10, 2 );

	}

	/**
	* Modifies the Comment defaults text for directory_listing post_type
	*
	*/
	function review_defaults($defaults){
		global $id;

		$post_id = $id;
		if(get_post_type($post_id) != 'directory_listing') return $defaults;

		$defaults['title_reply'] = __('Write a Review', $this->text_domain);
		$defaults['label_submit'] = __('Post Review', $this->text_domain);
		$defaults['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', $this->text_domain ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>';
		$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x( 'Review', $this->text_domain ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		$defaults['cancel_reply_link'] = __( 'Cancel review', $this->text_domain );

		return $defaults;
	}

	/**
	* adds our links to theme nav menus using wp_list_pages()
	**/
	function filter_list_pages( $list, $args ) {

		if ( current_user_can( 'edit_published_listings' ) ) {
			$directory_page = get_page($this->directory_page_id);


			if ( $args['depth'] == 1 )
			return $list;

			$link = '<a href="' . $directory_page->guid . '">' . $directory_page->post_title . '</a>';

			$temp_break = strpos( $list, $link );

			//if we can't find the page for some reason try with other link
			if ( $temp_break === false ) {
				$link = '<a href="' . $directory_page->guid . '" title="' . $directory_page->post_title . '">' . $directory_page->post_title . '</a>';
				$temp_break = strpos( $list, $link );

				//if we can't find the page for some reason skip
				if ( $temp_break === false )
				return $list;
			}


			$break = strpos( $list, '</a>', $temp_break ) + 4;

			$nav = substr( $list, 0, $break );
			/*
			$nav .= '<ul class="children">';
			$nav .= '<li class="page_item"><a href="' . site_url() . '/my-listings/" title="' . __( 'My Listings', $this->text_domain ) . '">' . __( 'My Listings', $this->text_domain ) . '</a></li>';
			$nav .= '<li class="page_item"><a href="' . site_url() . '/add-listing/" title="' . __( 'Add Listing', $this->text_domain ) . '">' . __( 'Add Listing', $this->text_domain ) . '</a></li>';
			$nav .= '</ul>';
			*/
			$nav .= substr( $list, $break );
			return $nav;
		}
		return $list;
	}


	/**
	* including JS/CSS
	**/
	function add_js_css() {
		//including CSS
		wp_enqueue_style( 'dr_style', $this->plugin_url . 'ui-front/style.css' );
		wp_enqueue_style( 'jquery-taginput', $this->plugin_url . 'ui-front/css/jquery.tagsinput.css' );
		wp_enqueue_script('directory-post', $this->plugin_url . 'ui-front/js/directory-post.js', array('jquery') );
	}

	/**
	* Hide some menu pages
	*/
	function hide_menu_pages( $args ) {

		$pages = (empty($args['exclude'])) ? '' : $args['exclude'];
		$pages .= ',' . $this->edit_listing_page_id;

		if ( is_user_logged_in() ) {
			$pages .= ',' . $this->signup_page_id;
			$pages .= ',' . $this->signin_page_id;
		}
		$args['exclude'] = trim($pages,',');

		return $args;
	}

	function on_parse_query(){
		global $wp_query;

		if ( isset( $wp_query ) ) {
			//Handle any security redirects
			if( is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id) || is_page($this->my_listings_page_id)){
				if (! is_user_logged_in()) {
					wp_redirect( get_permalink($this->signin_page_id) );
					exit;
				}
			}

			//Are we adding a Listing?
			if (is_page($this->add_listing_page_id)) {
				if(! current_user_can('publish_listings')){
					wp_redirect( get_permalink($this->my_listings_page_id) );
					exit;
				}
			}

			//Or are we editing a listing?
			if(is_page($this->edit_listing_page_id)){
				//Can the user edit listings?
				if ( ! $this->user_can_edit_listing( $_POST['post_id'] ) ) {
					wp_redirect( get_permalink($this->my_listings_page_id) );
					exit;
				}
			}

			//Or are we editing a listing?
			if(is_page($this->directory_page_id)){
				wp_redirect( get_post_type_archive_link('directory_listing') );
				exit;

			}
		}
	}

	function on_parse_request($wp){

		/*
		handle_action_buttons_requests
		If your want to go to admin profile
		*/
		if ( isset( $_POST['redirect_profile'] ) ) {
			wp_redirect( admin_url() . 'profile.php' );
			exit();
		}
		elseif ( isset( $_POST['redirect_listing'] ) ) {
			wp_redirect( get_permalink($this->add_listing_page_id) );
			exit();
		}
		elseif ( isset( $_POST['directory_logout'] ) ) {
			$options = get_option( DR_OPTIONS_NAME );

			wp_logout();

			if ( empty($options['general_settings']['logout_url']) )
			wp_redirect( home_url() );
			else
			wp_redirect( $options['general_settings']['logout_url'] );

			exit();
		}
	}

	/**
	* Intiate plugin.
	*
	* @return void
	*/
	function init() {

		$directory_listing_default = array(
		'public' => ( get_option( 'dp_options' ) ) ? false : true,
		'rewrite' => array( 'slug' => 'listings', 'with_front' => false ),
		'has_archive' => true,

		'capability_type' => 'listing',
		'capabilities' => array( 'edit_posts' => 'edit_published_listings' ),
		'map_meta_cap' => true,

		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', /*'post-formats'*/ ),

		'labels' => array(
		'name'          => __('Listings', $this->text_domain ),
		'singular_name' => __('Listing', $this->text_domain ),
		'add_new'       => __('Add New', $this->text_domain ),
		'add_new_item'  => __('Add New Listing', $this->text_domain ),
		'edit_item'     => __('Edit Listing', $this->text_domain ),
		'new_item'      => __('New Listing', $this->text_domain ),
		'view_item'     => __('View Listing', $this->text_domain ),
		'search_items'  => __('Search Listings', $this->text_domain ),
		'not_found'     => __('No listings found', $this->text_domain ),
		'not_found_in_trash' => __('No listings found in trash', $this->text_domain ),
		)
		);


		//Register directory_listing post type
		$ct_custom_post_types = get_option( 'ct_custom_post_types' );

		if ( isset( $ct_custom_post_types['directory_listing'] ) ) {

			//don't change position in menu
			if ( isset( $ct_custom_post_types['directory_listing']['menu_position'] ) && 0 == $ct_custom_post_types['directory_listing']['menu_position'] )
			$ct_custom_post_types['directory_listing']['menu_position'] = '';

			register_post_type( 'directory_listing', $ct_custom_post_types['directory_listing'] );
		} else {
			register_post_type( 'directory_listing', $directory_listing_default );
			$ct_custom_post_types['directory_listing'] = $directory_listing_default;
			update_option( 'ct_custom_post_types', $ct_custom_post_types );
		}

		$ct_custom_taxonomies = get_option('ct_custom_taxonomies');

		if (empty($ct_custom_taxonomies['listing_tag'])){
			$listing_tag = array();
			$listing_tag['object_type'] = array('directory_listing');
			$listing_tag['args'] = array(
			'rewrite'       => array( 'slug' => 'listings-tag', 'with_front' => false, 'hierarchical' => false ),
			'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
			'hierarchical' => false,
			'query_var' => true,
			'public' => true,
			'labels'        => array(
			'name'          => __( 'Listing Tags', $this->text_domain ),
			'singular_name' => __( 'Listing Tag', $this->text_domain ),
			'search_items'  => __( 'Search Listing Tags', $this->text_domain ),
			'popular_items' => __( 'Popular Listing Tags', $this->text_domain ),
			'all_items'     => __( 'All Listing Tags', $this->text_domain ),
			'edit_item'     => __( 'Edit Listing Tag', $this->text_domain ),
			'update_item'   => __( 'Update Listing Tag', $this->text_domain ),
			'add_new_item'  => __( 'Add New Listing Tag', $this->text_domain ),
			'new_item_name' => __( 'New Listing Tag Name', $this->text_domain ),
			'separate_items_with_commas' => __( 'Separate listing tags with commas', $this->text_domain ),
			'add_or_remove_items'        => __( 'Add or remove listing tags', $this->text_domain ),
			'choose_from_most_used'      => __( 'Choose from the most used listing tags', $this->text_domain ),
			)
			);

			$ct_custom_taxonomies['listing_tag'] = $listing_tag;
			update_option('ct_custom_taxonomies', $ct_custom_taxonomies);
			register_taxonomy( 'listing_tag', 'directory_listing', $ct_custom_taxonomies['listing_tag']['args']);
		}

		if (empty($ct_custom_taxonomies['listing_category'])){
			$listing_category = array();
			$listing_category['object_type'] = array('directory_listing');
			$listing_category['args'] = array(
			'rewrite'       => array( 'slug' => 'listings-category', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
			'hierarchical'  => true,
			'query_var' => true,
			'public' => true,
			'labels' => array(
			'name'          => __( 'Listing Categories', $this->text_domain ),
			'singular_name' => __( 'Listing Category', $this->text_domain ),
			'search_items'  => __( 'Search Listing Categories', $this->text_domain ),
			'popular_items' => __( 'Popular Listing Categories', $this->text_domain ),
			'all_items'     => __( 'All Listing Categories', $this->text_domain ),
			'parent_item'   => __( 'Parent Category', $this->text_domain ),
			'edit_item'     => __( 'Edit Listing Category', $this->text_domain ),
			'update_item'   => __( 'Update Listing Category', $this->text_domain ),
			'add_new_item'  => __( 'Add New Listing Category', $this->text_domain ),
			'new_item_name' => __( 'New Listing Category', $this->text_domain ),
			'parent_item_colon'        => __( 'Parent Category:', $this->text_domain ),
			'add_or_remove_items' => __( 'Add or remove listing categories', $this->text_domain ),
			)
			);

			$ct_custom_taxonomies['listing_category'] = $listing_category;
			update_option('ct_custom_taxonomies', $ct_custom_taxonomies);
			register_taxonomy( 'listing_category', 'directory_listing', $ct_custom_taxonomies['listing_category']['args']);
		}


		//import data from old plugin version
		$this->import_from_old();

	}

	/**
	* Create the default Directory pages.
	*
	* @return void
	**/
	function create_default_pages() {
		/* Create neccessary pages */

		$directory_page = $this->get_page_by_meta( 'listings' );
		$parent_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;
		$current_user = wp_get_current_user();

		if ( empty($parent_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Listings',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$parent_id = wp_insert_post( $args );
			add_post_meta( $parent_id, "directory_page", "listings" );
		}

		$this->directory_page_id = $parent_id; //Remember the number
		$this->directory_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'add_listing' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Add Listing',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			add_post_meta( $page_id, "directory_page", "add_listing" );
		}

		$this->add_listing_page_id = $page_id; //Remember the number
		$this->add_listing_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'edit_listing' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => 'Edit Listing',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			add_post_meta( $page_id, "directory_page", "edit_listing" );
		}

		$this->edit_listing_page_id = $page_id; //Remember the number
		$this->edit_listing_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'my_listings' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'My Listings',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			add_post_meta( $page_id, "directory_page", "my_listings" );
		}

		$this->my_listings_page_id = $page_id; //Remember the number
		$this->my_listings_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'signup' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Signup',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			add_post_meta( $page_id, "directory_page", "signup" );
		}

		$this->signup_page_id = $page_id; //Remember the number
		$this->signup_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'signin' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Signin',
			'post_status'    => 'publish',
			'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			add_post_meta( $page_id, "directory_page", "signin" );
		}

		$this->signin_page_id = $page_id; //Remember the number
		$this->signin_page_slug = $directory_page->post_name; //Remember the slug

	}

	/**
	* Get page by meta value
	*
	* @return int $page[0] /bool false
	*/
	function get_page_by_meta( $value ) {
		$post_statuses = array( 'publish', 'trash', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit' );
		foreach ( $post_statuses as $post_status ) {
			$args = array(
			'hierarchical'  => 0,
			'meta_key'      => 'directory_page',
			'meta_value'    => $value,
			'post_type'     => 'page',
			'post_status'   => $post_status
			);

			$page = get_pages( $args );

			if ( isset( $page[0] ) && 0 < $page[0]->ID )
			return $page[0];
		}
		return false;
	}

	/**
	* Import data from old version to new.
	*
	* @return void
	*/
	function import_from_old() {
		//update children of categories
		if ( get_option( 'dr_cache_update' ) ) {
			clean_term_cache( get_option( 'dp_cache_update' ), 'listing_category' );
			delete_option( 'dr_cache_update' );
		}

		if ( get_option( 'dp_options' ) && isset( $_POST['install_dir2'] ) ) {
			global $wpdb;
			//put top level Categories and Tags
			$old_taxonomies = get_option( 'ct_custom_taxonomies' );

			if ( $old_taxonomies ) {

				$new_taxonomies = array();

				foreach( $old_taxonomies as $old_taxonomy => $old_taxonomy_args ) {
					$args = array(
					'description'   => '',
					'slug'          => $old_taxonomy_args['args']['labels']['slug'],
					'parent'        => 0
					);

					if ( true == $old_taxonomy_args['args']['hierarchical'] ) {
						//add main level category
						$new_taxonomy = wp_insert_term( $old_taxonomy_args['args']['labels']['name'], 'listing_category', $args );

						if ( is_array( $new_taxonomy ) ) {
							//add child levels of Categories
							$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_category', parent=%d WHERE taxonomy = '%s' AND parent=0", $new_taxonomy['term_taxonomy_id'], $old_taxonomy ) );
							$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_category' WHERE taxonomy = '%s'", $old_taxonomy ) );
							//add action for update children of categories
							update_option( 'dr_cache_update', $new_taxonomy['term_taxonomy_id'] );
						}

					} else {
						//add tags
						$result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'listing_tag' WHERE taxonomy = '%s'", $old_taxonomy ) );
					}

					if ( !in_array( 'directory_listing', $old_taxonomy_args['object_type'] ) ) {
						$new_taxonomies[$old_taxonomy] = $old_taxonomy_args;
					}
				}
				//delete old directory taxonomies
				update_option( 'ct_custom_taxonomies', $new_taxonomies );
			}

			//rewrite options
			$old_options = get_option( 'dp_options' );
			if ( $old_options ) {
				if ( isset( $old_options['ads_settings']['header_ad_code'] ) && '' != $old_options['ads_settings']['header_ad_code'] ) {
					$params = array(
					'header_ad_code' => $old_options['ads_settings']['header_ad_code'],
					'key' => 'ads_settings'
					);
					$options = $this->get_options();
					$options = array_merge( $options, array( $params['key'] => $params ) );
					update_option( $this->options_name, $options );
				}
			}

			//rewrite payments options
			$old_payments = get_option( 'module_payments' );
			if ( $old_payments ) {
				if ( isset( $old_payments['settings'] ) ) {
					$old_settings = $old_payments['settings'];
					$new_payments = $this->get_options( 'payment_settings' );

					$params = array(
					'recurring_cost'    => isset( $old_settings['recurring_cost'] ) ? sprintf( "%01.2f", $old_settings['recurring_cost'] ) : sprintf( "%01.2f", $new_payments['recurring_cost'] ),
					'recurring_name'    => isset( $old_settings['recurring_name'] ) ? $old_settings['recurring_name'] : $new_payments['recurring_name'],
					'billing_period'    => isset( $old_settings['billing_period'] ) ? $old_settings['billing_period'] : $new_payments['billing_period'],
					'billing_frequency' => isset( $old_settings['billing_frequency'] ) ? $old_settings['billing_frequency'] : $new_payments['billing_frequency'],
					'billing_agreement' => isset( $old_settings['billing_agreement'] ) ? $old_settings['billing_agreement'] : $new_payments['billing_agreement'],
					'one_time_cost'     => isset( $old_settings['one_time_cost'] ) ? sprintf( "%01.2f", $old_settings['one_time_cost'] ) : sprintf( "%01.2f", $new_payments['one_time_cost'] ),
					'one_time_name'     => isset( $old_settings['one_time_name'] ) ? $old_settings['one_time_name'] : $new_payments['one_time_name'],
					'tos_content'       => isset( $old_settings['tos_content'] ) ? $old_settings['tos_content'] : $new_payments['tos_content'],
					'key'               => 'payment_settings'
					);

					$options = $this->get_options();
					$options = array_merge( $options, array( $params['key'] => $params ) );
					update_option( $this->options_name, $options );
				}

				if ( isset( $old_payments['paypal'] ) ) {
					$old_paypal = $old_payments['paypal'];
					$new_paypal = $this->get_options( 'paypal' );

					$params = array(
					'api_url'       => isset( $old_paypal['api_url'] ) ? $old_paypal['api_url'] : $new_paypal['api_url'],
					'api_username'  => isset( $old_paypal['api_username'] ) ? $old_paypal['api_username'] : $new_paypal['api_username'],
					'api_password'  => isset( $old_paypal['api_password'] ) ? $old_paypal['api_password'] : $new_paypal['api_password'],
					'api_signature' => isset( $old_paypal['api_signature'] ) ? $old_paypal['api_signature'] : $new_paypal['api_signature'],
					'currency'      => isset( $old_paypal['currency'] ) ? $old_paypal['currency'] : $new_paypal['currency'],
					'key'           => 'paypal'
					);

					$options = $this->get_options();
					$options = array_merge( $options, array( $params['key'] => $params ) );
					update_option( $this->options_name, $options );
				}
			}

			//change role for old users
			if( function_exists( get_users ) ) {
				$old_users = get_users( array( 'role' => 'dp_member' ) );
			} else {
				$wp_user_search = new WP_User_Search( "", "", 'dp_member' );
				$old_users = $wp_user_search->get_results();
			}

			if ( 0 < count( $old_users ) ) {

				foreach ( $old_users as $old_user ) {
					$old_userdata = get_userdata( $old_user->ID );

					if ( isset( $old_userdata->module_payments ) &&
					( 0 < count( $old_userdata->module_payments['paypal']['transactions'] ) || '' != $old_userdata->module_payments['paypal']['profile_id'] )
					) {
						update_usermeta( $old_user->ID, 'wp_capabilities', array( 'directory_member_paid' => '1' ) );
					} else {
						update_usermeta( $old_user->ID, 'wp_capabilities', array( 'directory_member_not_paid' => '1' ) );
					}
				}
				$result = $wpdb->query( "UPDATE {$wpdb->usermeta} SET meta_key = 'dr_options' WHERE meta_key = 'module_payments'" );
			}

			//delete old options
			delete_option( 'dp_options' );
			delete_option( 'module_payments' );
			delete_option( 'ct_flush_rewrite_rules' );
			delete_site_option( 'dp_options' );
			delete_site_option( 'module_payments' );
			delete_site_option( 'ct_flush_rewrite_rules' );
			delete_site_option( 'allow_per_site_content_types' );

			wp_redirect( add_query_arg( array( 'post_type' => 'directory_listing' ), 'edit.php' ) );
			exit;
		}

	}


	/**
	* Fire on plugin activation.
	*
	* @return void
	*/
	function plugin_activate() {

		if ( wp_next_scheduled( 'check_expiration_dates' ) ) {
			wp_clear_scheduled_hook( 'check_expiration_dates' );
		}

		$this->init();
		flush_rewrite_rules( false );
	}

	/**
	* Fire on plugin deactivation.
	* If $this->flush_plugin_data is set to "true"
	* all plugin data will be deleted
	*
	* @return void
	*/
	function plugin_deactivate() {
		// if true all plugin data will be deleted
		//TODO: do it when unistall plugin.
		if ( false ) {
			delete_option( $this->options_name );
			delete_site_option( $this->options_name );
		}
	}

	/**
	* Loads "{$text_domain}-[xx_XX].mo" language file from the "languages" directory
	*
	* @return void
	*/
	function load_plugin_textdomain() {
		load_plugin_textdomain( $this->text_domain, null, plugin_basename( $this->plugin_dir . 'languages' ) );
	}

	/**
	* Redirect templates using $wp_query.
	*/
	function template_redirect() {
		global $wp_query;

		if(is_page($this->signin_page_id)){
			if ( !is_user_logged_in() ) {
				if ( $_POST['signin_submit'] ) {
					$args = array( 'remember'   => ( $_POST['user_rem'] ) ? true : false,
					'user_login'     => $_POST['user_login'],
					'user_password'  => $_POST['user_pass']
					);

					$signin = wp_signon( $args, false );

					if ( is_wp_error( $signin ) ) {
						set_query_var( 'signin_error', $signin->get_error_message() );
					} else {
						$options = $this->get_options( 'general_settings' );

						if(! empty($_GET['redirect_to'])){
							wp_redirect($_GET['redirect_to']);
							exit;
						}

						if ( empty($options['signin_url']) )
						wp_redirect( home_url() );
						else
						wp_redirect( $options['signin_url'] );

						exit;
					}
				}
			}
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
	* Check if user can edit listing
	*/
	function user_can_edit_listing( $post_id ) {
		if ( 0 < $post_id ) {
			$post = get_post( $post_id, ARRAY_A );
			if ( current_user_can( 'edit_published_listings' ) && get_current_user_id() == $post['post_author']  )
			return true;
		}
		return false;
	}

	/**
	* Update or insert listing if no ID is passed.
	*
	* @param array $params Array of $_POST data
	* @param array|NULL $file Array of $_FILES data
	* @return int $post_id
	**/
	function update_listing( $params ) {

		/* Construct args for the new post */
		$args = array(
		/* If empty ID insert Listing instead of updating it */
		'ID'             => ( isset( $params['listing_data']['ID'] ) ) ? $params['listing_data']['ID'] : '',
		'post_title'     => wp_strip_all_tags($params['listing_data']['post_title']),
		'post_content'   => $params['listing_data']['post_content'],
		'post_excerpt'   => (isset($params['listing_data']['post_excerpt'])) ? $params['listing_data']['post_excerpt'] : '',
		'post_status'    => $params['listing_data']['post_status'],
		'post_author'    => get_current_user_id(),
		'post_type'      => 'directory_listing',
		'ping_status'    => 'closed',
		//'comment_status' => 'closed'
		);

		/* Insert page and get the ID */
		if (empty($args['ID']))
		$post_id = wp_insert_post( $args );
		else
		$post_id = wp_update_post( $args );

		if (! empty( $post_id ) ) {

			//Save custom tags
			if(is_array($params['tag_input'])){
				foreach($params['tag_input'] as $key => $tags){
					wp_set_post_terms($post_id, $params['tag_input'][$key], $key);
				}
			}
			//Save categories
			if(is_array($params['post_category'])){
				wp_set_post_terms($post_id, $params['post_category'], 'category');
			}

			//Save custom terms
			if(is_array($params['tax_input'])){
				foreach($params['tax_input'] as $key => $term_ids){
					if ( is_array( $params['tax_input'][$key] ) ) {
						wp_set_post_terms($post_id, $params['tax_input'][$key], $key);
					}
				}
			}

			if ( class_exists( 'CustomPress_Content_Types' ) ) {
				global $CustomPress_Content_Types;
				$CustomPress_Content_Types->save_custom_fields( $post_id );
			}

			return $post_id;
		}
	}

	/**
	* Handle $_REQUEST for main pages.
	*
	* @uses set_query_var() For passing variables to pages
	* @return void|die() if "_wpnonce" is not verified
	**/
	function handle_page_requests() {
		global $wp_query;


		/* Handles request for update-listing page */
		if(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id)){
			//		if ( ( 'add-listing' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'add-listing' == $wp_query->query_vars['name'] ) )
			//		|| ( 'edit-listing' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'edit-listing' == $wp_query->query_vars['name'] ) ) ) {

			if ( isset( $_POST['update_listing'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'verify' ) ) {
				//Update listing
				$this->update_listing( $_POST );

				if(is_page($this->add_listing_page_id)){
					//if ( 'add-listing' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'add-listing' == $wp_query->query_vars['name'] ) )
					wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'New Listing is added!', '' ) ) ), get_permalink($this->my_listings_page_id) ) );
				} else {
					wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is updated!', '' ) ) ), get_permalink($this->my_listings_page_id) ) );
				}
				exit;

			} elseif ( isset( $_POST['cancel_listing'] ) ) {
				wp_redirect( get_permalink($this->my_listings_page_id) );
				exit;
			}
		}

		/* Handles request for my-listings page */
		if(is_page($this->my_listings_page_id)){
			//		if ( 'my-listings' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'my-listings' == $wp_query->query_vars['name'] ) ) {
			if ( isset( $_POST['action'] ) && 'delete_listing' ==  $_POST['action'] && wp_verify_nonce( $_POST['_wpnonce'], 'action_verify' ) ) {
				if ( $this->user_can_edit_listing( $_POST['post_id'] ) ) {
					wp_delete_post( $_POST['post_id'] );
					wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is deleted!', '' ) ) ), get_permalink($this->my_listings_page_id) ) );
					exit;
				}
			}
		}
	}


	//scans post type at template_redirect to apply custom themeing to listings
	function load_directory_templates() {
		global $wp_query;

		// Redirect template loading to archive-listing.php rather than to archive.php
		if ( is_dr_page( 'tag' ) || is_dr_page( 'category' ) ) {
			$wp_query->set( 'post_type', 'directory_listing' );
		}

		$templates = array();

		//load proper theme for home listing page
		if ( $wp_query->is_home ) {

			$templates[] = 'home-listing.php';

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			}
		}

		//load proper theme for archive listings page
		elseif ( is_dr_page( 'archive' ) ) {

			//defaults
			$templates[] = 'dr_listings.php';
			$templates[] = 'archive-listings.php';

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
				$wp_query->is_page = 1;
				$wp_query->is_404 = null;
				$wp_query->post_count = 1;

				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 , 2 );
				$this->dr_first_thumbnail = true;
				add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 11 );
				add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 11 );
			}

			$this->is_directory_page = true;
		}

		//load proper theme for single listing page display
		elseif ( $wp_query->is_single &&  'directory_listing' == $wp_query->query_vars['post_type'] ) {

			//check for custom theme templates
			$listing_name = get_query_var( 'directory_listing' );
			$listing_id   = (int) $wp_query->get_queried_object_id();

			$templates = array();
			if ( $listing_name ) $templates[] = "single-listing-$listing_name.php";

			if ( $listing_id ) $templates[] = "single-listing-$listing_id.php";

			$templates[] = 'single-listing.php';

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own theme
				$wp_query->is_single    = 0;
				$wp_query->is_page      = 1;

				//add_filter( 'the_title', array( &$this, 'delete_post_title' ), 11 ); //after wpautop
				add_filter( 'the_content', array( &$this, 'listing_content' ), 99 ); //after wpautop
			}

			$this->is_directory_page = true;
		}


		//load proper theme for listing category or tag
		elseif ( 'listing_category' == $wp_query->query_vars['taxonomy'] || 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {
			if ( 'listing_category' == $wp_query->query_vars['taxonomy'] ) {

				$cat_name = get_query_var( 'listing_category' );
				$cat_id = absint( $wp_query->get_queried_object_id() );

				if ( $cat_name )
				$templates[] = "dr_category-$cat_name.php";
				if ( $cat_id )
				$templates[] = "dr_category-$cat_id.php";

				$templates[] = 'dr_category.php';

			} elseif ( 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {

				$tag_name = get_query_var( 'listing_tag' );
				$tag_id = absint( $wp_query->get_queried_object_id() );

				if ( $tag_name )
				$templates[] = "dr_tag-$tag_name.php";
				if ( $tag_id )
				$templates[] = "dr_tag-$tag_id.php";

				$templates[] = "dr_tag.php";

			}

			//defaults
			$templates[] = "dr_taxonomy.php";
			$templates[] = "dr_listinglist.php";

			//override if id is passed, mainly for product category dropdown widget
			if ( is_numeric( get_query_var($wp_query->query_vars['taxonomy']) ) ) {
				$term = get_term( get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy'] );
				$wp_query->query_vars[$wp_query->query_vars['taxonomy']] = $term->slug;
				$wp_query->query_vars['term'] = $term->slug;
			}

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
				$wp_query->is_page = 1;
				$wp_query->is_404 = null;
				$wp_query->post_count = 1;

				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 , 2 );
				$this->dr_first_thumbnail = true;
				add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 99 );
				add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
			}

			$this->is_directory_page = true;
		}
		//load proper theme for my-listing listing page display
		elseif(is_page($this->my_listings_page_id)){

			if ( !current_user_can( 'edit_published_listings' ) ) {
				wp_redirect( get_permalink($this->directory_page_id));
				exit;
			}

			$templates = array( 'page-my-listings.php' );

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own theme
				$wp_query->is_404 = false;
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 );
				add_filter( 'the_content', array( &$this, 'my_listings_content' ), 99 );
			}
			$this->is_directory_page = true;
		}

		//load proper theme for single listing page display

		elseif(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id)){

			if ( !current_user_can( 'edit_published_listings' ) ) {
				wp_redirect( get_permalink($this->directory_page_id));
				exit;
			}

			$templates = array( 'page-update-listing.php' );

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own theme
				$wp_query->is_404 = false;
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 99 );
				add_filter( 'the_content', array( &$this, 'update_listing_content' ), 99 );
			}
			$this->is_directory_page = true;

		}

		//load proper theme for signin listing page
		elseif(is_page($this->signin_page_id)){

			$templates = array( 'page-signin.php' );

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//fix for Infinite SEO (output buffering)
				$GLOBALS['post']->post_excerpt = 'signin';

				//otherwise load the page template and use our own theme
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
				add_filter( 'the_content', array( &$this, 'signin_content' ), 99 );
			}
			$this->is_directory_page = true;
		}

		//load proper theme for signup listing page
		elseif(is_page($this->signup_page_id)){

			$templates = array( 'page-signup.php' );

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//fix for Infinite SEO (output buffering)
				$GLOBALS['post']->post_excerpt = 'signup';

				//otherwise load the page template and use our own theme
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
				add_filter( 'the_content', array( &$this, 'signup_content' ), 99 );
			}
			$this->is_directory_page = true;
		}


		//load  specific items
		if ( $this->is_directory_page ) {
			add_filter( 'edit_post_link', array( &$this, 'delete_edit_post_link' ), 99 );

			//prevents 404 for virtual pages
			status_header( 200 );
		}
	}


	//filter the custom single listing template
	function custom_directory_template( $template ) {
		return $this->directory_template;
	}


	//content for single listing
	function listing_content( $content ) {
		global $post, $current_user;
		//don't filter outside of the loop
		if ( !in_the_loop() )
		return $content;

		ob_start();
		include_once($this->plugin_dir . '/ui-front/general/single-listing.php');
		$new_content = ob_get_contents();
		ob_end_clean();
		return $new_content;
	}


	//content for my-listings page
	function my_listings_content( $content ) {
		ob_start();
		include( $this->plugin_dir . 'ui-front/general/page-my-listings.php' );
		$new_content = ob_get_contents();
		ob_end_clean();
		return $new_content;
	}


	//content for add/edit listing page
	function update_listing_content( $content ) {
		ob_start();
		include( $this->plugin_dir . 'ui-front/general/page-update-listing.php' );
		$new_content = ob_get_contents();
		ob_end_clean();
		return $new_content;
	}

	//content for signup page
	function signup_content( $content ) {
		ob_start();
		include( $this->plugin_dir . 'ui-front/general/page-signup.php' );
		$new_content = ob_get_contents();
		ob_end_clean();
		return $new_content;
	}

	//content for singin page
	function signin_content( $content ) {
		ob_start();
		include( $this->plugin_dir . 'ui-front/general/page-signin.php' );
		$new_content = ob_get_contents();
		ob_end_clean();
		return $new_content;
	}

	//content for listings list
	function listing_list_theme( $content ) {
		//don't filter outside of the loop
		if ( !in_the_loop() )
		return $content;

		ob_start();
		include( $this->plugin_dir . 'ui-front/general/page-listings.php' );
		$new_content = ob_get_contents();
		ob_end_clean();

		return $new_content;
	}

	//replaces wp_trim_excerpt in our custom loops
	function listing_excerpt( $excerpt, $content, $post_id ) {

		$excerpt_more = apply_filters('wp_trim_excerpt', ' <a class="dr_listing_more_link" href="' . get_permalink( $post_id ) . '">' .  __( 'More Info &raquo;', $this->text_domain ) . "</a>\n");
		if ( $excerpt ) {
			return $excerpt . $excerpt_more;
		} else {
			$text = strip_shortcodes( $content );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
		}
		return $text;
	}

	//delete first image for lising on category/tag page (fixed duplication)
	function delete_first_thumbnail( $html ) {
		if ( $this->dr_first_thumbnail ) {
			$this->dr_first_thumbnail = false;
			return '';
		} else {
			return $html;
		}
	}

	//filters the titles for our custom pages
	function delete_post_title( $title, $id = false ) {
		global $wp_query;
		if ( $title == $wp_query->post->post_title ) {
			return '';
		}
		return $title;
	}

	//close comments
	function close_comments( $open ) {
		return false;
	}

	//filters the comments close text
	function comments_closed_text( $text ) {
		return '';
	}

	//filters the edit post button
	function delete_edit_post_link( $text ) {
		return '';
	}

	//filters the titles for our custom pages
	function page_title_output( $title, $id = false ) {
		global $wp_query;

		//filter out nav titles
		if ( !empty( $title ) && $id === false )
		return $title;

		//taxonomy pages
		if (  ( 'listing_category' == $wp_query->query_vars['taxonomy']  || 'listing_tag' == $wp_query->query_vars['taxonomy'] ) && $wp_query->post->ID == $id ) {
			if ( 'listing_category' == $wp_query->query_vars['taxonomy'] ) {
				$term = get_term_by( 'slug', get_query_var( 'listing_category' ), 'listing_category' );
				return sprintf( __( 'Listing Category: %s', $this->text_domain ), $term->name );
			} elseif ( 'listing_tag' == $wp_query->query_vars['taxonomy'] ) {
				$term = get_term_by( 'slug', get_query_var( 'listing_tag' ), 'listing_tag' );
				return sprintf( __( 'Listing Tag: %s', $this->text_domain ), $term->name );
			}
		}

		//title for listings page
		if ( is_dr_page( 'archive' ) && $wp_query->post->ID == $id )
		return __( 'Listings', $this->text_domain );

		/*

		if(is_page($this->my_listings_page_id))
		//if ( 'my-listings' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'my-listings' == $wp_query->query_vars['name'] ) )
		return __( 'My Listings', $this->text_domain );

		if(is_page($this->add_listings_page_id))
		//if ( 'add-listing' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'add-listing' == $wp_query->query_vars['name'] ) )
		return __( 'Add Listing', $this->text_domain );
		if(is_page($this->edit_listings_page_id))
		//if ( 'edit-listing' == $wp_query->query_vars['pagename'] || ( '' == $wp_query->query_vars['pagename'] && 'edit-listing' == $wp_query->query_vars['name'] ) )
		return __( 'Edit Listing', $this->text_domain );
		*/
		return $title;
	}


	//display custom fields
	function display_custom_fields_values( ) {
		global $wp_query;

		include( $this->plugin_dir . 'ui-front/general/display-custom-fields-values.php' );

	}

	/**
	* Shortcode definitions
	*/

	function list_categories_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'style' => '', //list, grid
		), $atts ) );

		if($style == 'grid') $result = '<div class="dr_list_grid">' .PHP_EOL;
		elseif($style == 'list') $result = '<div class="dr_list">' .PHP_EOL;
		else $result = "<div>\n";

		$result .= the_dr_categories_home( false, $atts );

		$result .= "</div><!--.dr_list-->\n";
		return $result;
	}

	function listings_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Listings', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button listings_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->directory_page_id); ?>';" ><?php echo $content; ?></button>
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
		<?php
	}

	function add_listing_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Add Listing', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button add_listing_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->add_listing_page_id); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function my_listings_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('My Listings', $this->text_domain),
		'view' => 'loggedin', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button my_listing_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->my_listings_page_id); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function profile_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Go to Profile', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button profile_btn" type="button" onclick="window.location.href='<?php echo admin_url() . 'profile.php'; ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function signup_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Signup', $this->text_domain),
		'view' => 'loggedout', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button signup_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->signup_page_id); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function signin_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Signin', $this->text_domain),
		'redirect' => '',
		'view' => 'loggedout', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$options = get_option( $this->options_name );
		if(empty($redirect)) $redirect = ( empty($options['general_settings']['signin_url'])) ? home_url() : $options['general_settings']['signin_url'];

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button signin_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->signin_page_id) . '?redirect_to=' . urlencode($redirect); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function logout_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Logout', $this->text_domain),
		'redirect' => '',
		'view' => 'loggedin', //loggedin, loggedout, both
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$options = get_option( $this->options_name );
		if(empty($redirect)) $redirect = ( empty($options['general_settings']['logout_url'])) ? home_url() : $options['general_settings']['logout_url'];

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button logout_btn" type="button" onclick="window.location.href='<?php echo wp_logout_url($redirect); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function get_post_image_link($post_id = 0){

		if ( ! ( post_type_supports( 'directory_listing', 'thumbnail' )
		&& current_theme_supports( 'post-thumbnails', 'directory_listing' ) ) )
		return '';

		ob_start();
		?>
		<div id="postimagediv">
			<div class="inside">
				<?php

				$html = '<p><a class="thickbox" href="' . admin_url() . "/media-upload.php?post_id={$post_id}&type=image&TB_iframe=1&width=640&height=510" . '" id="set-post-thumbnail" >' . esc_html__( 'Set Featured Image') . '</a></p>';

				if(has_post_thumbnail($post_id)){
					$html = get_the_post_thumbnail($post_id, array(226,100));
					$ajax_nonce = wp_create_nonce( "set_post_thumbnail-{$post_id}" );
					$html .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
				}
				echo $html;

				?>
			</div>
		</div>
		<?php
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	* Add directory_listing to the post_types supporting thumbnails. CustomPress' post_type support thumbnail must also be on.
	*
	*/
	function thumbnail_support(){

		$supported_types = get_theme_support( 'post-thumbnails' );

		if( $supported_types === false ) {
			add_theme_support( 'post-thumbnails', array( 'directory_listing' ) );
		}
		elseif( is_array( $supported_types ) )
		{
			$supported_types[0][] = 'directory_listing';
			add_theme_support( 'post-thumbnails', $supported_types[0] );
		}
	}

}

/* Initiate Class */

if (!is_admin())
$directory_core = new Directory_Core();


endif;
?>