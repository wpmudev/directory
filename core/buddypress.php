<?php
/**
* Directory Core Buddypress Class
**/

if ( !class_exists('Directory_Core_Buddypress') ):
class Directory_Core_Buddypress extends Directory_Core {

	/**
	* Constructor.
	*
	* @return void
	**/

	function Directory_Core_Buddypress() { __construct();}

	function __construct(){

		parent::__construct(); //Get the inheritance right

	}

	function init(){
		global $wp, $wp_rewrite;

		parent::init(); //Inheritance

		/* Set BuddyPress active state */
		$this->bp_active = true;

		/* Add navigation */
		add_action( 'wp', array( &$this, 'add_navigation' ), 2 );

		/* Add navigation */
		add_action( 'admin_menu', array( &$this, 'add_navigation' ), 2 );

		/* Enqueue styles */
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'wp_print_scripts', array( &$this, 'on_print_scripts' ) );
		add_action( 'wp_head', array( &$this, 'print_scripts' ) );
		add_action( 'bp_template_content', array( &$this, 'process_page_requests' ) );

		/* template for  page */
		//add_action( 'template_redirect', array( &$this, 'handle_nav' ) );
		add_action( 'template_redirect', array( &$this, 'handle_page_requests' ) );

	}

	/**
	* Add BuddyPress navigation.
	*
	* @return void
	**/
	function add_navigation() {
		global $bp;

		/* Set up directory as a sudo-component for identification and nav selection */
		$directory_page = get_page($this->directory_page_id);

		if (! @is_object($bp->directory) ){
			$bp->directory = new stdClass;
		}

		$bp->directory->slug = $directory_page->post_name;
		/* Construct URL to the BuddyPress profile URL */
		$user_domain = ( !empty( $bp->displayed_user->domain ) ) ? $bp->displayed_user->domain : $bp->loggedin_user->domain;
		$parent_url = $user_domain . $bp->directory->slug . '/';

		/* Add the settings navigation item */

		if ( 0 < $directory_page->ID )
		$nav_title = $directory_page->post_title;
		else
		$nav_title = 'Directory';

		bp_core_new_nav_item( array(
		'name'                    => __( $nav_title, $this->text_domain ),
		'slug'                    => $bp->directory->slug,
		'position'                => 100,
		'show_for_displayed_user' => true,
		'screen_function'         => array( &$this, 'load_template' )
		));

		if ( bp_is_my_profile() ) {

			$directory_page = get_page($this->my_listings_page_id);

			if ( 0 < $directory_page->ID )
			$nav_title = $directory_page->post_title;
			else
			$nav_title = 'My Directory';

			bp_core_new_subnav_item( array(
			'name'            => __( $nav_title, $this->text_domain ),
			'slug'            => $directory_page->post_name,
			'parent_url'      => $parent_url,
			'parent_slug'     => $bp->directory->slug,
			'screen_function' => array( &$this, 'load_template' ),
			'position'        => 10,
			'user_has_access' => true
			));

			if($this->use_credits && ! $this->is_full_access()){
				bp_core_new_subnav_item( array(
				'name'            => __( 'My Credits', $this->text_domain ),
				'slug'            => 'my-credits',
				'parent_url'      => $parent_url,
				'parent_slug'     => $bp->directory->slug,
				'screen_function' => array( &$this, 'load_template' ),
				'position'        => 10,
				'user_has_access' => true
				));
			}

			if( current_user_can('create_listings') ){
				bp_core_new_subnav_item( array(
				'name'            => __( 'Add Listing', $this->text_domain ),
				'slug'            => 'add-listing',
				'parent_url'      => $parent_url,
				'parent_slug'     => $bp->directory->slug,
				'screen_function' => array( &$this, 'load_template' ),
				'position'        => 10,
				'user_has_access' => true
				));
			}

		} else {
			//display author classifids page
			bp_core_new_subnav_item( array(
			'name'            => __( 'All', $this->text_domain ),
			'slug'            => 'all',
			'parent_url'      => $parent_url,
			'parent_slug'     => $bp->directory->slug,
			'screen_function' => array( &$this, 'load_template' ),
			'position'        => 10,
			'user_has_access' => true
			));
		}
	}

	/**
	* Load BuddyPress theme template file for plugin specific page.
	*
	* @return void
	**/
	function load_template() {
		/* This is generic BuddyPress plugins file. All other functions hook
		* themselves into the plugins template hooks. Each BuddyPress component
		* "members", "groups", etc. offers different plugin file and different hooks */

		bp_core_load_template( 'members/single/plugins', true );
	}

	function handle_nav(){

		global $bp, $post;

		/* Handles request for directory page */
		if ( $bp->current_component == $this->directory_page_slug && $bp->current_action == 'all' ) {
			$this->js_redirect( trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug .'/' . $this->my_listings_page_slug . '/active', true);

		}
		elseif( $post->ID == $this->my_listings_page_id ) {
			/* Set the proper step which will be loaded by "page-my-listings.php" */
			$this->js_redirect( trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug .'/' . $this->my_listings_page_slug . '/active', true);
		}
	}


	/**
	* Load the content for the specific directory component and handle requests
	*
	* @global object $bp
	* @return void
	**/
	function process_page_requests() {
		global $bp;

		$options = $this->get_options('payments');

		//Component my-listings page
		if ( $bp->current_component == $this->directory_page_slug && $bp->current_action == $this->my_listings_page_slug ) {
			if ( isset( $_POST['action'] ) && 'delete_listing' ==  $_POST['action'] && wp_verify_nonce( $_POST['_wpnonce'], 'action_verify' ) ) {
				if ( $this->user_can_edit_listing( $_POST['post_id'] ) ) {
					wp_delete_post( $_POST['post_id'] );
					wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is deleted!', $this->text_domain ) ) ), get_permalink($this->my_listings_page_id) ) );
					exit;
				}
			}

			$this->render_front('my-listings');
		}
		//Component add-listing page
		elseif ( $bp->current_component == $this->directory_page_slug && $bp->current_action == 'add-listing' ) {

//			//Are we adding a Listing?
//			if(! $this->use_free
//			&& ! (current_user_can('create_listings') && current_user_can('publish_listings') ) ) {
//				wp_redirect( get_permalink($this->signup_page_id) );
//				exit;
//			}

			if ( isset( $_POST['update_listing'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'verify' ) ) {

				$draft = $_POST['listing_data']['post_status'] == 'draft';
				$post_id = $this->update_listing( $_POST );
				$meta = new DR_Meta($post_id);

				// The credits required to renew the classified for the selected period
				$credits_required = ($this->use_free) ? 0 : $options['credits_per_listing'];
				if( ! ($draft || $meta->status == 'paid') ) {


					// If user have more credits of the required credits proceed with renewing the ad
					if ($this->is_full_access() || ( $this->user_credits >= $credits_required ) ){
						if ( ! $this->is_full_access() ) {
							// Update new credits amount
							$this->transactions->credits -= $credits_required;
						} else {
							//Check one_time
							if($this->transactions->billing_type == 'one_time') $this->transactions->status = 'used';
						}

						$meta->status = 'paid';

						$this->js_redirect( trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug . '/' . $this->my_listings_page_slug );

						if(is_page($this->add_listing_page_id)){
							wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'New Listing is added!', $this->text_domain ) ) ), get_permalink($this->my_listings_page_id) ) );
						} else {
							wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is updated!', $this->text_domain ) ) ), get_permalink($this->my_listings_page_id) ) );
						}
						exit;

					} else {

						//save listing if have no credits
						wp_update_post(array('ID' => $post_id, 'post_status' => 'draft') );
						$_POST['listing_data']['post_status'] = 'draft';
						set_query_var( 'dr_post_id', $post_id );
						/* Set the proper step which will be loaded by "page-my-listings.php" */
						set_query_var( 'dr_action', 'edit' );
						$error = __( 'You do not have enough credits to publish your listing. Please purchase more credits. Your Listing has been saved as a Draft.', $this->text_domain );
						set_query_var( 'dr_error', $error );
						$this->render_front('update-listing', array( 'dr_error' => $error ));
					}
				}
			} else {
				$this->render_front('update-listing', array());
			}
		}
		//Component my-credits page
		elseif ( $bp->current_component == $this->directory_page_slug && $bp->current_action == 'my-credits' ) {
			//redirect on signup page
			if ( isset( $_POST['purchase'] ) ) {
				$this->js_redirect( get_permalink($this->signup_page_id) );
				exit;
			}
			//show credits page
			$this->render_front('my-credits');
		}

		//Component Author directory page (directory/all)
		elseif ( $bp->current_component == $this->directory_page_slug && $bp->current_action == 'all' ) {
			if ( bp_is_my_profile() ) {

				//show author directory page
				$this->render_front('my-listings');
			} else {
				$this->render_front('listings');

			}
		}
		//default for directory page
		elseif ( $bp->current_component == $this->directory_page_slug ) {
			if ( bp_is_my_profile() ) {
				$this->js_redirect( trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug . '/' . $this->my_listings_page_slug );
			} else {
				$this->js_redirect( trailingslashit($bp->displayed_user->domain) . $this->directory_page_slug . '/' . 'all' );
			}
		}
	}

	/**
	* Handle $_REQUEST for main pages.
	*
	* @uses set_query_var() For passing variables to pages
	* @return void|die() if "_wpnonce" is not verified
	**/
	function handle_page_requests() {
		global $bp, $wp_query;

		/* Handles request for listings page */
		$templates = array();
		$page_template = locate_template( array('page.php', 'index.php' ) );
		$taxonomy = (empty($wp_query->query_vars['taxonomy']) ) ? '' : $wp_query->query_vars['taxonomy'];

		$logged_url = trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug . '/';
		/*
		if ( $bp->current_component == $this->directory_page_slug && $bp->current_action == 'all' ) {
		$this->js_redirect( $logged_url . $this->my_listings_page_slug . '/active', true);
		}

		else
		*/
		if ( is_post_type_archive('directory_listing') ) {
			/* Set the proper step which will be loaded by "page-my-listings.php" */
			$templates = array( 'page-listing.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;
				$wp_query->post_count = 1;
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				add_filter('the_content', array(&$this, 'listing_list_theme'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			$this->is_directory_page = true;
		}
		elseif( is_page($this->my_listings_page_id ) ){
			/* Set the proper step which will be loaded by "page-my-listings.php" */
			$this->js_redirect( $logged_url . $this->my_listings_page_slug . '/active', true);
		}

		elseif(is_single() && 'directory_listing' == get_query_var('post_type')){
			$templates = array( 'single-listing.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;

				$wp_query->is_single    = 0;
				$wp_query->is_page      = 1;

				add_filter('the_content', array(&$this, 'listing_content'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			$this->is_directory_page = true;
		}

		elseif(is_page($this->my_credits_page_id) ){
			wp_redirect($logged_url . 'my-credits'); exit;
			$templates = array( 'page-my-credits.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;
				add_filter('the_content', array(&$this, 'my_credits_content'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
		}

		elseif(is_page($this->signup_page_id) ){
			$templates = array( 'page-signup.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;

				add_filter('the_content', array(&$this, 'signup_content'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
		}

		elseif(is_page($this->signin_page_id) ){
			$templates = array( 'page-signin.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;

				add_filter( 'the_title', array( &$this, 'delete_post_title' ), 11 ); //after wpautop
				add_filter('the_content', array(&$this, 'signin_content'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
		}
		//load proper theme for listing category or tag
		elseif ( is_archive() && in_array($taxonomy, array('listing_category','listing_tag') ) ) {
			if ( 'listing_category' == $taxonomy ) {

				$cat_name = get_query_var( 'listing_category' );
				$cat_id = absint( $wp_query->get_queried_object_id() );

				if ( $cat_name )
				$templates[] = "dr_category-$cat_name.php";
				if ( $cat_id )
				$templates[] = "dr_category-$cat_id.php";

				$templates[] = 'dr_category.php';

			} elseif ( 'listing_tag' == $taxonomy ) {

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
			if ( is_numeric( get_query_var($taxonomy) ) ) {
				$term = get_term( get_query_var($taxonomy), $taxonomy );
				$wp_query->query_vars[$taxonomy] = $term->slug;
				$wp_query->query_vars['term'] = $term->slug;
			}

			//if custom template exists load it
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				$this->directory_template = $page_template;
				$wp_query->post_count = 1;
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ) );
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );

			$this->is_directory_page = true;
		}

		//Listings update pages
		elseif(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id)){

			wp_redirect($logged_url . 'add-listing/?' . http_build_query($_GET));
			exit;
		}
		/* If user wants to go to My Lisytings main page  */
		elseif ( isset( $_POST['go_my_listings'] ) ) {
			wp_redirect( get_permalink($this->my_listings_page_id) );
		}
		/* If user wants to go to My Listings main page  */
		elseif ( isset( $_POST['purchase'] ) ) {
			wp_redirect(  get_permalink($this->signup_page_id)  );
		} else {
			/* Set the proper step which will be loaded by "page-my-listings.php" */
			set_query_var( 'cf_action', 'my-listings' );
		}
		//load  specific items
		if ( $this->is_directory_page ) {
			add_filter( 'edit_post_link', array( &$this, 'delete_edit_post_link' ), 99 );

			//prevents 404 for virtual pages
			status_header( 200 );
		}

	}

	/**
	* Enqueue styles.
	*
	* @return void
	**/
	function enqueue_scripts() {
		if(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id)) {
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}

		if ( file_exists( get_template_directory() . '/style-bp-directory.css' ) )
		wp_enqueue_style( 'style-directory', get_template_directory() . '/style-bp-directory.css' );
		elseif ( file_exists( $this->plugin_dir . 'ui-front/buddypress/style-bp-directory.css' ) )
		wp_enqueue_style( 'style-directory', $this->plugin_url . 'ui-front/buddypress/style-bp-directory.css' );
	}

	/**
	* Print scripts for BuddyPress pages
	*
	* @global object $bp
	* @return void
	**/
	function print_scripts() {
		global $bp;
		if ( $bp->current_component == $this->directory_page_slug || is_single() ) {
			?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready(function($) {
					$('form.confirm-form').hide();
					$('form.cf-contact-form').hide();
				});
				var directory = {
					toggle_end: function(key) {
						jQuery('#confirm-form-'+key).show();
						jQuery('#action-form-'+key).hide();
						jQuery('input[name="action"]').val('end');
					},
					toggle_renew: function(key) {
						jQuery('#confirm-form-'+key).show();
						jQuery('#duration-'+key).show();
						jQuery('#action-form-'+key).hide();
						jQuery('input[name="action"]').val('renew');
					},
					toggle_delete: function(key) {
						jQuery('#confirm-form-'+key).show();
						jQuery('#action-form-'+key).hide();
						jQuery('#duration-'+key).hide();
						jQuery('input[name="action"]').val('delete');
					},
					toggle_contact_form: function() {
						jQuery('.cf-ad-info').hide();
						jQuery('#action-form').hide();
						jQuery('#confirm-form').show();
					},
					cancel_contact_form: function() {
						jQuery('#confirm-form').hide();
						jQuery('.cf-ad-info').show();
						jQuery('#action-form').show();
					},
					cancel: function(key) {
						jQuery('#confirm-form-'+key).hide();
						jQuery('#action-form-'+key).show();
					}
				};
				//]]>
			</script>
			<?php
		}
	}

	/**
	* Renders a section of user display code.  The code is first checked for in the current theme display directory
	* before defaulting to the plugin
	*
	* @param  string $name Name of the admin file(without extension)
	* @param  string $vars Array of variable name=>value that is available to the display code(optional)
	* @return void
	**/
	function render_front( $name, $vars = array() ) {
		/* Construct extra arguments */
		foreach ( $vars as $key => $val )
		$$key = $val;
		/* Include templates */

		$result = get_template_directory() . "/{$name}.php";
		if ( file_exists( $result ) ){
			include($result);
			return;
		}

		$result = "{$this->plugin_dir}ui-front/buddypress/members/single/directory/{$name}.php";
		if ( file_exists( $result ) && $this->bp_active ){
			include($result);
			return;
		}

		$result = "{$this->plugin_dir}ui-front/general/{$name}.php";

		if ( file_exists( $result ) ) {
			include($result);
			return;
		}

		echo "<p>Rendering of template $result {$name}.php failed</p>";
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
		<img src="<?php echo $this->plugin_url .'/ui-front/general/images/loader.gif'; ?>" alt="<?php _e( 'You are being redirected. Please wait.', $this->text_domain );  ?>" />
		<?php endif; ?>
		<script type="text/javascript">//<![CDATA[
			window.location = '<?php echo $url; ?>';	//]]>
		</script>
		<?php
	}

	/**
	* Print a list of javascript vars specific to Directory for use in javascript routines
	*
	*/
	function on_print_scripts(){
		global $bp;
		$logged_url = trailingslashit($bp->loggedin_user->domain) . $this->directory_page_slug . '/';


		echo '<script type="text/javascript">';
		echo "\nvar\n";
		echo "dr_listing = '" . esc_attr( $logged_url ) . "';\n";
		echo "dr_add = '"     . esc_attr( $logged_url .'add-listing' ) . "';\n";
		echo "dr_edit = '"    . esc_attr( $logged_url .'add-listing'  ) . "';\n";
		echo "dr_my = '"      . esc_attr( $logged_url .'my-listing'  ) . "';\n";
		echo "dr_credits = '" . esc_attr( $logged_url .'my-credits' ) . "';\n";
		echo "dr_signup = '"  . esc_attr(get_permalink($this->signup_page_id) ) . "';\n";
		echo "dr_signin = '"  . esc_attr(get_permalink($this->signin_page_id) ) . "';\n";
		echo "</script>\n";
	}




}

/* Initiate Class */
global $Directory_Core;
$Directory_Core = new Directory_Core_Buddypress();

endif;