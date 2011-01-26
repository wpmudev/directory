	<div id="action-bar">
	 Welcome, what would you like to do?
<?php if ( !is_user_logged_in()): ?>
<div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/submit-listing/'; ?>" class="button">Submit Listing</a></div>
<?php else: ?>
<div id="add-listing"><a href="<?php echo get_bloginfo('url') . '/?redirect_admin_listings'; ?>" class="button">Add Listing</a></div>
<div id="go-to-profile"><a href="<?php echo get_bloginfo('url') . '/?redirect_admin_profile'; ?>" class="button">Go to Profile</a></div>

<?php endif; ?>
<div class="clear"></div>
</div>