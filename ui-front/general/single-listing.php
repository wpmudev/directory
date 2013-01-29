<script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/ui-front.js'; ?>" >
</script>

<?php if ( isset( $_POST['_wpnonce'] ) ): ?>
<br clear="all" />
<div id="dr-message-error">
	<?php _e( "Send message failed: you didn't fill all required fields correctly in contact form!", $this->text_domain ); ?>
</div>
<br clear="all" />
<?php endif; ?>

<?php if ( isset( $_GET['sent'] ) ):

if(1 == $_GET['sent'] ): ?>
<br clear="all" />
<div id="dr-message">
	<?php _e( 'Message is sent!', $this->text_domain ); ?>
</div>
<br clear="all" />
<?php else: ?>
<div id="dr-message-error">
	<?php _e( 'Email service is not responding!', $this->text_domain ); ?>
</div>
<?php
endif;

endif; ?>

<?php if(has_post_thumbnail() ) the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?>

<div class="entry-meta">
	<?php the_dr_posted_on(); ?>
	<?php do_action('sr_avg_rating'); ?><br />
	<p>
		<span class="comments">
			<?php comments_popup_link(
			__( 'No Reviews &#187;', DR_TEXT_DOMAIN ),
			__( '1 Review &#187;', DR_TEXT_DOMAIN ),
			__( '% Reviews &#187;', DR_TEXT_DOMAIN ),
			'',
			__( 'Reviews Off;', DR_TEXT_DOMAIN )
			); ?>
		</span>
	</p>
	<?php the_dr_posted_in(); ?>

	<?php if ( !is_user_logged_in() ) : ?>
	<?php echo '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to rate item.', DR_TEXT_DOMAIN), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>'; ?>
	<?php else: ?>
	<?php do_action('sr_rate_this'); ?>
	<?php endif; ?>
</div>

<div class="clear"></div>

<form method="post" action="#" class="contact-user-btn action-form" id="action-form">
	<input type="submit" name="contact_user" value="<?php _e('Contact User', $this->text_domain ); ?>" onclick="dr_listings.toggle_contact_form(); return false;" />
</form>

<div class="clear"></div>

<form method="post" action="#" class="standard-form base dr-contact-form" id="confirm-form">
	<?php
	global $current_user;

	$name   = ( isset( $current_user->display_name ) && '' != $current_user->display_name ) ? $current_user->display_name :
	( ( isset( $current_user->first_name ) && '' != $current_user->first_name ) ? $current_user->first_name : '' );
	$email  = ( isset( $current_user->user_email ) && '' != $current_user->user_email ) ? $current_user->user_email : '';
	?>
	<div class="editfield">
		<label for="name"><?php _e( 'Name', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
		<input type="text" id="name" name ="name" value="<?php echo ( isset( $_POST['name'] ) ) ? $_POST['name'] : $name; ?>" />
		<p class="description"><?php _e( 'Enter your full name here.', $this->text_domain ); ?></p>
	</div>
	<div class="editfield">
		<label for="email"><?php _e( 'Email', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
		<input type="text" id="email" name ="email" value="<?php echo ( isset( $_POST['email'] ) ) ? $_POST['email'] : $email; ?>" />
		<p class="description"><?php _e( 'Enter a valid email address here.', $this->text_domain ); ?></p>
	</div>
	<div class="editfield">
		<label for="subject"><?php _e( 'Subject', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
		<input type="text" id="subject" name ="subject" value="<?php echo ( isset( $_POST['subject'] ) ) ? $_POST['subject'] : ''; ?>" />
		<p class="description"><?php _e( 'Enter the subject of your inquire here.', $this->text_domain ); ?></p>
	</div>
	<div class="editfield">
		<label for="message"><?php _e( 'Message', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
		<textarea id="message" name="message"><?php echo ( isset( $_POST['message'] ) ) ? $_POST['message'] : ''; ?></textarea>
		<p class="description"><?php _e( 'Enter the content of your inquire here.', $this->text_domain ); ?></p>
	</div>

	<div class="editfield">
		<label for="dr_random_value"><?php _e( 'Security image', $this->text_domain ); ?> (<?php _e( 'required', $this->text_domain ); ?>)</label>
		<img class="captcha" src="<?php echo $this->plugin_url; ?>ui-front/general/dr-captcha-image.php" />
		<input type="text" id="dr_random_value" name ="dr_random_value" value="" size="8" />
		<p class="description"><?php _e( 'Enter the characters from the image.', $this->text_domain ); ?></p>
	</div>

	<div class="submit">
		<p>
			<?php wp_nonce_field( 'send_message' ); ?>
			<input type="submit" class="button confirm" value="<?php _e( 'Send', $this->text_domain ); ?>" name="contact_form_send" />
			<input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onclick="dr_listings.cancel_contact_form(); return false;" />
		</p>
	</div>

</form>
<div class="clear"></div>

<?php
//Note that $content is prefilled with the usual expanded the_content from WP by the plugin, we're just wrapping it with extra meta.
echo $content;
?>
<div class="clear"></div>
<div class="dr-custom-block">
	<?php echo do_shortcode('[custom_fields_block wrap="table"][/custom_fields_block]'); ?>
</div>

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
