<?php

/** @todo Ugly stuff around here, rafactor */

function wpmu_blogpageloop() {
		do_action('wpmu_blogpageloop');
}

function wpmu_excerptloop() {
		do_action('wpmu_excerptloop');
}

/*function wpmu_singleloop() {
		do_action('wpmu_singleloop');
}*/

function wpmu_pageloop() {
		do_action('wpmu_pageloop');
}

function wpmu_attachmentloop() {
		do_action('wpmu_attachmentloop');
}

function wpmu_directoryloop() {
		do_action('wpmu_directoryloop');
}

/* blog / news template function */
function wpmu_directory_loop(){
		rewind_posts();
		
		global $wp_query;
		while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-meta">
							<?php dp_posted_on(); ?>
							<div class="entry-utility">
								<?php if ( count( get_the_category() ) ) : ?>
									<span class="cat-links">
										<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', THEME_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
									</span>
								<?php endif; ?>
								<?php
									$tags_list = get_the_tag_list( '', ', ' );
									if ( $tags_list ):
								?>
									<span class="tag-links">
										<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', THEME_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
									</span>
								<?php endif; ?>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a review', THEME_TEXT_DOMAIN ), __( '1 Review', THEME_TEXT_DOMAIN ), __( '% Reviews', THEME_TEXT_DOMAIN ), __( 'Reviews Off', THEME_TEXT_DOMAIN ) ); ?></span>;
								<?php edit_post_link( __( 'Edit', THEME_TEXT_DOMAIN ), '<span class="edit-link">', '</span>' ); ?>
							</div><!-- .entry-utility -->
						</div><!-- .entry-meta -->
						<div class="entry-post">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', THEME_TEXT_DOMAIN ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>


			<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
					<div class="entry-summary">
		                <?php the_post_thumbnail( array( 50, 50 ), array( 'class' => 'alignleft' )); ?>

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

		<?php endwhile; // End the loop. Whew. ?>

		<?php /* Display navigation to next/previous pages when applicable */ ?>
		<?php if (  $wp_query->max_num_pages > 1 ) : ?>
						<div id="nav-below" class="navigation">
							<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', THEME_TEXT_DOMAIN ) ); ?></div>
							<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', THEME_TEXT_DOMAIN ) ); ?></div>
						</div><!-- #nav-below -->
		<?php endif; 
}
add_action('wpmu_directoryloop', 'wpmu_directory_loop');

/* blog / news template function */
function wpmu_blogpage_loop(){
		rewind_posts();
		$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=5&paged=$page"); while ( have_posts() ) : the_post();?>
		<div class="excerptpost" id="post-<?php the_ID(); ?>">
			<div class="entry-meta">
				<span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', THEME_TEXT_DOMAIN ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', THEME_TEXT_DOMAIN ) ?><?php the_author_link();  ?></em></span>
				<span class="tags"><?php the_tags( __( 'Tags: ', THEME_TEXT_DOMAIN ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Reviews &#187;', THEME_TEXT_DOMAIN ), __( '1 Review &#187;', THEME_TEXT_DOMAIN ), __( '% Reviews &#187;', THEME_TEXT_DOMAIN ) ); ?></span>
			</div>
			<div class="entry-post">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', THEME_TEXT_DOMAIN ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry">
							<a href="<?php the_permalink() ?>">
								  <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
						
							</a>
					<?php the_excerpt(); ?>
					<div class="clear-left"></div>
				</div>
			</div>
					<div class="clear"></div>
		</div>
			<?php endwhile;
}
add_action('wpmu_blogpageloop', 'wpmu_blogpage_loop');

/* excerpt function */
function wpmu_excerpt_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
			<div class="excerptpost" id="post-<?php the_ID(); ?>">
				<div class="entry-meta">
					<span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', THEME_TEXT_DOMAIN ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', THEME_TEXT_DOMAIN ) ?><?php the_author_link();  ?></em></span>
					<span class="tags"><?php the_tags( __( 'Tags: ', THEME_TEXT_DOMAIN ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Reviews &#187;', THEME_TEXT_DOMAIN ), __( '1 Review &#187;', THEME_TEXT_DOMAIN ), __( '% Reviews &#187;', THEME_TEXT_DOMAIN ) ); ?></span>
				</div>
				<div class="entry-post">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', THEME_TEXT_DOMAIN ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry">
								<a href="<?php the_permalink() ?>">
									  <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>

								</a>
						<?php the_excerpt(); ?>
						<div class="clear-left"></div>
					</div>
				</div>
						<div class="clear"></div>
			</div>
			<?php endwhile;
}
add_action('wpmu_excerptloop', 'wpmu_excerpt_loop');

/* page function */
function wpmu_page_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
						<h2 class="pagetitle"><?php the_title(); ?></h2>
					<div class="entry">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', THEME_TEXT_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
						<?php edit_post_link( __( 'Edit this entry.', THEME_TEXT_DOMAIN ), '<p>', '</p>'); ?>
					</div>
				</div>
				
					<div id="commentbox">
					<?php comments_template('', true); ?>
					</div>
			<?php endwhile;
}
add_action('wpmu_pageloop', 'wpmu_page_loop');

function wpmu_attachment_loop(){
		rewind_posts();
			while (have_posts()) : the_post(); 
	$attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line 
			$_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>

				<div class="post" id="post-<?php the_ID(); ?>">

					<h2 class="posttitle"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &rarr; <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>

					<div class="entry">
						<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>

						<?php the_content( __('<p class="serif">Read the rest of this entry &rarr;</p>', THEME_TEXT_DOMAIN ) ); ?>

						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', THEME_TEXT_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>

				</div>
					<div id="commentbox">
					<?php comments_template('', true); ?>
					</div>
				<?php endwhile;
}
add_action('wpmu_attachmentloop', 'wpmu_attachment_loop');
?>
