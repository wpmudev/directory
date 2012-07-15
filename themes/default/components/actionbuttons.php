<div id="action-bar">
    <span><?php _e( 'Welcome, what would you like to do?', THEME_TEXT_DOMAIN ); ?></span>

    <?php if ( !is_user_logged_in()): ?>
        <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signup/'; ?>" class="button">Sign Up</a></div>
        <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signin/'; ?>" class="button">Sign In</a></div>
    <?php else: ?>
        <form id="log-out" action="#" method="post">
            <input type="submit" name="directory_logout" value="<?php _e('Log Out', THEME_TEXT_DOMAIN); ?>" />
        </form>
        <form id="add-listing" action="#" method="post">
            <input type="submit" name="redirect_listing" value="<?php _e('Add Listing', THEME_TEXT_DOMAIN); ?>" />
        </form>
        <form id="go-to-profile" action="#" method="post">
            <input type="submit" name="redirect_profile" value="<?php _e('Go to Profile', THEME_TEXT_DOMAIN); ?>" />
        </form>
    <?php endif; ?>

    <div class="clear"></div>
</div>
