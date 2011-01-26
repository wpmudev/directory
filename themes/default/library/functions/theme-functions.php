<?php

/**
 * Prints HTML with meta information for the current post—date/time and author.
 **/
function dp_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'directory' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a><br />',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span><br />',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'directory' ), get_the_author() ),
			get_the_author()
		)
	);
}

/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 **/
function dp_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'directory' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'directory' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'directory' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}

/**
 * Display Categories on Home
 *
 * @param string $section The section where these will be loded.
 * @return string(HTML)
 **/
function dp_list_categories( $section ) {
    if ( isset( $section ) && $section == 'home' ) {
        global $wp_rewrite;
        $taxonomies = get_taxonomies( '', 'label' );
        foreach ( $taxonomies as $taxonomy => $value ) {
            if ( is_object_in_taxonomy('directory_listing', $taxonomy )  ) {
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
                    'title_li'           => "<h2>{$value->labels->name}</h2>",
                    'number'             => 10,
                    'echo'               => 1,
                    'depth'              => 1,
                    'current_category'   => 0,
                    'pad_counts'         => 1,
                    'taxonomy'           =>  $taxonomy
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
        }
    } elseif ( isset( $section ) && $section == 'sub' ) {
        global $wp_query;
        $args = array(
            'orderby'            => 'name',
            'order'              => 'ASC',
            'show_last_update'   => 0,
            'style'              => 'list',
            'show_count'         => 1,
            'hide_empty'         => 0,
            'child_of'           => $wp_query->queried_object->parent,
            'exclude_tree'       => $wp_query->queried_object->parent ,
            'hierarchical'       => true,
            'title_li'           => '', //'<h2>' . $wp_query->queried_object->name . '</h2>',
            'echo'               => 1,
            'depth'              => 2,
            'current_category'   => 0,
            'pad_counts'         => 1,
            'taxonomy'           => $wp_query->queried_object->taxonomy,
            // optional arguments:
            // 'use_desc_for_title' => 0,
            // 'show_option_all'    => ,
            // 'include'            => ,
            // 'number'             => 2,
            // 'feed'               => ,
            // 'feed_type'          => ,
            // 'feed_image'         => ,
            // 'exclude'            => ,
            // 'walker'             => 'Walker_Category'
        );
        wp_list_categories( $args );
    }
}

/**
 * Get query var parateres.
 *
 * @global $wp_query
 * @return string Query var name/slug
 **/
function dp_get_taxonomy_vars( $param ) {
    global $wp_query;

    $taxonomies = get_taxonomies( '', 'label' );

    $name = $taxonomies[$wp_query->queried_object->taxonomy]->labels->name;
    $slug = $taxonomies[$wp_query->queried_object->taxonomy]->rewrite['slug'];

    if ( $param == 'name' )
        return $name;
    if ( $param == 'slug' )
        return $slug;
}

/**
 * Filter excerpt more link.
 *
 * @global <type> $post
 * @param <type> $more
 * @return string(HTML)
 **/
function dp_new_excerpt_more( $more ) {
    global $post;
	return ' ... <br /><a class="view-listing" href="'. get_permalink($post->ID) . '">' . '{View Listing}' . '</a>';
}
add_filter('excerpt_more', 'dp_new_excerpt_more');

/**
 * Filter excerpt lenght.
 *
 * @param int Chracter size of excerpt
 * @return int Chracter size of excerpt
 **/
function dp_new_excerpt_length( $length ) {
	return 23;
}
add_filter('excerpt_length', 'dp_new_excerpt_length');


/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 **/
function dp_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'directory' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'directory' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'directory' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'directory' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'directory' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'directory'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}