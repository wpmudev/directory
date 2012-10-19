<?php

/**
* The following functions are meant to be used directly in template files.
* They always echo.
*/

/* = General Template Tags
-------------------------------------------------------------- */

/**
* the_dir_categories_home
*
* @access public
* @return void
*/
function the_dr_categories_home( $echo = true ) {
	//get plugin options
	$options  = get_option( DR_OPTIONS_NAME );

	$cat_num                = ( isset( $options['general']['count_cat'] ) && is_numeric( $options['general']['count_cat'] ) && 0 < $options['general']['count_cat'] ) ? $options['general']['count_cat'] : 10;
	$sub_cat_num            = ( isset( $options['general']['count_sub_cat'] ) && is_numeric( $options['general']['count_sub_cat'] ) && 0 < $options['general']['count_sub_cat'] ) ? $options['general']['count_sub_cat'] : 5;
	$hide_empty_sub_cat     = ( isset( $options['general']['hide_empty_sub_cat'] ) && is_numeric( $options['general']['hide_empty_sub_cat'] ) && 0 < $options['general']['hide_empty_sub_cat'] ) ? $options['general']['hide_empty_sub_cat'] : 0;

	$taxonomies = array_values(get_taxonomies(array('object_type' => array('directory_listing'), 'hierarchical' => 1)));

	$args = array(
	'parent'       => 0,
	'orderby'      => 'name',
	'order'        => 'ASC',
	'hide_empty'   => 0,
	'hierarchical' => 1,
	'number'       => $cat_num,
	'taxonomy'     => $taxonomies,
	'pad_counts'   => 1
	);

	$categories = get_categories( $args );

	$output = '<div id="dr_list_categories" class="dr_list_categories" >' . "\n";
	$output .= "<ul>\n";

	foreach( $categories as $category ) {
		$count_items = 0;

		$output .= "<li>\n";
		$output .= '<h2><a href="' . get_term_link( $category ) . '" title="' . __( 'View all posts in ', DR_TEXT_DOMAIN ) . $category->name . '" >' . $category->name . "</a> </h2>\n";

		$args = array(
		'parent'       => $category->term_id,
		'orderby'      => 'name',
		'order'        => 'ASC',
		'hide_empty'   => $hide_empty_sub_cat,
		'hierarchical' => 1,
		'number'       => $sub_cat_num,
		'taxonomy'     => $category->taxonomy,
		'pad_counts'   => 1
		);

		$sub_categories = get_categories( $args );

		foreach ( $sub_categories as $sub_category ) {
			$output .= '<div class="term"><a href="' . get_term_link( $sub_category ) . '" title="' . __( 'View all posts in ', DR_TEXT_DOMAIN ) . $sub_category->name . '" >' . $sub_category->name . "</a> <span>({$sub_category->count})</span></div>\n";
			$count_items++;
		}

		if ( isset( $options['general']['display_listing'] ) && '1' == $options['general']['display_listing']  )
		{
			if ( $sub_cat_num > $count_items ) {
				$args = array(
				'numberposts'       => $sub_cat_num - $count_items,
				'post_type'         => 'directory_listing',
				'category'  => $category->slug,
				);

				$my_posts = get_posts( $args );

				foreach( $my_posts as $post ) {
					$output .= '<a href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '" >' . $post->post_title . "</a><br />\n";
					$count_items++;
					if ( $sub_cat_num < $count_items ) break;
				}
			}
		}
		$output .= "</li>\n";
	}

	$output .= "</ul>\n";
	$output .= "</div>\n";
	$output .= '<div class="clear"></div>' . "\n";

	if ( $echo )
	echo $output;
	else
	return $output;
}

/**
* the_dir_categories_archive
*
* @access public
* @return void
*/
function the_dr_categories_archive() {

	//get related taxonomies
	$taxonomies = array_values( get_taxonomies(array('object_type' => array('directory_listing'), 'hierarchical' => 1 ) ) );
	$args = array(
	'parent'       => get_queried_object_id(),
	'orderby'      => 'name',
	'order'        => 'ASC',
	'hide_empty'   => 0,
	'hierarchical' => 1,
	'number'       => 10,
	'taxonomy'     => $taxonomies,
	'pad_counts'   => 1
	);

	$categories = get_categories( $args );

	$i = 1;
	$output = '<table><tr><td>';

	foreach( $categories as $category ) {

		$output .= '<a href="' . get_term_link( $category ) . '" title="' . sprintf( __( 'View all posts in %s', DR_TEXT_DOMAIN ), $category->name ) . '" >' . $category->name . '</a> (' . $category->count . ') <br />';

		if ( $i % 5 == 0 )
		$output .= '</td><td>';

		$i++;
	}

	$output .= '</td></tr></table>';

	echo $output;
}

/**
* the_dir_breadcrumbs
*
* @access public
* @return void
*/
function the_dr_breadcrumbs() {
	$output = '';
	$category = get_queried_object();
	$category_parent_ids = get_ancestors( $category->term_id, $category->taxonomy );
	$category_parent_ids = array_reverse( $category_parent_ids );

	foreach ( $category_parent_ids as $category_parent_id ) {
		$category_parent = get_term( $category_parent_id, $category->taxonomy );

		$output .= '<a href="' . get_term_link( $category_parent ) . '" title="' . sprintf( __( 'View all posts in %s', DR_TEXT_DOMAIN ), $category_parent->name ) . '" >' . $category_parent->name . '</a> / ';
	}

	$output .= '<a href="' . get_term_link( $category ) . '" title="' . sprintf( __( 'View all posts in %s', DR_TEXT_DOMAIN ), $category->name ) . '" >' . $category->name . '</a>';

	echo $output;
}

/**
* Prints HTML with meta information for the current post—date/time and author.
*
* @access public
* @return void
*/
function the_dr_posted_on() {
  global $bp;

	// For BuddyPress compatibility
	$alink = ( isset( $bp ) ) ? bp_core_get_user_domain( get_the_author_meta('ID') ) . 'listings/' : get_author_posts_url( get_the_author_meta( 'ID' ) ) ;

	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', DR_TEXT_DOMAIN ),
	'meta-prep meta-prep-author',
	sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
	get_permalink(),
	esc_attr( get_the_time() ),
	get_the_date()
	),
	sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
	$alink,
	sprintf( esc_attr__( 'View all posts by %s', DR_TEXT_DOMAIN ), get_the_author() ),
	get_the_author()
	)
	);
}

/**
* Prints HTML with meta information for the current post (category, tags and permalink).
*
* @access public
* @return void
*/
function the_dr_posted_in() {
	global $post;

	$categories_list = get_the_category_list( __(', ',DR_TEXT_DOMAIN));

	$tag_list = get_the_tag_list('', __(', ',DR_TEXT_DOMAIN), '');

	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', DR_TEXT_DOMAIN );
	} elseif ( $categories_list ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', DR_TEXT_DOMAIN );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', DR_TEXT_DOMAIN );
	}

	// Prints the string, replacing the placeholders.
	printf(
	$posted_in,
	$categories_list,
	$tag_list,
	get_permalink(),
	the_title_attribute( 'echo=0' )
	);
}

/**
* Template for comments and pingbacks.
*
* Used as a callback by wp_list_comments() for displaying the comments.
*/
function the_dr_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :
	case '' : ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">

			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, 40 ); ?>
				<?php printf( __( '%s <span class="says">says:</span>', DR_TEXT_DOMAIN ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			</div><!-- .comment-author .vcard -->

			<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your review is awaiting moderation.', DR_TEXT_DOMAIN ); ?></em>
			<br />
			<?php endif; ?>

			<div class="comment-meta commentmetadata">
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
					<?php printf( __( '%1$s at %2$s', DR_TEXT_DOMAIN ), get_comment_date(),  get_comment_time() ); ?>
				</a><?php edit_comment_link( __( '(Edit)', DR_TEXT_DOMAIN ), ' ' ); ?>
			</div><!-- .comment-meta .commentmetadata -->

			<?php do_action('sr_user_rating', $comment->user_id); ?>

			<div class="comment-body"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->

		</div><!-- #comment-##  -->
	</li>

	<?php break;

	case 'pingback'  :
	case 'trackback' : ?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', DR_TEXT_DOMAIN ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', DR_TEXT_DOMAIN), ' ' ); ?></p>
	</li>
	<?php
	break;
	endswitch;
}
