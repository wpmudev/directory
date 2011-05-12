<?php

/**
 * Directory_Theme_Core 
 * 
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Theme_Core {

    /**
     * Class constructor. 
     */
    function DR_Theme_Core() {
        add_action( 'after_setup_theme', array( &$this, 'theme_setup' ) );
        add_action( 'widgets_init', array( &$this, 'register_sidebars' ) );
		add_filter( 'excerpt_length', array( &$this, 'new_excerpt_length' ) );
		add_filter( 'excerpt_more', array( &$this, 'new_excerpt_more' ) );
        add_action( 'init', array( &$this, 'handle_action_buttons_requests' ) );
	}

    /**
     * Setup theme.
     *
     * @return void
     **/
    function theme_setup() {
        add_theme_support( 'post-thumbnails', array( 'directory_listing' ) );
        add_theme_support( 'automatic-feed-links', array( 'directory_listing' ) );

        register_nav_menus( array(
            'primary' => __( 'Primary Navigation', 'directory' ),
        ) );
  		// This theme allows users to set a custom background
        add_custom_background();
    }

    /**
     * Register sidebars by running on the widgets_init hook.
     *
     * @return void
     */
    function register_sidebars() {
        // Area 1, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'First Footer Widget Area', 'directory' ),
            'id' => 'first-footer-widget-area',
            'description' => __( 'The first footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 2, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Second Footer Widget Area', 'directory' ),
            'id' => 'second-footer-widget-area',
            'description' => __( 'The second footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 3, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Third Footer Widget Area', 'directory' ),
            'id' => 'third-footer-widget-area',
            'description' => __( 'The third footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
        // Area 4, located in the footer. Empty by default.
        register_sidebar( array(
            'name' => __( 'Fourth Footer Widget Area', 'directory' ),
            'id' => 'fourth-footer-widget-area',
            'description' => __( 'The fourth footer widget area', 'directory' ),
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) );
    }

	/**
	 * Filter excerpt more link.
	 *
	 * @global <type> $post
	 * @param <type> $more
	 * @return void
	 */
	function new_excerpt_more( $more ) {
		global $post;
		return ' ... <br /><a class="view-listing" href="'. get_permalink( $post->ID ) . '">' . __( '{View Listing}', 'directory' ) . '</a>';
	}

	/**
	 * Filter excerpt lenght.
	 *
	 * @param int Chracter size of excerpt
	 * @return int Chracter size of excerpt
	 */
	function new_excerpt_length( $length ) {
		return 35;
	}

    /**
     * handle_action_buttons_requests 
     * 
     * @access public
     * @return void
     */
    function handle_action_buttons_requests(  ) {
        /* If your want to go to admin profile */
        if ( isset( $_POST['redirect_profile'] ) ) {
            wp_redirect( admin_url() . 'profile.php' );
            exit();
        }
        elseif ( isset( $_POST['redirect_listing'] ) ) {
            wp_redirect( admin_url() . 'post-new.php?post_type=listing' );
            exit();
        }
    }
}

new DR_Theme_Core();
