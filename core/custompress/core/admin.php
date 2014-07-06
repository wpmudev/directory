<?php

/**
* CustomPress_Core_Admin
*
* @uses CustomPress_Core
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub), Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

if (!class_exists('CustomPress_Core_Admin')):

class CustomPress_Core_Admin extends CustomPress_Content_Types {

	/** @public array Available Post Types */
	public $post_types;
	/** @public array Available Taxonomies */
	public $taxonomies;
	/** @public array Available Custom Fields */
	public $custom_fields;
	/** @public boolean Flag whether the users have the ability to declair post type for their own blogs */
	public $enable_subsite_content_types = true;

	function __construct(){

		parent::__construct();

		add_action( 'init', array( &$this, 'handle_post_type_requests' ), 0 );
		add_action( 'init', array( &$this, 'handle_taxonomy_requests' ), 0 );
		add_action( 'init', array( &$this, 'handle_custom_field_requests' ), 0 );

		add_action( 'add_meta_boxes', array( &$this, 'on_add_meta_boxes' ), 2 );

		add_action( 'admin_menu', array( &$this, 'on_admin_menu' ) );
		add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );

		add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_custom_field_scripts') );
		add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_custom_field_scripts') );

		add_action( 'wp_ajax_ct_get_caps', array( &$this, 'ajax_get_caps' ) );
		add_action( 'wp_ajax_ct_save', array( &$this, 'ajax_save' ) );

	}

	/**
	* Admin init
	*
	* @return void
	*/
	function admin_init() {

		//Add custom fields as Columns on edit Post Type page
		if ( isset( $_GET['post_type'] )){

			if ($this->display_network_content){
				if( isset($this->all_post_types[$_GET['post_type']]) && is_array( $this->all_post_types[$_GET['post_type'] ] ) ) {
					add_filter( 'manage_edit-' . $_GET['post_type'] . '_columns', array( &$this, 'add_new_cf_columns' ) );
					add_action( 'manage_' . $_GET['post_type'] . '_posts_custom_column', array( &$this, 'manage_cf_columns' ), 10, 2 );
				}
			} else {
				if( isset($this->post_types[$_GET['post_type']]) && is_array( $this->post_types[$_GET['post_type'] ] ) ) {
					add_filter( 'manage_edit-' . $_GET['post_type'] . '_columns', array( &$this, 'add_new_cf_columns' ) );
					add_action( 'manage_' . $_GET['post_type'] . '_posts_custom_column', array( &$this, 'manage_cf_columns' ), 10, 2 );
				}
			}
		}
	}

	/**
	* add new cf columns to columns list
	*
	* @return array columns
	*/
	function add_new_cf_columns( $columns ) {

		if( $this->display_network_content ) {

			if ( isset( $this->network_post_types[$_GET['post_type']]['cf_columns'] ) && is_array( $this->network_post_types[$_GET['post_type']]['cf_columns'] ) )
			foreach ( $this->network_post_types[$_GET['post_type']]['cf_columns'] as $key => $value ) {
				if ( 1 == $value )
				$columns[$key] = $this->network_custom_fields[$key]['field_title'];
			}
		} else {
			if ( isset( $this->post_types[$_GET['post_type']]['cf_columns'] ) && is_array( $this->post_types[$_GET['post_type']]['cf_columns'] ) )
			foreach ( $this->post_types[$_GET['post_type']]['cf_columns'] as $key => $value ) {
				if ( 1 == $value )
				$columns[$key] = $this->custom_fields[$key]['field_title'];
			}

		}
		return $columns;
	}

	/**
	* Get values for added cf columns
	*
	* @return void
	*/
	function manage_cf_columns( $column_name, $post_it ) {

		if ( $this->display_network_content ){
			if ( isset($this->all_custom_fields[$column_name])
			&& $column_name == $this->all_custom_fields[$column_name]['field_id']) {
				$custom_field = $this->all_custom_fields[$column_name];
			}
		} else {
			if ( isset($this->custom_fields[$column_name])
			&& $column_name == $this->custom_fields[$column_name]['field_id']) {
				$custom_field = $this->custom_fields[$column_name];
			}
			$prefix = ( empty($custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';
			echo get_post_meta( $post_it, $prefix . $column_name, true );
		}
	}

	/**
	* Register site admin menues.
	*
	* @access public
	* @return void
	*/
	function on_admin_menu() {

		if ( is_multisite() ) {
			$menu_slug = $this->enable_subsite_content_types ? 'ct_content_types' : 'cp_main';
			$menu_callback = $this->enable_subsite_content_types ? 'handle_content_types_page_requests' : 'handle_settings_page_requests';
		} else {
			$menu_slug = 'ct_content_types';
			$menu_callback = 'handle_content_types_page_requests';
		}

		add_menu_page( __('CustomPress', $this->text_domain), __('CustomPress', $this->text_domain), 'activate_plugins', $menu_slug, array( &$this, $menu_callback ) );

		if ( $this->enable_subsite_content_types || !is_multisite() ) {
			$page_content_types = add_submenu_page( 'ct_content_types' , __( 'Content Types', $this->text_domain ), __( 'Content Types', $this->text_domain ), 'activate_plugins', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );

			add_action( 'admin_print_scripts-' . $page_content_types, array( &$this, 'enqueue_scripts' ) );
		}

		$page_settings = add_submenu_page( $menu_slug, __('Settings', $this->text_domain), __('Settings', $this->text_domain), 'activate_plugins', 'cp_main', array( &$this, 'handle_settings_page_requests' ) );
		$export_settings = add_submenu_page( $menu_slug, __('Export / Import', $this->text_domain), __('Export / Import', $this->text_domain), 'manage_options', 'ct_export', array( &$this, 'handle_export' ) );

		add_action( 'admin_print_scripts-' . $page_settings, array( &$this, 'enqueue_settings_scripts' ) );
		add_action( 'admin_print_scripts-' . $export_settings, array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_head-' . $page_settings, array( &$this, 'ajax_actions' ) );
	}

	/**
	* Register network admin menus.
	*
	* @access public
	* @return void
	*/
	function network_admin_menu() {
		add_menu_page( __('CustomPress', $this->text_domain), __('CustomPress', $this->text_domain), 'manage_network', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );

		$page_content_types = add_submenu_page( 'ct_content_types' , __( 'Content Types', $this->text_domain ), __( 'Content Types', $this->text_domain ), 'manage_network', 'ct_content_types', array( &$this, 'handle_content_types_page_requests' ) );
		$page_settings      = add_submenu_page( 'ct_content_types', __('Settings', $this->text_domain), __('Settings', $this->text_domain), 'manage_network', 'cp_main', array( &$this, 'handle_settings_page_requests' ) );

		$export_settings = add_submenu_page( 'ct_content_types', __('Export / Import', $this->text_domain), __('Export / Import', $this->text_domain), 'manage_network', 'ct_export', array( &$this, 'handle_export' ) );

		add_action( 'admin_print_scripts-' . $page_content_types, array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_print_scripts-' . $page_settings, array( &$this, 'enqueue_settings_scripts' ) );
		add_action( 'admin_print_scripts-' . $export_settings, array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_head-' . $page_settings, array( &$this, 'ajax_actions' ) );
	}

	function handle_export(){
		$this->render_admin('export');
	}
	/**
	* Load scripts on plugin specific admin pages only.
	*
	* @return void
	*/
	function enqueue_scripts() {
		wp_enqueue_style( 'ct-admin-styles', $this->plugin_url . 'ui-admin/css/styles.css');
		wp_enqueue_script( 'ct-admin-scripts', $this->plugin_url . 'ui-admin/js/ct-scripts.js', array( 'jquery' ) );
		wp_enqueue_script('jquery-combobox');
		wp_enqueue_style('jquery-combobox');
		wp_enqueue_script('jquery-validate');
		wp_enqueue_style('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-datepicker-lang');
	}

	/**
	* Load scripts on plugin specific admin pages only.
	*
	* @return void
	*/
	function enqueue_settings_scripts() {
		wp_enqueue_script( 'settings-admin-scripts', $this->plugin_url . 'ui-admin/js/settings-scripts.js', array( 'jquery' ) );
		wp_enqueue_script('jquery-combobox');
		wp_enqueue_style('jquery-combobox');
		wp_enqueue_script('jquery-validate');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-datepicker-lang');
	}

	/**
	* Load styles for "Custom Fields" on add/edit post type pages only.
	*
	* @return void
	*/
	function enqueue_custom_field_scripts() {
		wp_enqueue_style( 'ct-admin-custom-field-styles', $this->plugin_url . 'ui-admin/css/custom-fields-styles.css' );
		wp_enqueue_script('jquery-combobox');
		wp_enqueue_style('jquery-combobox');
		wp_enqueue_script('jquery-validate');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-datepicker-lang');
	}

	/**
	* Handle $_GET and $_POST requests for Settings admin page.
	*
	* @return void
	*/
	function handle_settings_page_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// Save settings
		if ( isset( $params['save'] ) && wp_verify_nonce( $params['_wpnonce'], 'verify' ) ) {

			// Set network-wide content types
			if ( is_multisite() && is_super_admin() && is_network_admin() ) {
				update_site_option( 'allow_per_site_content_types', (bool) $params['enable_subsite_content_types'] );
				update_site_option( 'display_network_content_types', (bool) $params['display_network_content_types'] );
			}

			// Create template file
			if ( !empty( $params['post_type_file'] ) ) {
				$this->create_post_type_files( $params['post_type_file'] );
			}

			// Process post types display
			$options = $this->get_options();


			$dpt = array();
			$args = array( 'page' => 'home', 'post_type' => ( isset( $params['cp_post_type']['home'] ) ) ? $params['cp_post_type']['home'] : null );
			$display_post_types['display_post_types'][$args['page']] = $args;

			$args = array( 'page' => 'archive', 'post_type' => ( isset( $params['cp_post_type']['archive'] ) ) ? $params['cp_post_type']['archive'] : null );
			$display_post_types['display_post_types'][$args['page']] = $args;

			$args = array( 'page' => 'front_page', 'post_type' => ( isset( $params['cp_post_type']['front_page'] ) ) ? $params['cp_post_type']['front_page'] : null );
			$display_post_types['display_post_types'][$args['page']] = $args;

			$args = array( 'page' => 'search', 'post_type' => ( isset( $params['cp_post_type']['search'] ) ) ? $params['cp_post_type']['search'] : null );
			$display_post_types['display_post_types'][$args['page']] = $args;

			$options = array_merge( $options , $display_post_types );

			//Update datepicker settings
			if (! empty($params['datepicker_theme']) && ! empty($params['date_format']))
			$options = array_merge( $options, array('datepicker_theme' => $params['datepicker_theme'], 'date_format' => $params['date_format']  ) );
			update_option( $this->options_name, $options );
		}

		$this->render_admin('settings');
	}

	/**
	* Handle $_GET and $_POST requests for Content Types admin page.
	*
	* @return void
	*/
	function handle_content_types_page_requests() {
		$this->render_admin('navigation');
		if ( empty( $_GET['ct_content_type'] ) || $_GET['ct_content_type'] == 'post_type' ) {
			if ( isset( $_GET['ct_add_post_type'] ) )
			$this->render_admin('add-post-type');
			elseif ( isset( $_GET['ct_edit_post_type'] ) )
			$this->render_admin('edit-post-type');
			else
			$this->render_admin('post-types');
		}
		elseif ( $_GET['ct_content_type'] == 'taxonomy' ) {
			if ( isset( $_GET['ct_add_taxonomy'] ) )
			$this->render_admin('add-taxonomy');
			elseif ( isset( $_GET['ct_edit_taxonomy'] ) )
			$this->render_admin('edit-taxonomy');
			else
			$this->render_admin('taxonomies');
		}
		elseif ( $_GET['ct_content_type'] == 'custom_field' ) {
			if ( isset( $_GET['ct_add_custom_field'] ) )
			$this->render_admin('add-custom-field');
			elseif ( isset( $_GET['ct_edit_custom_field'] ) )
			$this->render_admin('edit-custom-field');
			else
			$this->render_admin('custom-fields');
		}
		elseif ( $_GET['ct_content_type'] == 'shortcodes' ) {
			$this->render_admin('shortcodes');
		}
	}

	/**
	* Ajax callback which gets the post types associated with each page.
	*
	* @return JSON Encoded string
	*/
	function ajax_get_caps() {
		global $wp_roles;

		if ( !current_user_can( 'manage_options' ) ) die(-1);

		if(empty($_REQUEST['role'])) die(-1);
		if(empty($_REQUEST['post_type'])) die(-1);

		$role = $_REQUEST['role'];
		$post_type = $_REQUEST['post_type'];

		if ( !$wp_roles->is_role( $role ) )
		die(-1);

		if ( !post_type_exists( $post_type ) )
		die(-1);

		$role_obj = $wp_roles->get_role( $role );

		$all_caps = $this->all_capabilities($post_type);

		$response = array_intersect( array_keys( $role_obj->capabilities ), $all_caps );
		$response = array_flip( $response );

		wp_send_json( $response);
	}

	/**
	* Save admin options.
	*
	* @return void die() if _wpnonce is not verified
	*/
	function ajax_save() {

		check_admin_referer( 'submit_post_type' );

		if ( !current_user_can( 'manage_options' ) ) die(-1);

		// add/remove capabilities
		global $wp_roles;

		$role = $_REQUEST['roles'];
		$post_type = $_REQUEST['post_type'];

		$all_caps = $this->all_capabilities($post_type);

		$to_add = array_keys( (array)$_REQUEST['capabilities'] );
		$to_remove = array_diff( $all_caps, $to_add );

		foreach ( $to_remove as $capability ) {
			$wp_roles->remove_cap( $role, $capability );
		}

		foreach ( $to_add as $capability ) {
			$wp_roles->add_cap( $role, $capability );
		}
		die(1);
	}

	/**
	* Intercept $_POST request and processes the custom post type submissions.
	*
	* @return void
	*/
	function handle_post_type_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If add/update request is made
		if ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_post_type' )
		) {
			// Validate input fields
			if ( $this->validate_field( 'post_type', strtolower( $params['post_type'] ) ) ) {
				// Post type labels
				$labels = array(
				'name'                  => $params['labels']['name'],
				'singular_name'         => $params['labels']['singular_name'],
				'add_new'               => $params['labels']['add_new'],
				'add_new_item'          => $params['labels']['add_new_item'],
				'edit_item'             => $params['labels']['edit_item'],
				'new_item'              => $params['labels']['new_item'],
				'view_item'             => $params['labels']['view_item'],
				'search_items'          => $params['labels']['search_items'],
				'not_found'             => $params['labels']['not_found'],
				'not_found_in_trash'    => $params['labels']['not_found_in_trash'],
				'parent_item_colon'     => $params['labels']['parent_item_colon'],
				'custom_fields_block'   => $params['labels']['custom_fields_block']
				);

				//choose regular taxonomies
				$supports_reg_tax = array (
				'category' => ( isset( $params['supports_reg_tax']['category'] ) ) ? '1' : '',
				'post_tag' => ( isset( $params['supports_reg_tax']['post_tag'] ) ) ? '1' : '',
				);

				// Post type args
				$args = array(
				'labels'              => $labels,
				'supports'            => $params['supports'],
				'supports_reg_tax'    => $supports_reg_tax,
				'capability_type'     => ( isset( $params['capability_type'] ) ) ? $params['capability_type'] : 'post',
				'map_meta_cap'        => (bool) $params['map_meta_cap'],
				'description'         => $params['description'],
				'menu_position'       => (empty($params['menu_position']) ) ? '' : (int)  $params['menu_position'],
				'public'              => (bool) $params['public'] ,
				'show_ui'             => ( isset( $params['show_ui'] ) ) ? (bool) $params['show_ui'] : null,
				'show_in_nav_menus'   => ( isset( $params['show_in_nav_menus'] ) ) ? (bool) $params['show_in_nav_menus'] : null,
				'publicly_queryable'  => ( isset( $params['publicly_queryable'] ) ) ? (bool) $params['publicly_queryable'] : null,
				'exclude_from_search' => ( isset( $params['exclude_from_search'] ) ) ? (bool) $params['exclude_from_search'] : null,
				'hierarchical'        => (bool) $params['hierarchical'],
				'has_archive'		  => (bool) $params['has_archive'],
				'rewrite'             => (bool) $params['rewrite'],
				'query_var'           => (bool) $params['query_var'],
				'can_export'          => (bool) $params['can_export'],
				'cf_columns'          => $params['cf_columns'],
				);
				
				$args['capabilities'] = array('create_posts' => "create_{$args['capability_type']}s" );

				// Remove empty labels so we can use the defaults
				foreach( $args['labels'] as $key => $value ) {
					if ( empty( $value ) )
					unset( $args['labels'][$key] );
				}

				// Set menu icon
				if ( !empty( $params['menu_icon'] ) )
				$args['menu_icon'] = $params['menu_icon'];

				// remove keys so we can use the defaults
				if ( $params['public'] == 'advanced' ) {
					unset( $args['public'] );
				} else {
					unset( $args['show_ui'] );
					unset( $args['show_in_nav_menus'] );
					unset( $args['publicly_queryable'] );
					unset( $args['exclude_from_search'] );
				}

				// Set slug for post type archive pages
				if ( !empty( $params['has_archive_slug'] ) && $args['has_archive'] !== false )
				$args['has_archive'] = sanitize_key($params['has_archive_slug']);

				// Set key for post type query var
				if ( !empty( $params['query_var_key'] ) && $args['query_var'] !== false)
				$args['query_var'] = sanitize_key($params['query_var_key']);

				// Customize taxonomy rewrite
				if ( !empty( $params['rewrite'] ) ) {

					if ( !empty( $params['rewrite_slug'] ) )
					$args['rewrite'] = (array) $args['rewrite'] + array( 'slug' => $params['rewrite_slug'] );

					$args['rewrite'] = (array) $args['rewrite'] + array( 'with_front' => !empty( $params['rewrite_with_front'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'feeds' => !empty( $params['rewrite_feeds'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'pages' => !empty( $params['rewrite_pages'] ) ? true : false );

					$args['rewrite']['ep_mask'] = array_sum($params['ep_mask']);

					// Remove boolean remaining from the type casting
					if ( is_array( $args['rewrite'] ) )
					unset( $args['rewrite'][0] );

					$this->flush_rewrite_rules = true;
				}

				// Set new post types
				$post_type = ( $params['post_type'] ) ? strtolower( $params['post_type'] ) : $_GET['ct_edit_post_type'];

				// Check whether we have a new post type and set flag which will later be used in register_post_types()
				if ( !is_array( $this->all_post_types ) || !array_key_exists( $post_type, $this->all_post_types ) ){
					$this->flush_rewrite_rules = true;
				}

				// Update options with the post type options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
					$post_types = ( $this->post_types ) ? array_merge( $this->post_types, array( $post_type => $args ) ) : array( $post_type => $args );
					update_option( 'ct_custom_post_types', $post_types );
				} else {
					$post_types = ( $this->network_post_types ) ? array_merge( $this->network_post_types, array( $post_type => $args ) ) : array( $post_type => $args );
					update_site_option( 'ct_custom_post_types', $post_types );
					// Set flag for flush rewrite rules network-wide
					flush_network_rewrite_rules();
				}

				// Redirect to post types page
				wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&updated&frr=' . $this->flush_rewrite_rules ) );
			}
		}
		elseif ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_post_type' )
		) {
			$post_types = (is_network_admin()) ? $this->network_post_types : $this->post_types;
			// remove the deleted post type
			unset( $post_types[$params['post_type_name']] );
			// update the available post types
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_post_types', $post_types );
			else
			update_site_option( 'ct_custom_post_types', $post_types );
			// Redirect to post types page

			flush_network_rewrite_rules();

			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&updated' ));
		}
		elseif ( isset( $params['redirect_add_post_type'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&ct_add_post_type=true' ) );
		}
	}

	/**
	* Intercepts $_POST request and processes the custom taxonomy requests
	*
	* @return void
	*/
	function handle_taxonomy_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If valid add/edit taxonomy request is made
		if (   isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_taxonomy' )
		) {
			// Validate input fields
			$valid_taxonomy = $this->validate_field( 'taxonomy', ( isset( $params['taxonomy'] ) ) ? strtolower( $params['taxonomy'] ) : null );
			$valid_object_type = $this->validate_field( 'object_type', ( isset( $params['object_type'] ) ) ? $params['object_type'] : null );

			if ( $valid_taxonomy && $valid_object_type ) {
				// Construct args
				$labels = array(
				'name'                       => $params['labels']['name'],
				'singular_name'              => $params['labels']['singular_name'],
				'add_new_item'               => $params['labels']['add_new_item'],
				'new_item_name'              => $params['labels']['new_item_name'],
				'edit_item'                  => $params['labels']['edit_item'],
				'update_item'                => $params['labels']['update_item'],
				'search_items'               => $params['labels']['search_items'],
				'popular_items'              => $params['labels']['popular_items'],
				'all_items'                  => $params['labels']['all_items'],
				'parent_item'                => $params['labels']['parent_item'],
				'parent_item_colon'          => $params['labels']['parent_item_colon'],
				'add_or_remove_items'        => $params['labels']['add_or_remove_items'],
				'separate_items_with_commas' => $params['labels']['separate_items_with_commas'],
				'choose_from_most_used'      => $params['labels']['all_items']
				);

				$args = array(
				'labels'              => $labels,
				'public'              => (bool) $params['public'] ,
				'show_ui'             => ( isset( $params['show_ui'] ) ) ? (bool) $params['show_ui'] : null,
				'show_tagcloud'       => ( isset( $params['show_tagcloud'] ) ) ? (bool) $params['show_tagcloud'] : null,
				'show_admin_column'   => ( isset( $params['show_admin_column'] ) ) ? (bool) $params['show_admin_column'] : null,
				'show_in_nav_menus'   => ( isset( $params['show_in_nav_menus'] ) ) ? (bool) $params['show_in_nav_menus'] : null,
				'hierarchical'        => (bool) $params['hierarchical'],
				'rewrite'             => (bool) $params['rewrite'],
				'query_var'           => (bool) $params['query_var'],
				'capabilities'        => array (
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'edit_posts',
				),
				);

				// Remove empty values from labels so we can use the defaults
				foreach( $args['labels'] as $key => $value ) {
					if ( empty( $value ))
					unset( $args['labels'][$key] );
				}

				// If no advanced is set, unset values so we can use the defaults
				if ( $params['public'] == 'advanced' ) {
					unset( $args['public'] );
				} else {
					unset( $args['show_ui'] );
					unset( $args['show_tagcloud'] );
					unset( $args['show_in_nav_menus'] );
				}

				// Set key for taxonomy query var
				if ( !empty( $params['query_var_key'] ) && $args['query_var'] !== false)
				$args['query_var'] = sanitize_key($params['query_var_key'] );

				// Customize taxonomy rewrite
				if ( !empty( $params['rewrite'] ) ) {

					if ( !empty( $params['rewrite_slug'] ) )
					$args['rewrite'] = (array) $args['rewrite'] + array( 'slug' => $params['rewrite_slug'] );

					$args['rewrite'] = (array) $args['rewrite'] + array( 'with_front' => !empty( $params['rewrite_with_front'] ) ? true : false );
					$args['rewrite'] = (array) $args['rewrite'] + array( 'hierarchical' => !empty( $params['rewrite_hierarchical'] ) ? true : false );

					$args['rewrite']['ep_mask'] = array_sum($params['ep_mask']);

					// Remove boolean remaining from the type casting
					if ( is_array( $args['rewrite'] ) )
					unset( $args['rewrite'][0] );

					$this->flush_rewrite_rules = true;
				}

				// Set the associated object types ( post types )
				if(is_network_admin() ) {
					$post_types = get_site_option('ct_custom_post_types');
				} else {
					$post_types = get_option('ct_custom_post_types');
				}
				$object_type = $params['object_type'];

				//Set assign_terms for this associated post_type if not already includes 'post'
				$cap_type = 'post';

				global $wp_post_types;
				foreach($object_type as $post_type){
					//Check the customs first
					$cap = $post_types[$post_type]['capability_type'];
					if( !empty($cap) && $cap != 'post') {
						$cap_type=$cap;
						break;
					}
					//No then check the builtins
					$cap = $wp_post_types[$post_type]->capability_type;
					if( !empty($cap) && $cap != 'post') {
						$cap_type=$cap;
						break;
					}
				}
				$args['capabilities']['assign_terms'] = "edit_{$cap_type}s";

				// Set the taxonomy which we are adding/updating
				$taxonomy = ( isset( $params['taxonomy'] )) ? strtolower( $params['taxonomy'] ) : $_GET['ct_edit_taxonomy'];

				// Set new taxonomies
				$taxonomies = ( $this->taxonomies )
				? array_merge( $this->taxonomies, array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) ) )
				: array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) );

				// Check whether we have a new post type and set flush rewrite rules
				if ( !is_array( $this->all_taxonomies ) || !array_key_exists( $taxonomy, $this->all_taxonomies ) ){
					$this->flush_rewrite_rules = true;
				}
				// Update wp_options with the taxonomies options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
					$taxonomies = ( $this->taxonomies )
					? array_merge( $this->taxonomies, array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) ) )
					: array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) );
					update_option( 'ct_custom_taxonomies', $taxonomies );
				} else {
					$taxonomies = ( $this->network_taxonomies )
					? array_merge( $this->network_taxonomies, array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) ) )
					: array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ) );
					update_site_option( 'ct_custom_taxonomies', $taxonomies );
					// Set flag for flush rewrite rules network-wide
					flush_network_rewrite_rules();
				}

				// Redirect back to the taxonomies page
				wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&updated&frr' . $this->flush_rewrite_rules ) );
			}
		}
		elseif ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_taxonomy' )
		) {
			// Set available taxonomies
			$taxonomies = (is_network_admin()) ? $this->network_taxonomies : $this->taxonomies;

			// Remove the deleted taxonomy
			unset( $taxonomies[$params['taxonomy_name']] );

			// Update the available taxonomies
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
				update_option( 'ct_custom_taxonomies', $taxonomies );
			} else {
				update_site_option( 'ct_custom_taxonomies', $taxonomies );
				flush_network_rewrite_rules();
			}

			// Redirect back to the taxonomies page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&updated' ) );
		}
		elseif ( isset( $params['redirect_add_taxonomy'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&ct_add_taxonomy=true' ));
		}
	}

	/**
	* Intercepts $_POST request and processes the custom fields submissions
	*/
	function handle_custom_field_requests() {

		//$params is the $_POST variable with slashes stripped
		$params = array_map('stripslashes_deep',$_POST);

		// If valid add/edit custom field request is made
		if ( isset( $params['submit'] )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'submit_custom_field' )
		) {

			// Validate input fields data
			$field_title_valid       = $this->validate_field( 'field_title', ( isset( $params['field_title'] ) ) ? $params['field_title'] : null );
			$field_object_type_valid = $this->validate_field( 'object_type', ( isset( $params['object_type'] ) ) ? $params['object_type'] : null );

			// Check for specific field types and validate differently
			if ( in_array( $params['field_type'], array( 'radio', 'checkbox', 'selectbox', 'multiselectbox' ) ) ) {

				$field_options_valid = $this->validate_field( 'field_options', $params['field_options'] ); //2 because an empty first choice is common
				// Check whether fields pass the validation, if not stop execution and return
				if ( $field_title_valid == false || $field_object_type_valid == false || $field_options_valid == false )
				return;
			} else {
				// Check whether fields pass the validation, if not stop execution and return
				if ( $field_title_valid == false || $field_object_type_valid == false )
				return;
			}

			if ( 2 == $params['field_wp_allow'] && empty( $_GET['ct_edit_custom_field'] ) ) {
				$field_title = str_replace( ' ', '_', $params['field_title'] );
				$field_title = substr( preg_replace ( '/\W/', '', $field_title ), 0, 10);
				$field_id = $field_title . '_' . $params['field_type'] . '_' . substr( uniqid(''), 7, 4) ;
			} else {
				$field_id = ( empty( $_GET['ct_edit_custom_field'] ) ) ? $params['field_type'] . '_' . uniqid('') : $_GET['ct_edit_custom_field'];
			}

			$args = array(
			'field_title'          => $params['field_title'],
			'field_wp_allow'       => ( 2 == $params['field_wp_allow'] ) ? 1 : 0,
			'field_type'           => $params['field_type'],
			'field_sort_order'     => $params['field_sort_order'],
			'field_options'        => $params['field_options'],
			'field_date_format'    => $params['field_date_format'],
			'field_regex'          => trim($params['field_regex']),
			'field_regex_options'  => trim($params['field_regex_options']),
			'field_regex_message'  => trim($params['field_regex_message']),
			'field_message'        => trim($params['field_message']),
			'field_default_option' => ( isset( $params['field_default_option'] ) ) ? $params['field_default_option'] : NULL,
			'field_description'    => (empty($params['field_description']) ) ? '' : $params['field_description'],
			'object_type'          => $params['object_type'],
			'hide_type'            => (empty($params['hide_type']) ) ? array() : $params['hide_type'] ,
			'field_required'       => (2 == $params['field_required'] ) ? 1 : 0,
			'field_id'             => $field_id,
			);


			// Unset if there are no options to be stored in the db
			if ( $args['field_type'] == 'text' || $args['field_type'] == 'textarea'){
				unset( $args['field_options'] );
			} else {
				//regex on text only
				unset( $args['field_regex'] );
				unset( $args['field_regex_message'] );
				unset( $args['field_regex_options'] );
			}

			// Set new custom fields

			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() ) {
				$custom_fields = ( $this->custom_fields )
				? array_merge( $this->custom_fields, array( $field_id => $args ) )
				: array( $field_id => $args );
				$i = 0;
				foreach($custom_fields as &$custom_field){
					$custom_field['field_order'] = $i++;
				}
				update_option( 'ct_custom_fields', $custom_fields );
			} else {
				$custom_fields = ( $this->network_custom_fields )
				? array_merge( $this->network_custom_fields, array( $field_id => $args ) )
				: array( $field_id => $args );
				$i = 0;
				foreach($custom_fields as &$custom_field){
					$custom_field['field_order'] = $i++;
				}
				update_site_option( 'ct_custom_fields', $custom_fields );
			}

			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );
		}
		elseif ( ( isset( $params['submit'] ) || isset( $params['delete_cf_values'] ) )
		&& isset( $params['_wpnonce'] )
		&& wp_verify_nonce( $params['_wpnonce'], 'delete_custom_field' )
		) {
			// Set available custom fields
			$custom_fields = (is_network_admin()) ? $this->network_custom_fields : $this->custom_fields;

			// Remove all values of custom field
			if ( isset( $params['delete_cf_values'] ) ) {

				if ( isset( $custom_fields[$params['custom_field_id']]['field_wp_allow'] ) && 1 == $custom_fields[$params['custom_field_id']]['field_wp_allow'] )
				$prefix = 'ct_';
				else
				$prefix = '_ct_';

				global $wpdb;
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", $prefix . $params['custom_field_id'] ) );
			}

			//update custom fields columns for custom post type
			$cf_columns_update = 0;
			foreach ( $custom_fields[$params['custom_field_id']]['object_type'] as $object_type ) {

				if ( is_array( $this->network_post_types[$object_type]['cf_columns'] ) )
				if( is_network_admin() ){
					foreach ( $this->network_post_types[$object_type]['cf_columns'] as $key => $value )
					if ( $params['custom_field_id'] == $key ) {
						unset( $this->network_post_types[$object_type]['cf_columns'][$key] );
						$cf_columns_update = 1;
					}
				} else {
					if ( is_array( $this->post_types[$object_type]['cf_columns'] ) )
					foreach ( $this->post_types[$object_type]['cf_columns'] as $key => $value )
					if ( $params['custom_field_id'] == $key ) {
						unset( $this->post_types[$object_type]['cf_columns'][$key] );
						$cf_columns_update = 1;
					}
				}
			}

			if ( 1 == $cf_columns_update ) {
				// Update options with the post type options
				if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
				update_option( 'ct_custom_post_types', $this->post_types );
				else
				update_site_option( 'ct_custom_post_types', $this->network_post_types );
			}


			// Remove the deleted custom field
			unset( $custom_fields[$params['custom_field_id']] );

			//Set the field_order of the fields to the current default order
			$i = 0;
			foreach($custom_fields as &$custom_field){
				$custom_field['field_order'] = $i++;
			}

			// Update the available custom fields
			if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
			update_option( 'ct_custom_fields', $custom_fields );
			else
			update_site_option( 'ct_custom_fields', $custom_fields );

			// Redirect back to the taxonomies page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );
		}
		elseif ( isset( $_GET['ct_reorder_custom_field'] )
		&& isset( $_GET['_wpnonce'] )
		&& wp_verify_nonce( $_GET['_wpnonce'], 'reorder_custom_fields' )
		) {

			// Reorder the fields
			$dir = $_GET['direction'];
			$fid = $_GET['ct_reorder_custom_field'];

			$custom_fields = (is_network_admin()) ? $this->network_custom_fields : $this->custom_fields;

			//Set the field_order of the fields to the current default order
			$i = 0;
			foreach($custom_fields as &$custom_field){
				$custom_field['field_order'] = $i++;
			}

			$keys = array_keys($custom_fields);
			$key = array_search($fid, $keys);

			if($key !== false) {
				$swapped = false;
				if($dir == 'up' && $key > 0) {
					$ndx = $custom_fields[$keys[$key]]['field_order'];
					$custom_fields[$keys[$key]]['field_order']=$custom_fields[$keys[$key-1]]['field_order'];
					$custom_fields[$keys[$key-1]]['field_order'] = $ndx;
					$swapped = true;
				}
				if($dir == 'down' && array_key_exists($key+1, $keys)) {
					$ndx = $custom_fields[$keys[$key]]['field_order'];
					$custom_fields[$keys[$key]]['field_order']=$custom_fields[$keys[$key+1]]['field_order'];
					$custom_fields[$keys[$key+1]]['field_order'] = $ndx;
					$swapped = true;
				}

				if($swapped){

					if(!function_exists('ct_cmp')){
						function ct_cmp($a, $b){
							if ($a['field_order'] == $b['field_order']) return 0;
							return ($a['field_order'] < $b['field_order']) ? -1 : 1;
						}

						if (uasort($custom_fields, 'ct_cmp')){
							// Update the available custom fields
							if ( $this->enable_subsite_content_types == 1 && !is_network_admin() )
							update_option( 'ct_custom_fields', $custom_fields );
							else
							update_site_option( 'ct_custom_fields', $custom_fields );
						}
					}

				}
			}
			// Redirect back to the custom fields page
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&updated' ) );


		}
		elseif ( isset( $params['redirect_add_custom_field'] ) ) {
			wp_redirect( self_admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&ct_add_custom_field=true' ));
		}
	}

	/**
	* Validates input fields data
	*
	* @param string $field
	* @param mixed $value
	* @return bool true/false depending on validation outcome
	*/
	function validate_field( $field, $value ) {
		// Validate set of common fields
		if ( $field == 'taxonomy' || $field == 'post_type' ) {
			// Regular expression. Checks for alphabetic characters and underscores only
			if ( preg_match( '/^[a-zA-Z0-9_]{2,}$/', $value ) ) {
				return true;
			} else {
				if ( $field == 'post_type' )
				add_action( 'ct_invalid_field_post_type', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'taxonomy' )
				add_action( 'ct_invalid_field_taxonomy', create_function( '', 'echo "form-invalid";' ) );
				return false;
			}
		}

		// Validate set of common fields
		if ( $field == 'object_type' || $field == 'field_title' || $field == 'field_options' ) {

			if (is_array($value)){

				$invalid = true;

				foreach($value as $item){
					$invalid = (!empty($item)) ? false : $invalid;
				}

			} else {
				$invalid = empty($value);
			}

			if ( $invalid ) {
				if ( $field == 'object_type' )
				add_action( 'ct_invalid_field_object_type', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'field_title' )
				add_action( 'ct_invalid_field_title', create_function( '', 'echo "form-invalid";' ) );
				if ( $field == 'field_options' )
				add_action( 'ct_invalid_field_options', create_function( '', 'echo "form-invalid";' ) );
				return false;
			} else {
				return true;
			}
		}
	}


	/**
	* Add Custom field edit metaboxes
	*
	* @return void
	*/
	function on_add_meta_boxes() {

		$current_post_type = $this->get_current_post_type();

		$net_custom_fields = get_site_option('ct_custom_fields');

		//		if ( $this->display_network_content && !empty($net_custom_fields)) {
		//			//get the network fields
		//			$net_post_types = get_site_option('ct_custom_post_types');
		//			$meta_box_label = __('Default CustomPress Fields', $this->text_domain);
		//
		//			//If we have this post type rename the metabox
		//			if($current_post_type) {
		//				if ( ! empty( $net_post_types[$current_post_type]['labels']['custom_fields_block'] ) )
		//				$meta_box_label = $net_post_types[$current_post_type]['labels']['custom_fields_block'];
		//
		//			}
		//			//Do we even need the metabox
		//			$has_fields = false;
		//			foreach ( $net_custom_fields as $custom_field ) {
		//				$has_fields = (is_array($custom_field['object_type']) ) ? in_array($current_post_type, $custom_field['object_type']) : false;
		//				if ($has_fields){
		//					add_meta_box( 'ct-network-custom-fields', $meta_box_label, array( &$this, 'display_custom_fields_network' ), $current_post_type, 'normal', 'high' );
		//					break;
		//				}
		//			}
		//		}

		if($this->display_network_content) $custom_fields = $this->all_custom_fields;
		else $custom_fields = $this->custom_fields;

		if ( ! empty($custom_fields)) {
			//get the local fields
			$meta_box_label = __('CustomPress Fields', $this->text_domain);

			//If we have this post type rename the metabox
			if($current_post_type) {
				if ( ! empty( $this->network_post_types[$current_post_type]['labels']['custom_fields_block'] ) )
				$meta_box_label = $this->network_post_types[$current_post_type]['labels']['custom_fields_block'];

				if ( ! empty( $this->post_types[$current_post_type]['labels']['custom_fields_block'] ) )
				$meta_box_label = $this->post_types[$current_post_type]['labels']['custom_fields_block'];
			}
			//Do we even need the metabox
			$has_fields = false;
			foreach ( $custom_fields as $custom_field ) {
				$has_fields = (is_array($custom_field['object_type']) ) ? in_array($current_post_type, $custom_field['object_type']) : false;
				if ($has_fields){
					add_meta_box( 'ct-custom-fields', $meta_box_label, array( &$this, 'display_custom_fields' ), $current_post_type, 'normal', 'high' );
					break;
				}
			}
		}
	}



}

/* Initiate Admin Class */
if(is_admin()) {
	$CustomPress_Core = new CustomPress_Core_Admin();
}
endif;
