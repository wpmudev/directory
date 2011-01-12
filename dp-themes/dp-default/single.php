<?php
/**
 * The Template for displaying all single posts.
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						<?php dp_posted_on(); ?>
					</div><!-- .entry-meta -->
					<div class="entry-content">
                        <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
						<?php the_content(); ?>
                        <?php _e( 'Website URL', 'directory'); ?>:
                        <a href="<?php echo get_post_meta( $post->ID, '_ct_text_4ccc5fd023950', true ); ?>"><?php echo get_post_meta( $post->ID, '_ct_text_4ccc5fd023950', true ); ?></a>
                        <br /><br />
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directory' ), 'after' => '</div>' ) ); ?>

					</div><!-- .entry-content -->

<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( esc_attr__( 'About %s', 'directory' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
									<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'directory' ), get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
<?php endif; ?>

					<div class="entry-utility">
						<?php dp_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'directory' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-## -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
