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
		
		add_action( 'template_redirect', array( &$this, 'handle_page_requests' ) );

//		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );

//		add_action( 'wp_print_scripts', array( &$this, 'enqueue_scripts' ) );

	}
	
		//scans post type at template_redirect to apply custom themeing to listings
	function handle_page_requests() {
		global $wp_query;

		$taxonomy = (empty($wp_query->query_vars['taxonomy']) ) ? '' : $wp_query->query_vars['taxonomy'];
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
		elseif ( is_post_type_archive('directory_listing') ) {

			//defaults
			$templates[] = 'dr_listings.php';
			$templates[] = 'archive-listings.php';

			//if custom template exists load it
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
				$wp_query->is_page = 1;
				//$wp_query->is_404 = null;
				$wp_query->post_count = 1;

				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				$this->dr_first_thumbnail = true;
//				add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 11 );
//				add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 11 );
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
		elseif ( in_array($taxonomy, array('listing_category','listing_tag') ) ) {
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
			if ( $this->directory_template = locate_template( $templates ) ) {
				add_filter( 'template_include', array( &$this, 'custom_directory_template' ) );
			} else {
				//otherwise load the page template and use our own list theme. We don't use theme's taxonomy as not enough control
				$wp_query->is_page = 1;
				$wp_query->is_404 = null;
				$wp_query->post_count = 1;

				add_filter( 'comments_open', array( &$this, 'close_comments' ), 99 );
				add_filter( 'comments_close_text', array( &$this, 'comments_closed_text' ), 99 );
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
				$this->dr_first_thumbnail = true;
//				add_filter( 'post_thumbnail_html', array( &$this, 'delete_first_thumbnail' ) );
				add_filter( 'the_content', array( &$this, 'listing_list_theme' ), 99 );
//				add_filter( 'the_excerpt', array( &$this, 'listing_list_theme' ), 99 );
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
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
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
				add_filter( 'the_title', array( &$this, 'page_title_output' ), 10 , 2 );
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




}

/* Initiate Class */
if (!is_admin()) {
	global $Directory_Core;
	$Directory_Core = new Directory_Core_Main();
}
endif;