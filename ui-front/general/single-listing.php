<?php

?>
<div class="entry-post">
	<h1 class="entry-title"><?php echo get_dr_title( $post->ID ); ?></h1>
	<div class="entry-content">
		<?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>
		<?php
		$content = get_the_content( null, 0 );
		$content = str_replace( ']]>', ']]&gt;', $content );
		echo $content;
		?>

		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', DR_TEXT_DOMAIN ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<div class="clear"></div>
<div class="custom-fields">
	<?php $this->display_custom_fields_values(); ?>
</div>
</div>

<div class="entry-meta">
	<?php the_dr_posted_on(); ?>
	<?php do_action('sr_avg_rating'); ?><br />
	<p><span class="comments"><?php comments_popup_link( __( 'No Reviews &#187;', DR_TEXT_DOMAIN ), __( '1 Review &#187;', DR_TEXT_DOMAIN ), __( '% Reviews &#187;', DR_TEXT_DOMAIN ) ); ?></span></p>
	<?php the_dr_posted_in(); ?>

	<?php if ( !is_user_logged_in() ) : ?>
	<?php echo '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to rate item.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>'; ?>
	<?php else: ?>
	<?php do_action('sr_rate_this'); ?>
	<?php endif; ?>
</div>
<div class="clear"></div>
<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
<div id="entry-author-info">
	<div id="author-avatar">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), 60 ); ?>
	</div><!-- #author-avatar -->
	<div id="author-description">
		<h2><?php printf( esc_attr__( 'About %s', DR_TEXT_DOMAIN ), get_the_author() ); ?></h2>
		<?php the_author_meta( 'description' ); ?>
		<div id="author-link">
			<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
				<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', DR_TEXT_DOMAIN ), get_the_author() ); ?>
			</a>
		</div><!-- #author-link    -->
	</div><!-- #author-description -->
</div><!-- #entry-author-info -->
<?php endif; ?>

