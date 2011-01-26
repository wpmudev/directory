<?php get_header() ?>	
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-single"><!-- start #blog-single -->
			<?php if (have_posts()) :  
					while (have_posts()) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div class="entry-meta">
										<?php dp_posted_on(); ?>
				                        <?php do_action('sr_avg_rating'); ?><br />
											<?php dp_posted_in(); ?><br />
											<?php edit_post_link( __( 'Edit', 'directory' ), '<span class="edit-link">', '</span>' ); ?><br />
											<span class="tags"><?php the_tags( __( 'Tags: ', TEMPLATE_DOMAIN ), ', ', ''); ?></span> 	<br /><span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span>
											 <?php do_action('sr_rate_this'); ?>
								</div>

								<div class="entry-post">
									<h1 class="entry-title"><?php the_title(); ?></h1>
										<div class="entry-content">
					                        <?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
											<?php the_content(); ?>

											<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directory' ), 'after' => '</div>' ) ); ?>

										</div><!-- .entry-content -->

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
						<?php endwhile; ?>
				<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
			<?php else: ?>
				<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div><!-- end #blog-single -->
	</div>
</div><!-- end #content -->
<?php get_footer() ?>