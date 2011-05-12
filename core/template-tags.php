<?php

/**
 * The following functions are meant to be used directly in template files.
 * They always echo.
 */

/* = General Template Tags
-------------------------------------------------------------- */

function the_dir_categories_home() {
	$args = array(
		'orderby'      => 'name',
		'order'        => 'ASC',
		'hide_empty'   => 0,
		'hierarchical' => 1,
		'number'       => 10,
		'taxonomy'     => 'listing_category',
		'pad_counts'   => 1 
	);

	$categories = get_categories( $args );  

	$output = '<ul>';

	foreach( $categories as $category ) { 		

		$output .= '<li>';
		$output .= '<h2><a href="' . get_term_link( $category ) . '" title="' . sprintf( __( 'View all posts in %s', DP_TEXT_DOMAIN ), $category->name ) . '" >' . $category->name . '</a> </h2>';
		// $output .= '<p> Description:'. $category->description . '</p>';
		// $output .= '<p> Post Count: '. $category->count . '</p>';  

		$args = array(
			'type'         => 'post',
			'parent'       => $category->term_id,
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'number'       => 10,
			'taxonomy'     => 'listing_category',
			'pad_counts'   => 1 
		);

		$sub_categories = get_categories( $args );

		foreach ( $sub_categories as $sub_category ) {

			$output .= '<a href="' . get_term_link( $sub_category ) . '" title="' . sprintf( __( 'View all posts in %s', DP_TEXT_DOMAIN ), $sub_category->name ) . '" ' . '>' . $sub_category->name.'</a>(' . $sub_category->count . ') ';
		}

		$output .= '</li>';
	}

	$output .= '</ul>';

	echo $output;
}

function the_dir_categories_archive() {
	$args = array(
		'orderby'      => 'name',
		'order'        => 'ASC',
		'hide_empty'   => 0,
		'hierarchical' => 1,
		'number'       => 10,
		'taxonomy'     => 'listing_category',
		'pad_counts'   => 1 
	);

	$categories = get_categories( $args );  

	$output = '<ul>';

	foreach( $categories as $category ) { 		

		$output .= '<li>';
		$output .= '<h2><a href="' . get_term_link( $category ) . '" title="' . sprintf( __( 'View all posts in %s', DP_TEXT_DOMAIN ), $category->name ) . '" >' . $category->name . '</a> </h2>';
		// $output .= '<p> Description:'. $category->description . '</p>';
		// $output .= '<p> Post Count: '. $category->count . '</p>';  

		$output .= '</li>';
	}

	$output .= '</ul>';

	echo $output;
}
