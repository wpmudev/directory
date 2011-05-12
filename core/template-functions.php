<?php

/**
 * The following functions are meant to be used directly in template files.
 * They always echo.
 */

/* = General Template Tags
-------------------------------------------------------------- */

/**
 * the_listing_tags 
 * 
 * @param string $before 
 * @param string $sep 
 * @param string $after 
 * @access public
 * @return void
 */
function the_listing_tags( $before = '<div class="listing-tags">', $sep = ' ', $after = '</div>' ) {
	the_terms( 0, 'listing_tag', $before, $sep, $after );
}

/**
 * Display Categories on Home
 *
 * @param string $section The section where these will be loded.
 * @return string(HTML)
 **/
function the_listing_categories() {
	$args = array(
		'orderby'            => 'name',
		'order'              => 'ASC',
		'show_last_update'   => 0,
		'style'              => 'list',
		'show_count'         => 0,
		'hide_empty'         => 0,
		'use_desc_for_title' => 0,
		'child_of'           => 0,
		'hierarchical'       => true,
		'title_li'           => "<h2>Example</h2>",
		'number'             => 10,
		'echo'               => 1,
		'depth'              => 1,
		'current_category'   => 0,
		'pad_counts'         => 1,
		'taxonomy'           => 'listing_category' 
		// optional arguments:
		// 'show_option_all'    => ,
		// 'feed'               => ,
		// 'feed_type'          => ,
		// 'feed_image'         => ,
		// 'exclude'            => ,
		// 'exclude_tree'       => ,
		// 'include'            => ,
		// 'walker'             => 'Walker_Category'
	);
	wp_list_categories( $args );
}


?>
