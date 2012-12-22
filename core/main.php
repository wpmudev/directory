<?php
/**
* Directory Core Main Class
**/

if ( !class_exists('Directory_Core_Main') ):
class Directory_Core_Main extends Directory_Core {

	/**
	* Constructor.
	*
	* @return void
	**/

	function Directory_Core_Main() { __construct();}

	function __construct(){

		parent::__construct(); //Get the inheritance right

		add_action( 'init', array(&$this, 'init'));
		add_action( 'wp_enqueue_scripts', array( &$this, 'on_enqueue_scripts' ) );

		add_action( 'template_redirect', array( &$this, 'get_author_template' ));

		add_action( 'template_redirect', array( &$this, 'process_page_requests' ));
		add_action( 'template_redirect', array( &$this, 'handle_page_requests' ));
		add_action( 'wp_print_scripts', array( &$this, 'on_print_scripts' ) );

		//hide some menu pages
		add_filter( 'wp_page_menu_args', array( &$this, 'hide_menu_pages' ), 99 );

		//add menu items
		add_filter( 'wp_list_pages', array( &$this, 'filter_list_pages' ), 10, 2 );

	}

	function init(){
		global $wp, $wp_rewrite;

		parent::init();

		//Listing author rewrite rule
		$wp->add_query_var( 'dr_author_name' );
		$wp->add_query_var( 'dr_author_page' );
		$result = add_query_arg(  array(
		'dr_author_name' => '$matches[1]',
		'dr_author_page' => '$matches[3]',
		), 'index.php' );
		add_rewrite_rule( 'dr-author/(.+?)(/page/(.+?))?/?$', $result, 'top' );
		$rules = get_option( 'rewrite_rules' );
		if ( ! isset( $rules['dr-author/(.+?)(/page/(.+?))?/?$'] ) )
		$wp_rewrite->flush_rules();

	}

	/**
	* Process $_REQUEST for main pages.
	*
	* @uses set_query_var() For passing variables to pages
	* @return void|die() if "_wpnonce" is not verified
	**/
	function process_page_requests() {
		global $post;

		$options = $this->get_options('payments');

		// Handles request for my-listings page
		if(is_page($this->my_listings_page_id)){
			if ( isset( $_POST['action'] ) && 'delete_listing' ==  $_POST['action'] && wp_verify_nonce( $_POST['_wpnonce'], 'action_verify' ) ) {
				if ( $this->user_can_edit_listing( $_POST['post_id'] ) ) {
					wp_delete_post( $_POST['post_id'] );
					wp_redirect( add_query_arg( array( 'updated' => 'true', 'dmsg' => urlencode( __( 'Listing is deleted!', $this->text_domain ) ) ), get_permalink($this->my_listings_page_id) ) );
					exit;
				}
			}
		}
		// Handles request for update-listing page
		elseif(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id) ){

			if ( isset( $_POST['update_listing'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'verify' ) ) {


				$draft = $_POST['listing_data']['post_status'] == 'draft';
				$post_id = $this->update_listing( $_POST );
				$meta = new DR_Meta($post_id);

				// The credits required to renew the directory for the selected period
				$credits_required = ($this->use_free) ? 0 : $options['credits_per_listing'];

				if( ! ($draft || $meta->status == 'paid') ) {


					// If user have more credits of the required credits proceed with renewing the ad
					if ($this->is_full_access() || ($this->user_credits >= $credits_required ) ){
						if ( ! $this->is_full_access() ) {
							// Update new credits amount
							$this->transactions->credits -= $credits_required;
						} else {
							//Check one_time
							if($this->transactions->billing_type == 'one_time') $this->transactions->status = 'used';
						}

						$meta->status = 'paid';

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
						/* Set the proper step which will be loaded by "page-my-listigns.php" */
						set_query_var( 'dr_action', 'edit' );
						$error = __( 'You do not have enough credits to publish your listing. Please purchase more credits. Your Listing has been saved as a Draft.', $this->text_domain );
						set_query_var( 'dr_error', $error );
					}
				}

			}
			elseif ( isset( $_POST['cancel_listing'] ) ) {
				wp_redirect( get_permalink($this->my_listings_page_id) );
				exit;
			}
		}

	}

	/**
	* scans post type at template_redirect to apply custom theme to listings
	*
	*/
	function handle_page_requests() {
		global $wp_query;

		$templates = array();
		$taxonomy = (empty($wp_query->query_vars['taxonomy']) ) ? '' : $wp_query->query_vars['taxonomy'];

		//Check if a custom template is selected, if not or not a page, default to the one selected for the directory_listing virtual page. 
		$id = get_queried_object_id();
		if(empty($id) ) $id = $this->directory_page_id;
		$slug = get_page_template_slug($id);
		if(empty($slug) ) $page_template = get_page_template();
		else $page_template = locate_template(array($slug, 'page.php', 'index.php') );

		//load proper theme for home listing page
		if ( is_home() ) {
			$templates[] = 'home-listing.php';

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			}
		}

		//load proper theme for archive listings page
		elseif ( is_post_type_archive('directory_listing') ) {

			//defaults
			$templates[] = 'dr_listings.php';
			$templates[] = 'archive-listings.php';

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

		//load proper theme for single listing page display
		elseif ( is_single() &&  'directory_listing' == get_query_var('post_type') ) {

			//check for custom theme templates
			$listing_name = get_query_var( 'directory_listing' );
			$listing_id   = (int) $wp_query->get_queried_object_id();

			if ( $listing_name ) $templates[] = "single-listing-$listing_name.php";

			if ( $listing_id ) $templates[] = "single-listing-$listing_id.php";

			$templates[] = 'single-listing.php';



			//if custom template exists load it
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;
				$wp_query->is_single    = 0;
				$wp_query->is_page      = 1;

				//add_filter( 'the_title', array( &$this, 'delete_post_title' ), 11 ); //after wpautop
				add_filter( 'the_content', array( &$this, 'listing_content' ), 99 ); //after wpautop
			}

			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );

			$this->is_directory_page = true;
		}


		//load proper theme for listing category or tag
		elseif (is_archive() && in_array($taxonomy, array('listing_category','listing_tag') ) ) {
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
			if (! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
				$this->directory_template = $page_template;
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );

				$wp_query->post_count = 1;

				//				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				$this->dr_first_thumbnail = true;
				//				add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 10 );
				//				add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );

			$this->is_directory_page = true;
		}
		//load proper theme for my-listing listing page display
		elseif(is_page($this->my_listings_page_id)){
			/*
			if ( ! current_user_can( 'edit_published_listings' ) ) {
			wp_redirect( get_permalink($this->directory_page_id));
			exit;
			}
			*/
			$templates = array( 'page-my-listings.php' );

			//if custom template exists load it
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;
				$wp_query->is_404 = false;
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				//				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				add_filter( 'the_content', array( &$this, 'my_listings_content' ), 99 );
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
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
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;

				//				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				add_filter( 'the_content', array( &$this, 'update_listing_content' ), 99 );
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			$this->is_directory_page = true;
		}
		elseif(is_page($this->my_credits_page_id) ){
			$templates = array( 'page-my-credits.php' );
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;
				add_filter('the_content', array(&$this, 'my_credits_content'));
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
		}
		//load proper theme for signin listing page
		elseif(is_page($this->signin_page_id)){

			$templates = array( 'page-signin.php' );

			//if custom template exists load it
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;
				//fix for Infinite SEO (output buffering)
				$GLOBALS['post']->post_excerpt = 'signin';

				//otherwise load the page template and use our own theme
				$wp_query->is_single    = null;
				$wp_query->is_page      = 1;
				//				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'delete_post_title' ), 99 );
				add_filter( 'the_content', array( &$this, 'signin_content' ), 99 );
			}
			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			$this->is_directory_page = true;
		}

		//load proper theme for signup listing page
		elseif(is_page($this->signup_page_id)){

			$templates = array( 'page-signup.php' );
			//if custom template exists load it
			if ( ! $this->directory_template = locate_template( $templates ) ) {
				//otherwise load the page template and use our own theme
				$this->directory_template = $page_template;
				//fix for Infinite SEO (output buffering)
				$GLOBALS['post']->post_excerpt = 'signup';

				//				add_filter( 'comments_open', array( &$this, 'close_comments' ) );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ) );
				add_filter( 'the_title', array( &$this, 'delete_post_title' ) );
				add_filter( 'the_content', array( &$this, 'signup_content' ) );
			}

			add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			$this->is_directory_page = true;
		}


		//load  specific items
		if ( $this->is_directory_page ) {
			add_filter( 'edit_post_link', array( &$this, 'delete_edit_post_link' ), 99 );

			//prevents 404 for virtual pages
			status_header( 200 );
		}
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
			$nav .= substr( $list, $break );

			return $nav;
		}
		return $list;
	}

	/**
	* Hide some menu pages
	*/
	function hide_menu_pages( $args ) {

		$pages = (empty($args['exclude'])) ? '' : $args['exclude'];

		if ( $this->use_free ) {
			$pages .= ',' . $this->signup_page_id;
		}

		//If nothing saleable no checkout
		if ( ! $this->use_credits && ! $this->use_one_time && ! $this->use_recurring ) {
			$pages .= ',' . $this->signup_page_id;
		}

		$args['exclude'] = trim($pages,',');

		return $args;
	}

	/**
	* including JS/CSS
	**/
	function on_enqueue_scripts() {
		
		if(is_page($this->add_listing_page_id) || is_page($this->edit_listing_page_id)) {
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}
		//including CSS
		if ( file_exists( get_template_directory() . '/style-directory.css' ) )
		wp_enqueue_style( 'style-directory', get_bloginfo('template_url') . '/style-directory.css' );
		elseif ( file_exists( $this->plugin_dir . 'ui-front/general/style-directory.css' ) )
		wp_enqueue_style( 'style-directory', $this->plugin_url . 'ui-front/general/style-directory.css' );
	}

	/**
	* Print a list of javascript vars specific to Directory for use in javascript routines
	*
	*/
	function on_print_scripts(){
		echo '<script type="text/javascript">';
		echo "\nvar\n";
		echo "dr_listing = '" . esc_attr(get_permalink($this->directory_page_id) ) . "';\n";
		echo "dr_add = '" . esc_attr(get_permalink($this->add_listing_page_id) ) . "';\n";
		echo "dr_edit = '" . esc_attr(get_permalink($this->edit_listing_page_id) ) . "';\n";
		echo "dr_my = '" . esc_attr(get_permalink($this->my_listings_page_id) ) . "';\n";
		echo "dr_credits = '" . esc_attr(get_permalink($this->my_credits_page_id) ) . "';\n";
		echo "dr_signup = '" . esc_attr(get_permalink($this->signup_page_id) ) . "';\n";
		echo "dr_signin = '" . esc_attr(get_permalink($this->signin_page_id) ) . "';\n";
		echo "</script>\n";
	}

	function get_author_template(){

		$author_slug = get_query_var( 'dr_author_name' );

		if(empty($author_slug) && empty($_REQUEST['dr-author']) ) return;

		$templates = array( 'author-listings.php' );
		//if custom template exists load it
		if ( ! $this->directory_template = locate_template( $templates ) ) {
			//otherwise load the page template and use our own theme
			$this->directory_template = $this->plugin_dir . 'ui-front/general/author.php';
		}
		load_template( $this->directory_template );
		exit;

	}


}

/* Initiate Class */
if ( ! is_admin() && ! defined('BP_VERSION') ) {
	global $Directory_Core;
	$Directory_Core = new Directory_Core_Main;
}
endif;
