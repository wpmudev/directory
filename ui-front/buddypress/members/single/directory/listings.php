<?php
global $bp, $wp_query, $post;


$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$original_query = $wp_query;

$query_args = array(
'post_type' => 'directory_listing',
'post_status' => 'publish',
'paged' => $paged,
'author' => bp_displayed_user_id(),

);

//setup taxonomy if applicable
$tax_key = (empty($wp_query->query_vars['taxonomy'])) ? '' : $wp_query->query_vars['taxonomy'];
$taxonomies = array_values(get_object_taxonomies($query_args['post_type'], 'names') );

if ( in_array($tax_key, $taxonomies) ) {
	$query_args['tax_query'] = array(
	array(
	'taxonomy' => $tax_key,
	'field' => 'slug',
	'terms' => get_query_var( $tax_key),
	)
	);
}

//The Query
$dr_query = new WP_Query( $query_args );
$dr_query->set('type_title', true);

//allows pagination links to work get_posts_nav_link()
if ( $wp_query->max_num_pages == 0){
	$wp_query->max_num_pages = $dr_query->max_num_pages;
	$wp_query->is_singular = 0;
}

//Remove the archive title filter for the individual listings
remove_filter( 'the_title', array( &$this, 'no_title' ) );

//breadcrumbs
if ( ! is_post_type_archive('directory_listing') ): ?>

<div class="breadcrumbtrail">
	<p class="page-title dp-taxonomy-name"><?php the_dr_breadcrumbs(); ?></p>
	<div class="clear"></div>
</div>
<?php endif; ?>

<?php if ( $dr_query->have_posts() ): ?>
<header class="page-header">
	<h1 class="page-title">
		<?php post_type_archive_title() ?>
	</h1>
</header>
<?php else: ?>
<div id="dr_no_listings"><?php echo apply_filters( 'dr_listing_list_none', __( 'No Listings', $this->text_domain ) ); ?></div>
<?php endif; ?>

<?php echo $this->pagination( $this->pagination_top ); ?>
<div class="clear"></div>

<?php

//Hijack the loop
if($dr_query->have_posts()):
$last = $dr_query->post_count;
$count = 1;

while( $dr_query->have_posts() ): $dr_query->the_post();

// Retrieves categories list of current post, separated by commas.
$categories_list = get_the_category_list( __(', ',$this->text_domain),'');

// Retrieves tag list of current post, separated by commas.
$tags_list = get_the_tag_list('', __(', ',$this->text_domain), '');

//add last css class for styling grids
if ( $count == $last )
$class = 'dr_listing last-listing';
else
$class = 'dr_listing';
?>
<div id="dr_listing_list">
	<div class="<?php echo $class ?>">


		<div class="entry-post">
			<h2 class="entry-title">
				<a href="<?php echo the_permalink(); ?>" title="<?php echo sprintf( esc_attr__( 'Permalink to %s', $this->text_domain ), get_the_title() ); ?>" rel="bookmark"><?php the_title();?></a>
			</h2>

			<div class="entry-meta">
				<?php the_dr_posted_on(); ?>
				<div class="entry-utility">
					<?php if ( $categories_list ): ?>
					<span class="cat-links"><?php echo sprintf( __( '<span class="%1$s">Posted in</span> %2$s', $this->text_domain ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list ); ?></span><br />
					<?php
					unset( $categories_list );
					endif;
					if ( $tags_list ): ?>
					<span class="tag-links"><?php echo sprintf ( __( '<span class="%1$s">Tagged</span> %2$s', $this->text_domain ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?></span><br />
					<?php
					unset( $tags_list );
					endif;
					do_action( 'sr_avg_ratings_of_listings', get_the_ID() ); ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a review', $this->text_domain ), __( '1 Review', $this->text_domain ), esc_attr__( '% Reviews', $this->text_domain ), '', __( 'Reviews Off', $this->text_domain ) ); ?></span>
				</div>
			</div>

			<div class="entry-summary">

				<?php
				if (has_post_thumbnail()){
					the_post_thumbnail( array(50,50),
					array(
					'class' => 'alignleft dr_listing_image_listing',
					'title' => get_the_title(),
					)
					);
				}
				//the_excerpt();
				?>
				<?php echo $this->listing_excerpt( $post->post_excerpt, $post->post_content, get_the_ID() );
				?>
			</div>
			<div class="clear"></div>
		</div>

	</div>
	<?php $count++;
	endwhile;
	//posts_nav_link();
	add_filter( 'comments_open', array( &$this, 'close_comments' ), 10 ,2 );

	//  $wp_query= $original_query;
	//	wp_reset_postdata();
	endif;
	?>
	<?php echo $this->pagination( $this->pagination_bottom ); ?>
</div>
