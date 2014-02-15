<?php

/**
* Directory_Core
*
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub) {@link http://premium.wpmudev.org}
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

//** Directory_Core Class
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

	public $capability_map = null;
	public $is_directory_page = false;
	public $directory_template;
	public $dr_first_thumbnail;
	public $post_type = 'directory_listing';

	/** @var boolean True if BuddyPress is active. */
	public $bp_active;
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

	/** @public string/int Current maximum range of page links to show in pagination pagination (used in query)*/
	public $pagination_range = 4;
	/** @public string/bool Whether to display pagination at the top of the page*/
	public $pagination_top;
	/** @public string/bool Whether to display pagination at the bottom of the page*/
	public $pagination_bottom;

	public $use_credits = false;
	public $use_paypal = false;
	public $use_authorizenet = false;

	public $use_free = false;
	public $use_recurring = false;
	public $use_one_time = false;

	var $transactions = null;


	/**
	* Constructor.
	*/
	function __construct(){

		//Default capability map for Listings
		$this->capability_map = array(
		'read_listings'             => __( 'View listings.', $this->text_domain ),
		'read_private_listings'     => __( 'View private listings.', $this->text_domain ),

		'publish_listings'          => __( 'Add listings.', $this->text_domain ),

		'edit_listings'             => __( 'Edit listings.', $this->text_domain ),
		'edit_published_listings'   => __( 'Edit published listings.', $this->text_domain ),
		'edit_private_listings'     => __( 'Edit private listings.', $this->text_domain ),

		'delete_listings'           => __( 'Delete listings', $this->text_domain ),
		'delete_published_listings' => __( 'Delete published listings.', $this->text_domain ),
		'delete_private_listings'   => __( 'Delete private listings.', $this->text_domain ),

		'edit_others_listings'      => __( 'Edit others\' listings.', $this->text_domain ),
		'delete_others_listings'    => __( 'Delete others\' listings.', $this->text_domain ),

		'upload_files'              => __( 'Upload files.', $this->text_domain ),
		);

		add_action( 'plugins_loaded', array( &$this, 'on_plugins_loaded' ), 8);
		add_action('after_setup_theme', array(&$this, 'thumbnail_support') );
		add_action( 'init', array( &$this, 'init' ) );

		/* Create neccessary pages */
		add_action( 'wp_loaded', array( &$this, 'create_default_pages' ) );
		add_action( 'parse_request', array( &$this, 'on_parse_request' ) );
		add_action('pre_get_posts', array(&$this, 'on_pre_get_posts') );

		add_action( 'template_redirect', array( &$this, 'handle_contact_form_requests' ) );

		add_action('wp_logout', array( &$this, 'on_wp_logout' ) );

		add_filter( 'parse_query', array( &$this, 'on_parse_query' ) );
		add_filter( 'map_meta_cap', array( &$this, 'map_meta_cap' ), 11, 4 );
		add_filter( 'comment_form_defaults', array($this,'review_defaults'));
		add_filter( 'user_contactmethods', array( &$this, 'contact_fields' ), 10, 2 );
		add_filter( 'excerpt_more', array(&$this, 'on_excerpt_more'));
		add_filter( 'author_link', array(&$this, 'on_author_link'));
		add_filter( 'login_redirect', array(&$this, 'on_login_redirect'), 10, 3);
		add_filter('admin_post_thumbnail_html', array(&$this,'on_admin_post_thumbnail_html') );

		//Shortcodes

		add_shortcode( 'dr_list_categories', array( &$this, 'list_categories_sc' ) );
		add_shortcode( 'dr_listings_btn', array( &$this, 'listings_btn_sc' ) );
		add_shortcode( 'dr_add_listing_btn', array( &$this, 'add_listing_btn_sc' ) );
		add_shortcode( 'dr_edit_listing_btn', array( &$this, 'edit_listing_btn_sc' ) );
		add_shortcode( 'dr_my_listings_btn', array( &$this, 'my_listings_btn_sc' ) );
		add_shortcode( 'dr_my_credits_btn', array( &$this, 'my_credits_btn_sc' ) );
		add_shortcode( 'dr_profile_btn', array( &$this, 'profile_btn_sc' ) );
		add_shortcode( 'dr_logout_btn', array( &$this, 'logout_btn_sc' ) );
		add_shortcode( 'dr_signin_btn', array( &$this, 'signin_btn_sc' ) );
		add_shortcode( 'dr_signup_btn', array( &$this, 'signup_btn_sc' ) );
		add_shortcode( 'dr_custom_fields', array( &$this, 'custom_fields_sc' ) );

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
		$defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . __( 'Review', $this->text_domain ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		$defaults['cancel_reply_link'] = __( 'Cancel review', $this->text_domain );

		return $defaults;
	}

	/**
	* Contact fields to add to the User profile to track credit card info
	* @param array $contact_fields
	* @param object $user_id
	* @return array
	*/
	function contact_fields($contact_fields = array(), $user = null){

		$cc_contact = array(
		'cc_email'        => __('CC Email', $this->text_domain),
		'cc_firstname'    => __('CC First Name', $this->text_domain),
		'cc_lastname'     => __('CC Last Name', $this->text_domain),
		'cc_street'       => __('CC Street', $this->text_domain),
		'cc_city'         => __('CC City', $this->text_domain),
		'cc_state'        => __('CC State', $this->text_domain),
		'cc_zip'          => __('CC Zip', $this->text_domain),
		'cc_country_code' => __('CC Country Code', $this->text_domain),
		);

		return array_merge($cc_contact, $contact_fields );

	}

	/**
	* Map meta capabilities
	*
	* Learn more:
	* @link http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
	* @link http://wordpress.stackexchange.com/questions/1684/what-is-the-use-of-map-meta-cap-filter/2586#2586
	*
	* @param <type> $caps
	* @param <type> $cap
	* @param <type> $user_id
	* @param <type> $args
	* @return array
	**/
	function map_meta_cap( $caps, $cap, $user_id, $args ) {

		/* If editing, deleting, or reading a listing, get the post and post type object. */
		if ( 'edit_listing' == $cap || 'delete_listing' == $cap || 'read_listing' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			/* Set an empty array for the caps. */
			$caps = array();
		}

		/* If editing a listing, assign the required capability. */
		if ( 'edit_listing' == $cap ) {
			if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->edit_posts;
			else
			$caps[] = $post_type->cap->edit_others_posts;
		}

		/* If deleting a listing, assign the required capability. */
		elseif ( 'delete_listing' == $cap ) {
			if ( $user_id == $post->post_author )
			$caps[] = $post_type->cap->delete_posts;
			else
			$caps[] = $post_type->cap->delete_others_posts;
		}

		/* If reading a private listing, assign the required capability. */
		elseif ( 'read_listing' == $cap ) {

			if ( 'private' != $post->post_status )
			$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
			$caps[] = 'read';
			else
			$caps[] = $post_type->cap->read_private_posts;
		}

		/* Return the capabilities required by the user. */
		return $caps;
	}

	/**
	* Recurring or One time active.
	*
	* @return void
	*/
	function is_full_access(){

		$result = false;

		if(current_user_can('manage_options') || $this->use_free) $result = true;

		//for paid users
		if ( $this->transactions->billing_type ) {
			if ( 'one_time' == $this->transactions->billing_type && 'success' == $this->transactions->status ) {
				$result = true;
			} elseif ( 'recurring' == $this->transactions->billing_type && 'success' == $this->transactions->status) {
				if(time() < $this->transactions->expires ) {
					$result = true;
				} else {
					$this->transactions->status = 'expired';
				}
			}
		}

		return apply_filters('directory_full_access', $result);
	}

	/**
	* Redirect signin to home or user defined url
	*
	*/
	function on_login_redirect($redirect = '', $request = '', $user = '') {

		$options = $this->get_options('general');
		$signin_url  = trim($options['signin_url']);
		$redirect = empty( $signin_url )? $redirect : $signin_url;

		return $redirect;
	}


	/**
	* Redirect signout to home or user defined url
	*
	*/
	function on_wp_logout(){
		$options = $this->get_options('general');
		$url = trim($options['logout_url']);
		if( ! empty($url) ) {
		wp_redirect($url);
		exit;
		}
	}

	function on_parse_query($query){

		if ( isset( $query ) ) {


			//Or are we editing a listing?
			if(! @is_post_type_archive('directory_listing') && @is_page($this->directory_page_id) ){

				wp_redirect( get_post_type_archive_link('directory_listing') );
				exit;
			}

			//Handle any security redirects
			if (! is_user_logged_in()) {
				if( @is_page($this->add_listing_page_id)
				|| @is_page($this->edit_listing_page_id)
				|| @is_page($this->my_listings_page_id)
				|| @is_page($this->signup_page_id) ) {

					$args = array('redirect_to' => urlencode(get_permalink($query->queried_object_id)));
					if(!empty($_REQUEST['register'])) $args['register'] = $_REQUEST['register'];
					if(!empty($_REQUEST['reset'])) $args['reset'] = $_REQUEST['reset'];

					$redirect = add_query_arg($args, get_permalink($this->signin_page_id) );
					wp_redirect( $redirect );
					exit;
				}
			}

			//Are we adding a Listing?
			if (@is_page($this->add_listing_page_id) && (! $this->use_free) ) {
				if(! (current_user_can('create_listings') && current_user_can('publish_listings') ) ) {
					wp_redirect( get_permalink($this->signup_page_id) );
					exit;
				}
			}

			//Or are we editing a listing?
			//Can the user edit listings?
			if ( ! empty($_REQUEST['post_id']) && ! current_user_can('edit_listing', $_REQUEST['post_id'] ) ) {
				if(@is_page($this->edit_listing_page_id)){
					wp_redirect( get_permalink($this->my_listings_page_id) );
					exit;
				}
			}
		}
		return $query;
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

			if ( empty($options['general']['logout_url']) )
			wp_redirect( home_url() );
			else
			wp_redirect( $options['general']['logout_url'] );

			exit();
		}
		return $wp;
	}

	function on_pre_get_posts($wp_query_obj) {
		global $current_user;

		if( $current_user->ID == 0 ) return;

		if(! empty($_POST['action']) && $_POST['action'] == 'query-attachments'
		&& !current_user_can('administrator')
		&& !current_user_can('edit_others_listings') )
		$wp_query_obj->set('author', $current_user->ID );
	}

	/**
	* Intiate plugin.
	*
	* @return void
	*/
	function init() {
		global $blog_id;

		$directory_obj = get_post_type_object('directory_listing');

		if( ! empty( $directory_obj ) ) {

			if( !is_string($slug = $directory_obj->has_archive) ){
				$slug = $listings;
			}

			add_rewrite_rule("author/([^/]+)/{$slug}/page/?([2-9][0-9]*)",
			"index.php?post_type=directory_listing&author_name=\$matches[1]&paged=\$matches[2]", 'top');

			add_rewrite_rule("author/([^/]+)/{$slug}",
			"index.php?post_type=directory_listing&author_name=\$matches[1]", 'top');
		}

		// post_status "virtual" for pages not to be displayed in the menus but that users should not be editing.
		register_post_status( 'virtual', array(
		'label' => __( 'Virtual', $this->text_domain ),
		'public' => (! is_admin()), //This trick prevents the virtual pages from appearing in the All Pages list but can be display on the front end.
		'exclude_from_search' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>' ),
		) );

		/* Set current user */
		$this->current_user = wp_get_current_user();

		$this->transactions = new DR_Transactions;

		/* Set current user credits */
		$this->user_credits = $this->transactions->credits;

		// Get pagination settings
		$options = $this->get_options('general');

		$this->pagination_range = (isset($options['pagination_range'])) ? intval($options['pagination_range']) : 4;
		$this->pagination_top = ( ! empty($options['pagination_top']));
		$this->pagination_bottom = ( ! empty($options['pagination_bottom']));

		//How do we sell stuff
		$options = $this->get_options('payment_types');

		$this->use_free = ( ! empty($options['use_free']));
		if (! $this->use_free) { //Can't use gateways if it's free.

			$this->use_paypal = ( ! empty($options['use_paypal']));
			if ($this->use_paypal){ //make sure the api fields have something in them
				$this->use_paypal = (! empty($options['paypal']['api_username'])) && (! empty($options['paypal']['api_password'])) && (! empty($options['paypal']['api_signature']));
			}

			$this->use_authorizenet = (! empty($options['use_authorizenet']));
			if ($this->use_authorizenet){ //make sure the api fields have something in them
				$this->use_authorizenet = (! empty($options['authorizenet']['api_user']) ) && (! empty($options['authorizenet']['api_key']));
			}

			$options = $this->get_options('payments');
			$this->use_credits = ( ! empty($options['enable_credits']));
			$this->use_recurring = ( ! empty($options['enable_recurring']));
			$this->use_one_time = ( ! empty($options['enable_one_time']));
		}

		//Set a user capability based on users purchases
		if(! is_multisite() || is_user_member_of_blog(get_current_user_id(), $blog_id) ) {
			if($this->use_free
			|| ($this->use_credits && $this->user_credits >= $options['credits_per_listing'])
			|| $this->is_full_access() ){
				$this->current_user->add_cap('create_listings');
			} else {
				$this->current_user->remove_cap('create_listings');
			}
		}
	}

	/**
	* Get page by meta value
	*
	* @return int $page[0] /bool false
	*/
	function get_page_by_meta( $value ) {
		$post_statuses = get_post_stati();
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
	* Create the default Directory pages.
	*
	* @return void
	**/
	function create_default_pages() {

		/* Create neccessary pages */
		$post_content = __('Virtual page. Editing this page won\'t change anything.', $this->text_domain);

		$directory_page = $this->get_page_by_meta( 'listings' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;
		$current_user = wp_get_current_user();

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Listings',
			'post_status'    => 'publish',
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", "listings" );
		}

		$this->directory_page_id = $page_id; //Remember the number
		$this->directory_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'my_listings' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'My Listings',
			'post_status'    => 'publish',
			'post_parent'    => $this->directory_page_id,
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", "my_listings" );
		}

		$this->my_listings_page_id = $page_id; //Remember the number
		$this->my_listings_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'add_listing' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Add Listing',
			'post_status'    => 'virtual',
			'post_parent'    => $this->directory_page_id,
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", "add_listing" );
		} else {
			if(! in_array($directory_page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}

		$this->add_listing_page_id = $page_id; //Remember the number
		$this->add_listing_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'edit_listing' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => 'Edit Listing',
			'post_status'    => 'virtual',
			'post_parent'    => $this->directory_page_id,
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", "edit_listing" );
		} else {
			if(! in_array($directory_page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}

		$this->edit_listing_page_id = $page_id; //Remember the number
		$this->edit_listing_page_slug = $directory_page->post_name; //Remember the slug

		$directory_page = $this->get_page_by_meta( 'my_listings_credits' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			$current_user = wp_get_current_user();
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'My Listings Credits',
			'post_name'     => 'my-credits',
			'post_status'    => 'virtual',
			//'post_author'    => $current_user->ID,
			'post_parent'    => $this->directory_page_id,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed',
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", 'my_listings_credits' );
		} else {
			if(! in_array($directory_page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}

		$this->my_credits_page_id = $page_id; // Remember the number
		$this->my_credits_page_slug = $directory_page->post_name; //Remember the slug


		$directory_page = $this->get_page_by_meta( 'signup' );
		$page_id = ($directory_page && $directory_page->ID > 0) ? $directory_page->ID : 0;

		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => 'Signup',
			'post_status'    => 'publish',
			'post_parent'    => $this->directory_page_id,
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
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
			'post_status'    => 'virtual',
			'post_parent'    => $this->directory_page_id,
			//'post_author'    => $current_user->ID,
			'post_type'      => 'page',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$directory_page = get_post($page_id);
			add_post_meta( $page_id, "directory_page", "signin" );
		} else {
			if(! in_array($directory_page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}

		$this->signin_page_id = $page_id; //Remember the number
		$this->signin_page_slug = $directory_page->post_name; //Remember the slug

	}

	/**
	* Create the default Directory member roles and capabilities.
	*
	* @return void
	*/
	function create_default_directory_roles() {

		//set capability for admin
		$admin = get_role('administrator');
		foreach ( array_keys( $this->capability_map ) as $capability )
		$admin->add_cap($capability );
	}

	/**
	* Loads "{$text_domain}-[xx_XX].mo" language file from the "languages" directory
	*
	* @return void
	*/
	function on_plugins_loaded() {
		load_plugin_textdomain( $this->text_domain, false, plugin_basename( $this->plugin_dir . 'languages' ) );

		//If the activate flag is set then try to initalize the defaults
		if( get_site_option('dr_activate', false))	{
			include_once($this->plugin_dir . 'core/data.php');
			new Directory_Core_Data();
			delete_site_option('dr_activate');
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
		//'post_author'    => get_current_user_id(),
		'post_type'      => 'directory_listing',
		'ping_status'    => 'closed',
		'comment_status' => 'open'
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

			if ( class_exists( 'CustomPress_Core' ) ) {
				global $CustomPress_Core;
				$CustomPress_Core->save_custom_fields( $post_id );
			}

			if ( isset($_FILES['feature_image']) && empty( $_FILES['feature_image']['error'] )) {
				/* Require WordPress utility functions for handling media uploads */
				require_once( ABSPATH . '/wp-admin/includes/media.php' );
				require_once( ABSPATH . '/wp-admin/includes/image.php' );
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				/* Upload the image ( handles creation of thumbnails etc. ), set featured image  */
				$thumbnail_id = media_handle_upload( 'feature_image', $post_id );
				set_post_thumbnail( $post_id, $thumbnail_id );
			}

			return $post_id;
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

	/**
	* My Directory Credits.
	*
	* @return void
	**/
	function my_credits_content($content = null){
		if(! in_the_loop()) return $content;
		ob_start();
		require($this->plugin_dir . 'ui-front/general/page-my-credits.php');
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

	function on_excerpt_more($more = ''){
		return ' <a href="' . get_permalink() .'">More Info &raquo;</a>';
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
	function close_comments( $open, $id = 0 ) {
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
		global $wp_query, $post;

		//filter out nav titles
		if ( !is_object($post) || ($post->ID != $id) )
		return $title;

		//taxonomy pages
		$tax_key = (empty($wp_query->query_vars['taxonomy']) ) ? '' : $wp_query->query_vars['taxonomy'];

		$taxonomies = get_object_taxonomies('directory_listing', 'objects');
		if ( array_key_exists($tax_key, $taxonomies ) ){
			$term = get_term_by( 'slug', get_query_var( $tax_key ), $tax_key );
			return $taxonomies[$tax_key]->labels->singular_name . ': ' . $term->name;
		}

		//title for listings page
		if ( is_post_type_archive('directory_listing' ) ){
			return post_type_archive_title();
		}
		return $title;
	}


	/**
	* Format date.
	*
	* @param int $date unix timestamp
	* @return string formatted date
	**/
	function format_date( $date ) {
		return date_i18n( get_option('date_format'), $date );
	}




	//display custom fields
	function display_custom_fields_values( ) {
		include( $this->plugin_dir . 'ui-front/general/display-custom-fields-values.php' );
	}

	/**
	* Shortcode definitions
	*/

	function list_categories_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'style' => '', //list, grid
		), $atts ) );

		if($style == 'grid') $result = '<div class="dr_list_grid">';
		elseif($style == 'list') $result = '<div class="dr_list">';
		else $result = "<div>";

		$result .= the_dr_categories_home( false, $atts );

		$result .= "</div><!--.dr_list-->";

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
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
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

	function edit_listing_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Edit Listing', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'post' => '0',
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button add_listing_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->edit_listing_page_id) . "?post_id=$post"; ?>';" ><?php echo $content; ?></button>
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

	function my_credits_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('My Listing Credits', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		), $atts ) );

		if(! $this->use_credits || (!$this->use_paypal && ! $this->use_authorizenet)) return ''; //No way to pay no button

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button credits_btn" type="button" onclick="window.location.href='<?php echo get_permalink($this->my_credits_page_id); ?>';" ><?php echo $content; ?></button>
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
		'view' => 'both', //loggedin, loggedout, both
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

		$options = $this->get_options( 'general');
		if(empty($redirect)) $redirect = ( empty($options['signin_url'])) ? home_url() : $options['signin_url'];

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

		$options = $this->get_options( 'general' );
		if(empty($redirect)) $redirect = ( empty($options['logout_url'])) ? home_url() : $options['logout_url'];

		$content = (empty($content)) ? $text : $content;
		ob_start();
		?>
		<button class="dr_button log-out logout_btn" type="button" onclick="window.location.href='<?php echo wp_logout_url($redirect); ?>';" ><?php echo $content; ?></button>
		<?php
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function custom_fields_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Logout', $this->text_domain),
		'redirect' => '',
		'view' => 'loggedin', //loggedin, loggedout, both
		), $atts ) );

		$options = get_option( $this->options_name );
		$content = (empty($content)) ? $text : $content;
		ob_start();
		$this->display_custom_fields_values();
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	*  on_admin_post_thumbnail_html adds a hidden required field if the feature image is empty
	*
	*/
	function on_admin_post_thumbnail_html($content = ''){

		if(get_post_type() != 'directory_listing') return $content;

		$options = $this->get_options('general');
		$required = empty($options['field_image_req']);

		if( !$required || (stripos($content, 'remove-post-thumbnail') !== false) ) return $content;

		$content = str_replace('<a', '<input type="text" style="visibility: hidden;width:0;" value="" class="required" /><a', $content);

		return $content;
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

	/**
	* return fancy pagination links.
	* @uses $wp_query
	*
	*/
	function pagination($show = true){
		if(! $show) return '';
		ob_start();

		include($this->plugin_dir . 'ui-front/general/pagination.php');

		$result = apply_filters( 'dr_pagination', ob_get_contents());
		ob_end_clean();

		return $result;
	}

	function no_title($content = ''){
		if(! in_the_loop()) return $content;
		return '';
	}

	function on_author_link($link=''){
		global $post;
		if($post->post_type == 'directory_listing'){
			$link = str_replace('/author/', '/dr-author/', $link );
		}
		return $link;
	}

	function email_replace( $content = '' ){
		global $post;

		$user_info  = get_userdata( $post->post_author );

		$result =
		str_replace('SITE_NAME', get_bloginfo('name'),
		str_replace('POST_TITLE', $post->post_title,
		str_replace('POST_LINK', make_clickable( get_permalink($post->ID) ),
		str_replace('TO_NAME', $user_info->nicename,
		str_replace('FROM_NAME', $_POST['name'],
		str_replace('FROM_EMAIL', $_POST['email'],
		str_replace('FROM_SUBJECT', $_POST['subject'],
		str_replace('FROM_MESSAGE', $_POST['message'],
		$content) ) ) ) ) ) ) );

		return $result;
	}

	/**
	* Handles the request for the contact form on the single{}.php template
	**/
	function handle_contact_form_requests() {

		if(! session_id() ) session_start();
		/* Only handle request if on single{}.php template and our post type */
		if ( get_post_type() == $this->post_type && is_single() ) {

			if (isset( $_POST['contact_form_send'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'send_message' ) ){
				$_POST = stripslashes_deep($_POST);
				if ( isset( $_POST['name'] ) && '' != $_POST['name']
				&& isset( $_POST['email'] ) && '' != $_POST['email']
				&& isset( $_POST['subject'] ) && '' != $_POST['subject']
				&& isset( $_POST['message'] ) && '' != $_POST['message']
				&& isset( $_POST['dr_random_value'] ) && (md5(strtoupper($_POST['dr_random_value']) )  == $_SESSION['dr_random_value'] )

				) {
					global $post;

					$user_info  = get_userdata( $post->post_author );

					$options = $this->get_options( 'general' );

					$body       = nl2br($this->email_replace( $options['email_content'] ) );

					$tm_subject =  $this->email_replace( $options['email_subject'] );

					$to         = $user_info->user_email;
					$subject    = $tm_subject;
					$message    = $body;
					$headers[]  = "MIME-Version: 1.0";
					$headers[]  = "From: " . $_POST['name'] .  " <{$_POST['email']}>";
					$headers[]  = "Content-Type: text/html; charset=\"" . get_option( 'blog_charset' ) . '"';

					if( $options['cc_admin'] == '1'){
						$headers[]  = "Cc: " . get_bloginfo('admin_email');
					}

					if( $options['cc_sender'] == '1'){
						$headers[]  = "Cc: " . $_POST['name'] .  " <{$_POST['email']}>";
					}

					$sent = (wp_mail( $to, $subject, $message, $headers ) ) ? '1' : '0';
					wp_redirect( get_permalink( $post->ID ) . '?sent=' . $sent );
					exit;
				}
			}
		}
	}

	/**
	* Redirect using JavaScript. Useful if headers are already sent.
	*
	* @param string $url The URL to which the function should redirect
	**/
	function js_redirect( $url, $silent = false ) {
		if(! $silent ):
		?>
		<p><?php _e( 'You are being redirected. Please wait.', $this->text_domain );  ?></p>
		<img src="<?php echo $this->plugin_url .'/ui-front/images/loader.gif'; ?>" alt="<?php _e( 'You are being redirected. Please wait.', $this->text_domain );  ?>" />
		<?php endif; ?>
		<script type="text/javascript">//<![CDATA[
			window.location = '<?php echo $url; ?>';	//]]>
		</script>
		<?php
	}


}

include_once DR_PLUGIN_DIR . 'core/class-dr-transactions.php';
include_once DR_PLUGIN_DIR . 'core/class-dr-meta.php';

endif;

// Set flag on activation to trigger initial data
add_action('activated_plugin', 'dr_flag_activation', 1);
function dr_flag_activation($plugin=''){
	//Flag we're activating
	if($plugin == 'directory/loader.php') add_site_option('dr_activate', true);
}

//Decide whether to load Admin BP or Standard version
add_action('plugins_loaded', 'dr_on_plugins_loaded');
function dr_on_plugins_loaded(){
	if(is_admin()){ 	//Are we admin
		include_once DR_PLUGIN_DIR . 'core/admin.php';
		//include_once DR_PLUGIN_DIR . 'core/tutorial.php';
		require_once DR_PLUGIN_DIR . 'core/contextual_help.php';
	}
	elseif(defined('BP_VERSION')){ //Are we BuddyPress
		include_once DR_PLUGIN_DIR . 'core/buddypress.php';
	}
	else {
		include_once DR_PLUGIN_DIR . 'core/main.php';
	}
}

