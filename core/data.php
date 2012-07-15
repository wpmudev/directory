<?php

/**
* Load core DB data.
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
	}

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
			'rewrite'       => array( 'slug' => 'listings-tag', 'with_front' => false, 'hierarchical' => false ),
			'capabilities'  => array( 'assign_terms' => 'assign_terms' ),
			'hierarchical' => false,
			'query_var' => true,
			'public' => true,
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
			'separate_items_with_commas' => __( 'Separate listing tags with commas', DR_TEXT_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove listing tags', DR_TEXT_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used listing tags', DR_TEXT_DOMAIN ),
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
			'rewrite'       => array( 'slug' => 'listings-category', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'  => array( 'assign_terms' => 'edit_published_listings' ),
			'hierarchical'  => true,
			'query_var' => true,
			'public' => true,
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
			'parent_item_colon'        => __( 'Parent Category:', DR_TEXT_DOMAIN ),
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

			// Update post types and delete tmp options
			flush_network_rewrite_rules();
		}

		//add rule for show dr-author page
		global $wp, $wp_rewrite;

		if ( class_exists( 'BP_Core' ) ) {
			$wp->add_query_var( 'dr_author_page' );
			$wp->add_query_var( 'dr_current_component' );
			$result = add_query_arg(  array(
			'dr_author_page'        => '$matches[3]',
			'dr_current_component'  => 'classifieds'
			), 'index.php' );
			//            add_rewrite_rule( 'members/admin/classifieds(/page/(.+?))?/?$', $result, 'top' );
			add_rewrite_rule( 'members/(.+?)/classifieds(/page/(.+?))?/?$', $result, 'top' );
			$rules = get_option( 'rewrite_rules' );
			if ( ! isset( $rules['members/(.+?)/classifieds(/page/(.+?))?/?$'] ) )
			//            if ( ! isset( $rules['members/admin/classifieds(/page/(.+?))?/?$'] ) )
			$wp_rewrite->flush_rules();
		} else {
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
	}
}

endif;