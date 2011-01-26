<?php
	if ( post_password_required() ) :
		echo '<h3 class="comments-header">' . __('Password Protected', TEMPLATE_DOMAIN) . '</h3>';
		echo '<p class="alert password-protected">' . __('Enter the password to view comments.', TEMPLATE_DOMAIN) . '</p>';
		return;
	endif;
	if ( is_page() && !have_comments() && !comments_open() && !pings_open() )
		return;
?>

<?php if ( have_comments() ) : ?>
	<div id="comments">
		<?php
		$numTrackBacks = 0; $numComments = 0;
		foreach ( (array)$comments as $comment ) if ( get_comment_type() != "comment") $numTrackBacks++; else $numComments++;
		?>
		<h3 id="comments"><?php comments_number( '', '', $numComments );?></h3>
		<ol class="commentlist">
					<?php wp_list_comments(); ?>
		</ol><!-- .comment-list -->
		<?php if ( get_option( 'page_comments' ) ) : ?>
			<div class="comment-navigation paged-navigation">
				<?php paginate_comments_links(); ?>
			</div>
		<?php endif; ?>
	</div><!-- #comments -->
<?php else : ?>
	<?php if ( pings_open() && !comments_open() && is_single() ) : ?>
		<p class="comments-closed pings-open">
			<?php printf( __('Comments are closed, but <a href="%1$s" title="Trackback URL for this post">trackbacks</a> and pingbacks are open.', TEMPLATE_DOMAIN), trackback_url( '0' ) ); ?>
		</p>
	<?php elseif ( !comments_open() && is_single() ) : ?>
		<p class="comments-closed">
			<?php _e('Comments are closed.', TEMPLATE_DOMAIN); ?>
		</p>
	<?php endif; ?>
<?php endif; ?>
	<?php if ( comments_open() ) : ?>
	<div id="respond">
		
							<div class="comment-content-wp">
		
			<h3 id="reply" class="comments-header">
				<?php comment_form_title( __( 'Leave a Reply', TEMPLATE_DOMAIN ), __( 'Leave a Reply to %s', TEMPLATE_DOMAIN ), true ); ?>
			</h3>
			<p id="cancel-comment-reply">
				<?php cancel_comment_reply_link( __( 'Click here to cancel reply.', TEMPLATE_DOMAIN ) ); ?>
			</p>
			<?php if ( get_option( 'comment_registration' ) && !$user_ID ) : ?>
				<p class="alert">
					<?php printf( __('You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', TEMPLATE_DOMAIN), wp_login_url( get_permalink() ) ); ?>
				</p>
			<?php else : ?>

				<form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform" class="standard-form">
					<?php if ( $user_ID ) : ?>
						<p class="log-in-out">
													<?php printf(__('Logged in as %s.', TEMPLATE_DOMAIN), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <?php $mywp_version = get_bloginfo('version'); if ($mywp_version >= '2.7') { ?> <a href="<?php echo wp_logout_url(get_bloginfo('url')); ?>"><?php _e('Log out &raquo;', TEMPLATE_DOMAIN); ?></a> <?php } else { ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php __('Log out of this account', TEMPLATE_DOMAIN); ?>"><?php _e('Log out &raquo;', TEMPLATE_DOMAIN); ?></a> <?php } ?>
						</p>
					<?php else : ?>
						<?php $req = get_option( 'require_name_email' ); ?>
						<p class="form-author">
							<label for="author"><?php _e('Name', TEMPLATE_DOMAIN); ?> <?php if ( $req ) : ?><span class="required"><?php _e('*', TEMPLATE_DOMAIN); ?></span><?php endif; ?></label>
							<input type="text" class="text-input" name="author" id="author" value="<?php echo $comment_author; ?>" size="40" tabindex="1" />
						</p>
						<p class="form-email">
							<label for="email"><?php _e('Email', TEMPLATE_DOMAIN); ?>  <?php if ( $req ) : ?><span class="required"><?php _e('*', TEMPLATE_DOMAIN); ?></span><?php endif; ?></label>
							<input type="text" class="text-input" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="40" tabindex="2" />
						</p>
						<p class="form-url">
							<label for="url"><?php _e('Website', TEMPLATE_DOMAIN); ?></label>
							<input type="text" class="text-input" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="40" tabindex="3" />
						</p>
					<?php endif; ?>
					<p class="form-textarea">
						<label for="comment"><?php _e('Comment', TEMPLATE_DOMAIN); ?></label>
						<textarea name="comment" id="comment" cols="60" rows="10" tabindex="4"></textarea>
					</p>
	
					<p class="form-submit">
						<input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', TEMPLATE_DOMAIN); ?>" />
						<?php comment_id_fields(); ?>
					</p>
					<div class="comment-action">
						<?php do_action( 'comment_form', $post->ID ); ?>
					</div>
				</form>
			<?php endif; ?>
		</div><!-- .comment-content -->
	</div><!-- #respond -->
	<?php endif; ?>
	<?php if ( $numTrackBacks ) : ?>
		<div id="trackbacks">
			<span class="title"><?php the_title() ?></span>
			<?php if ( 1 == $numTrackBacks ) : ?>
				<h3><?php printf( __( '%d Trackback', TEMPLATE_DOMAIN ), $numTrackBacks ) ?></h3>
			<?php else : ?>
				<h3><?php printf( __( '%d Trackbacks', TEMPLATE_DOMAIN ), $numTrackBacks ) ?></h3>
			<?php endif; ?>
			<ul id="trackbacklist">
				<?php foreach ( (array)$comments as $comment ) : ?>
					<?php if ( get_comment_type() != 'comment' ) : ?>
						<li><h5><?php comment_author_link() ?></h5><em>on <?php comment_date() ?></em></li>
  					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>