<div id="action-bar">
    <span><?php _e( 'Welcome, what would you like to do?', THEME_TEXT_DOMAIN ); ?></span>

    <?php if ( !is_user_logged_in()): ?>
        <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signup/'; ?>" class="button">Sign Up</a></div>
        <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signin/'; ?>" class="button">Sign In</a></div>
    <?php else: ?>
        <form id="go-to-profile" action="#" method="post">
            <input type="submit" name="directory_logout" value="Log out" />
        </form>
        <form id="add-listing" action="#" method="post">
            <input type="submit" name="redirect_listing" value="Add Listing" />
        </form>
        <form id="go-to-profile" action="#" method="post">
            <input type="submit" name="redirect_profile" value="Go To Profile" />
        </form>
    <?php endif; ?>

    <div class="clear"></div>
</div>
