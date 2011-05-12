<?php get_header(); ?>

		<div id="listing-container">
			<div id="listing-content" role="main">

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="listing-nav-above" class="listing-navigation">
					<div class="listing-nav-previous"><?php previous_post_link( '%link', '<span class="listing-meta-nav">' . _x( '&larr;', 'Previous post link', DP_TEXTDOMAIN ) . '</span> %title' ); ?></div>
					<div class="listing-nav-next"><?php next_post_link( '%link', '%title <span class="listing-meta-nav">' . _x( '&rarr;', 'Next post link', DP_TEXTDOMAIN ) . '</span>' ); ?></div>
				</div><!-- #listing-nav-above -->

				<div id="listing-post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="listing-entry-title"><?php the_title(); ?></h1>

					<div class="listing-entry-meta">
						<?php // twentyten_posted_on(); ?>
					</div><!-- .listing-entry-meta -->

					<div class="listing-entry-content">
						<?php the_content(); ?>
						<?php the_listing_tags(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', DP_TEXTDOMAIN ), 'after' => '</div>' ) ); ?>
					</div><!-- .listing-entry-content -->


					<?php if ( get_the_author_meta( 'description' ) ) : 
					// If a user has filled out their description, show a bio on their entries  ?>

					<div id="listing-entry-author-info">
						<div id="listing-author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="listing-author-description">
							<h2><?php printf( esc_attr__( 'About %s', DP_TEXTDOMAIN ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="listing-author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
									<?php printf( __( 'View all posts by %s <span class="listing-meta-nav">&rarr;</span>', DP_TEXTDOMAIN ), get_the_author() ); ?>
								</a>
							</div><!-- #listing-author-link	-->
						</div><!-- #listing-author-description -->
					</div><!-- #listing-entry-author-info -->

					<?php endif; ?>

					<div class="listing-entry-utility">
						<?php // twentyten_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', DP_TEXTDOMAIN ), '<span class="listing-edit-link">', '</span>' ); ?>
					</div><!-- .listing-entry-utility -->

				</div><!-- #post-## -->

				<div id="listing-nav-below" class="listing-navigation">
					<div class="listing-nav-previous"><?php previous_post_link( '%link', '<span class="listing-meta-nav">' . _x( '&larr;', 'Previous post link', DP_TEXTDOMAIN ) . '</span> %title' ); ?></div>
					<div class="listing-nav-next"><?php next_post_link( '%link', '%title <span class="listing-meta-nav">' . _x( '&rarr;', 'Next post link', DP_TEXTDOMAIN ) . '</span>' ); ?></div>
				</div><!-- #listing-nav-below -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

			</div><!-- #listing-content -->
		</div><!-- #listing-container -->

<?php get_footer(); ?>
