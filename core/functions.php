<?php

add_theme_support('post-thumbnails', array('directory-listing'));

/**
* The following functions abstract away various implementation details of the plugin.
* They never echo anything.
*/

/**
* Check if we're on a certain type of page
*
* @param string $type The type of page
* @return bool
*/
function is_dr_page( $type = '' ) {
	global $wp_query;
	static $flags;

	if ( is_404() )
	return false;
	
	if(property_exists($wp_query, 'post_type')){
	  $is_dr = (is_array($wp_query->post_type)) ? in_array('directory_listing', $wp_query->post_type) : 'directory_listing' == $wp_query->post_type;
	} else {
		$is_dr = false;
	}
	
	
	if ( !$flags ) {
		$flags = array(
		'single'   => is_singular( 'directory_listing' ),
		'archive'  => is_post_type_archive() && $is_dr,
		'tag'      => is_tax( 'listing_tag' ),
		'category' => is_tax( 'listing_category' ),
		'signin'   => is_page( 'signin'),
		'signup'   => is_page( 'signup')
		);
	}

	// Check if any flags are true
	if ( empty( $type ) )
	return in_array( true, $flags );

	return isset( $flags[ $type ] ) && $flags[ $type ];
}

/**
* Get array of term objects parents of the term passed
*
* @param mixed $term
* @access public
* @return array Array of term objects ordered in a hierarchical way including
* the term passed as an argument
*/
function get_dr_term_parents( $term ) {
	static $terms = array();

	array_unshift( $terms, $term );

	if ( !empty( $term->parent ) )
	get_term_parents( get_term( $term->parent, $term->taxonomy ) );

	return $terms;
}


/**
* Get array of term objects parents of the term passed
*
* @param mixed $term
* @access public
* @return array Array of term objects ordered in a hierarchical way including
* the term passed as an argument
*/
function get_dr_title( $id = 0 ) {
	$post   = &get_post( $id );
	$title  = isset( $post->post_title ) ? $post->post_title : '';
	return $title;
}

/**
* Does a Directory listing support a given taxonomy
* @return bool
*/
function dr_supports_taxonomy($taxonomy=''){
	global $wp_taxonomies;
	if(empty($taxonomy)) return false;
	return (is_array($wp_taxonomies[$taxonomy]->object_type)) ? in_array('directory_listing', $wp_taxonomies[$taxonomy]->object_type) : false;
}
