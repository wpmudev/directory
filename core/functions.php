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
			'single'   => is_singular( 'directory_listing' ),
			'archive'  => is_post_type_archive( 'directory_listing' ),
			'tag'      => is_tax( 'listing_tag' ),
			'category' => is_tax( 'listing_category' ),
            'signin'   => (bool) get_query_var( 'dr_signin' ),
			'signup'   => (bool) get_query_var( 'dr_signup' )
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

