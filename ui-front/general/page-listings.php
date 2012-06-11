<?php
global $wp_query, $post;

$page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$query_args = array(
'post_type' => 'directory_listing',
'post_status' => 'publish',
'paged' => $page,
);

//The Query
$custom_query = new WP_Query( $query_args );

//allows pagination links to work get_posts_nav_link()
if ( $wp_query->max_num_pages == 0 || $taxonomy_query ){
	$wp_query->max_num_pages = $custom_query->max_num_pages;
	$wp_query->is_singular = 0;
}

//breadcrumbs
if ( !is_dr_page( 'archive' ) ): ?>



<div class="breadcrumbtrail">
	<p class="page-title dp-taxonomy-name"><?php the_dr_breadcrumbs(); ?></p>
	<div class="clear"></div>
</div>
<?php endif; ?>

<div id="dr_listing_list">
	<?php
	//Hijack the loop
	if(is_array($custom_query->posts)) :
	$last = count( $custom_query->posts );
	$count = 1;
	foreach ( $custom_query->posts as $the_post ) :
	//Set Global post
	$post = $the_post;
	setup_postdata($post);

	// Retrieves categories list of current post, separated by commas.
	$categories_list = get_the_category_list( __(', ',DR_TEXT_DOMAIN));

	// Retrieves tag list of current post, separated by commas.
	$tags_list = get_the_tag_list('', __(', ',DR_TEXT_DOMAIN), '');

	//add last css class for styling grids
	if ( $count == $last )
	$class = 'dr_listing last-listing';
	else
	$class = 'dr_listing';
	?>
	<div class="<?php echo $class ?>">


		<div class="entry-post">
			<h2 class="entry-title">
				<a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php sprintf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), $post->post_title ); ?>" rel="bookmark"><?php echo $post->post_title;?></a>
			</h2>

			<div class="entry-meta">
				<?php the_dr_posted_on(); ?>
				<div class="entry-utility">
					<?php if ( $categories_list ): ?>
					<span class="cat-links"><?php echo sprintf( __( '<span class="%1$s">Posted in</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list ); ?></span><br />
					<?php
					unset( $categories_list );
					endif;
					if ( $tags_list ): ?>
					<span class="tag-links"><?php echo sprintf ( __( '<span class="%1$s">Tagged</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?></span><br />
					<?php
					unset( $tags_list );
					endif;
					do_action( 'sr_avg_ratings_of_listings', $post->ID); ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a review', DR_TEXT_DOMAIN ), __( '1 Review', DR_TEXT_DOMAIN ), __( '% Reviews', DR_TEXT_DOMAIN ), '', __( 'Reviews Off', DR_TEXT_DOMAIN ) ); ?></span>
				</div>
			</div>
			<div class="clear_left"></div>

			<div class="entry-summary">
				<?php echo get_the_post_thumbnail( $post->ID, array( 50, 50 ), array( 'class' => 'alignleft dr_listing_image_listing', 'title' => $post->post_title  ) ); ?>
				<?php echo $this->listing_excerpt( $post->post_excerpt, $post->post_content, $post->ID ); ?>
			</div>
		<div class="clear"></div>
		</div>


	</div>
	<?php $count++;
	endforeach;
	posts_nav_link();

	else:?>
	<div id="dr_no_listings"><?php echo apply_filters( 'dr_listing_list_none', __( 'No Listings', DR_TEXT_DOMAIN ) ); ?></div>
	<?php endif; ?>
</div>


