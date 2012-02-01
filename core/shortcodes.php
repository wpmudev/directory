<?php
/*
MarketPress Shortcode Support
*/

class DR_Shortcodes {

	function DR_Shortcodes() {
		$this->__construct();
	}

  function __construct() {

        add_shortcode( 'dr_list_categories', array( &$this, 'dr_list_categories_sc' ) );
//        add_shortcode( 'dr_actionbuttons', array( &$this, 'dr_actionbuttons_sc' ) );

	}


    /**
    * Display or retrieve the HTML list of product categories.
    */
    function dr_list_categories_sc( $atts ) {
        return the_dr_categories_home( false, $atts );
    }





    function dr_actionbuttons_sc( $atts ) {
        ob_start();

        ?>

        <div id="action-bar">
            <span><?php _e( 'Welcome, what would you like to do?', THEME_TEXT_DOMAIN ); ?></span>

            <?php if ( !is_user_logged_in()): ?>
                <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signup/'; ?>" class="button">Sign Up</a></div>
                <div id="submit-site"><a href="<?php echo get_bloginfo('url') . '/signin/'; ?>" class="button">Sign In</a></div>
            <?php else: ?>
                <form id="go-to-profile" action="" method="post">
                    <input type="submit" name="directory_logout" value="Log out" />
                </form>
                <form id="add-listing" action="" method="post">
                    <input type="submit" name="redirect_listing" value="Add Listing" />
                </form>
                <form id="go-to-profile" action="" method="post">
                    <input type="submit" name="redirect_profile" value="Go To Profile" />
                </form>
            <?php endif; ?>

            <div class="clear"></div>
        </div>

        <?php

        $abuttons = ob_get_contents();
        ob_end_clean();


        return $abuttons;
    }









}
$dr_shortcodes = new DR_Shortcodes();

?>