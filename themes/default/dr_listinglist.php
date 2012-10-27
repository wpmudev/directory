<?php
/**
* The template for displaying Taxonomy pages.
*/
?>

<?php get_header() ?>

<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="breadcrumbtrail">
			<h1 class="page-title dp-taxonomy-name">
				<?php the_dr_breadcrumbs(); ?>
			</h1>
			<div class="clear"></div>
		</div>

		<?php the_dr_categories_archive(); ?>
		<div class="clear"></div><br />

		<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="entry-meta">
				<?php the_dr_posted_on(); ?>

				<div class="entry-utility">
					<?php
					// Retrieves categories list of current post, separated by commas.
					$categories = wp_get_post_terms( $post->ID, "listing_category", "" );

					foreach ( $categories as $category )
					$categories_list[] = '<a href="' . get_term_link( $category ) . '" title="' . $category->name . '" >' . $category->name . '</a>';

					$categories_list = implode( ", ", ( array ) $categories_list );

					?>

					<?php if ( $categories_list ) : ?>
					<span class="cat-links">
						<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', THEME_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list ); ?>
					</span>
					<br />
					<?php unset( $categories_list ) ?>
					<?php endif; ?>

					<?php
					// Retrieves tag list of current post, separated by commas.
					$tags = wp_get_post_terms( $post->ID, "listing_tag", "" );
					foreach ( $tags as $tag )
					$tags_list[] = '<a href="' . get_term_link( $tag ) . '" title="' . $tag->name . '" >' . $tag->name . '</a>';

					$tags_list = implode( ", ", ( array ) $tags_list );
					?>

					<?php if ( $tags_list ): ?>
					<span class="tag-links">
						<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', THEME_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
					</span>
					<br />
					<?php unset( $tags_list ) ?>
					<?php endif; ?>

					<?php do_action('sr_avg_ratings_of_listings', get_the_ID() ); ?>

					<span class="comments-link"><?php comments_popup_link( __( 'Leave a review', THEME_TEXT_DOMAIN ), __( '1 Review', THEME_TEXT_DOMAIN ), __( '% Reviews', THEME_TEXT_DOMAIN ), '',  __( 'Reviews Off', THEME_TEXT_DOMAIN ) ); ?></span>;
					<?php edit_post_link( __( 'Edit', THEME_TEXT_DOMAIN ), '<span class="edit-link">', '</span>' ); ?>

				</div><!-- .entry-utility -->
			</div><!-- .entry-meta -->

			<div class="entry-post">
				<h2 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', THEME_TEXT_DOMAIN ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h2>

				<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
				<div class="entry-summary">
					<?php if( has_post_thumbnail() ): ?>
					<?php the_post_thumbnail( array( 50, 50 ), array( 'class' => 'alignleft' )); ?>
					<?php endif; ?>
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
				<?php else : ?>
				<div class="entry-content">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', THEME_TEXT_DOMAIN ) ); ?>
					<?php //wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', THEME_TEXT_DOMAIN ), 'after' => '</div>' ) ); ?>
				</div><!-- .entry-content -->
				<?php endif; ?>

				<div class="clear"></div>
			</div>

		</div><!-- #post-## -->

		<?php //comments_template( '', true ); ?>

		<?php endwhile; ?>

		<div id="post-navigator-single">
			<div class="alignleft"><?php previous_post_link('&laquo;%link') ?></div>
			<div class="alignright"><?php next_post_link('%link&raquo;') ?></div>
		</div>

		<?php else : ?>

		<h3><?php _e("Sorry, we can't find the archive you're looking for at this URL. Please try selecting a menu item from above or to the side of this message to get where you'd like to go.", THEME_TEXT_DOMAIN); ?></h3>

		<?php endif; ?>

	</div>
</div><!-- end #content -->

<?php get_footer() ?>
