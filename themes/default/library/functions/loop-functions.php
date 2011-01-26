<?php
function wpmu_blogpageloop() {
		do_action('wpmu_blogpageloop');
}

function wpmu_excerptloop() {
		do_action('wpmu_excerptloop');
}

function wpmu_singleloop() {
		do_action('wpmu_singleloop');
}

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
							<br />
							<div class="entry-utility">
								<?php if ( count( get_the_category() ) ) : ?>
									<span class="cat-links">
										<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'directory' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
									</span>
									<br />
								<?php endif; ?>
								<?php
									$tags_list = get_the_tag_list( '', ', ' );
									if ( $tags_list ):
								?>
									<span class="tag-links">
										<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'directory' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
									</span>
									<br />
								<?php endif; ?>
								<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'directory' ), __( '1 Comment', 'directory' ), __( '% Comments', 'directory' ) ); ?></span><br />
								<?php edit_post_link( __( 'Edit', 'directory' ), '<span class="edit-link">', '</span>' ); ?>
							</div><!-- .entry-utility -->
						</div><!-- .entry-meta -->
						<div class="entry-post">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'directory' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>


			<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
					<div class="entry-summary">
		                <?php the_post_thumbnail( array( 50, 50 ), array( 'class' => 'alignleft' )); ?>

						<?php the_excerpt(); ?>
					</div><!-- .entry-summary -->
			<?php else : ?>
					<div class="entry-content">

						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'directory' ) ); ?>
						<?php //wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directory' ), 'after' => '</div>' ) ); ?>
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
							<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'directory' ) ); ?></div>
							<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'directory' ) ); ?></div>
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
				<span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', TEMPLATE_DOMAIN ) ?> <?php the_category(', ') ?><br /> <em><?php _e( 'by ', TEMPLATE_DOMAIN ) ?><?php the_author_link();  ?></em></span><br />
				<span class="tags"><?php the_tags( __( 'Tags: ', TEMPLATE_DOMAIN ), ', ', '<br />'); ?></span> <br /><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span>
			</div>
			<div class="entry-post">
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', TEMPLATE_DOMAIN ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<div class="entry">
							<a href="<?php the_permalink() ?>">
								<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
								<?php the_post_thumbnail(); ?></div><?php } } ?>
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
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-content-wp">
					<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', TEMPLATE_DOMAIN ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					<p class="date"><span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', TEMPLATE_DOMAIN ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', TEMPLATE_DOMAIN ) ?><?php the_author_link();  ?></em></span></p>
						<div class="entry">
									<a href="<?php the_permalink() ?>">
										<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
										<?php the_post_thumbnail(); ?></div><?php } } ?>
									</a>
							<?php the_excerpt(); ?>
						</div>
				<p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', TEMPLATE_DOMAIN ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span></p>
			</div>
		</div>
			<?php endwhile;
}
add_action('wpmu_excerptloop', 'wpmu_excerpt_loop');

/* single function */
function wpmu_single_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-meta">
							<?php dp_posted_on(); ?><br />
	                        <?php do_action('sr_avg_rating'); ?><br />
								<?php dp_posted_in(); ?><br />
								<?php edit_post_link( __( 'Edit', 'directory' ), '<span class="edit-link">', '</span>' ); ?><br />
								<span class="tags"><?php the_tags( __( 'Tags: ', TEMPLATE_DOMAIN ), ', ', '<br />'); ?></span> <br /><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span>
					</div>
					
					<div class="entry-post">
						<h1 class="entry-title"><?php the_title(); ?></h1>
							<div class="entry-content">
		                        <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
								<?php the_content(); ?>
		                  
								<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directory' ), 'after' => '</div>' ) ); ?>

							</div><!-- .entry-content -->
							  <?php do_action('sr_rate_this'); ?>
										<div class="clear"></div>
					</div>
				
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
				</div>
			<div id="commentbox">
			<?php comments_template('', true); ?>
			</div>
			<?php endwhile;
		
}
add_action('wpmu_singleloop', 'wpmu_single_loop');

/* page function */
function wpmu_page_loop(){
		rewind_posts();
		while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
						<h2 class="pagetitle"><?php the_title(); ?></h2>
					<div class="entry">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', TEMPLATE_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
						<?php edit_post_link( __( 'Edit this entry.', TEMPLATE_DOMAIN ), '<p>', '</p>'); ?>
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

						<?php the_content( __('<p class="serif">Read the rest of this entry &rarr;</p>', TEMPLATE_DOMAIN ) ); ?>

						<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', TEMPLATE_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>

				</div>
					<div id="commentbox">
					<?php comments_template('', true); ?>
					</div>
				<?php endwhile;
}
add_action('wpmu_attachmentloop', 'wpmu_attachment_loop');
?>