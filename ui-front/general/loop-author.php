<?php
/**
* The loop that displays posts.
* You can override this file in your active theme.
*
* The loop displays the posts and the post content.  See
* http://codex.wordpress.org/The_Loop to understand it and
* http://codex.wordpress.org/Template_Tags to understand
* the tags used in it.
*
* This can be overridden in child themes with loop.php or
* loop-template.php, where 'template' is the loop context
* requested by a template. For example, loop-index.php would
* be used if it exists and we ask for the loop with:
* <code>get_template_part( 'loop', 'index' );</code>
*
* @package Directory
* @subpackage Author
* @since Directory 2.2
*/

global $post, $wp_query, $query_string, $Directory_Core;

$dr = &$Directory_Core; //shorthand

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

//page for pagination
if ( 1 < get_query_var( 'dr_author_page' ) )
$dr_page = get_query_var( 'dr_author_page' );
else
$dr_page = '1';

$GLOBALS['paged'] = $dr_page;

$query_args = array(
'paged' => $paged,
'author_name' => get_query_var( 'dr_author_name' ),
'post_status' => 'publish',
'post_type' => 'directory_listing'
);

query_posts( $query_args );

?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php $dr->pagination( $dr->pagination_top ); ?>


<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
<div id="post-0" class="post error404 not-found">
	<h1 class="entry-title"><?php _e( 'Not Found', DR_TEXT_DOMAIN ); ?></h1>
	<div class="entry-content">
		<p><?php _e( 'Apologies, but no results were found for the requested listings. Perhaps searching will help find a related listing.', DR_TEXT_DOMAIN ); ?></p>
		<?php get_search_form(); ?>
	</div><!-- .entry-content -->
</div><!-- #post-0 -->
<?php endif; ?>

<?php
/* Start the Loop.
*
* In Twenty Ten we use the same loop in multiple contexts.
* It is broken into three main parts: when we're displaying
* posts that are in the gallery category, when we're displaying
* posts in the asides category, and finally all other posts.
*
* Additionally, we sometimes check for whether we are on an
* archive page, a search page, etc., allowing for small differences
* in the loop on each template without actually duplicating
* the rest of the loop that is shared.
*
* Without further ado, the loop:
*/

$last = $wp_query->post_count;
$count = 1;

// Retrieves categories list of current post, separated by commas.
$categories_list = get_the_category_list( __(', ',DR_TEXT_DOMAIN),'');

// Retrieves tag list of current post, separated by commas.
$tags_list = get_the_tag_list('', __(', ',DR_TEXT_DOMAIN), '');

//add last css class for styling grids
if ( $count == $last )
$class = 'dr_listing last-listing';
else
$class = 'dr_listing';
?>
<div id="dr_listing_list">

	<?php while ( have_posts() ) : the_post(); ?>
	<div class="<?php echo $class ?>">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="entry-post">
				<h2 class="entry-title">
					<a href="<?php echo the_permalink(); ?>" title="<?php echo sprintf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), get_the_title() ); ?>" rel="bookmark"><?php the_title();?></a>
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
						do_action( 'sr_avg_ratings_of_listings', get_the_ID() ); ?>
						<br /><span class="comments-link"><?php comments_popup_link( __( 'Leave a review', DR_TEXT_DOMAIN ), __( '1 Review', DR_TEXT_DOMAIN ), esc_attr__( '% Reviews', DR_TEXT_DOMAIN ), '', __( 'Reviews Off', DR_TEXT_DOMAIN ) ); ?></span>
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
					the_excerpt();
					?>
					<?php //echo $this->listing_excerpt( $post->post_excerpt, $post->post_content, get_the_ID() );
					?>
				</div>
				<div class="clear"></div>
			</div><!-- .entry-post -->

			<?php $count++;
			?>
		</div><!-- #post-## -->
	</div>
	<?php endwhile; ?>
</div>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php $dr->pagination( $dr->pagination_bottom ); ?>