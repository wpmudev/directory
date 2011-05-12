<?php

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
	static $flags;

	if ( is_404() )
		return false;

	if ( !$flags ) {
		$flags = array(
			'single'   => is_singular( 'listing' ),
			'archive'  => is_post_type_archive( 'listings' ),
			'tag'      => is_tax( 'listing_tag' ),
			'category' => is_tax( 'listing_category' )
		);
	}

	// Check if any flags are true
	if ( empty( $type ) )
		return in_array( true, $flags );

	return isset( $flags[ $type ] ) && $flags[ $type ];
}

/**
 * get_term_parents 
 * 
 * @param mixed $term 
 * @access public
 * @return void
 */
function get_dr_term_parents( $term ) {
	static $terms = array();

	array_unshift( $terms, $term );

	if ( !empty( $term->parent ) ) 
		get_term_parents( get_term( $term->parent, $term->taxonomy ) );

	return $terms;
}

