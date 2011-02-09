<?php

if ( !class_exists('Directory_Theme_Core') ):

/**
 * Directory_Theme_Core 
 * 
 * @package Directory Theme 
 * @version 1.0.0
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class Directory_Theme_Core
{

    /**
     * Class constructor. 
     */
    function Directory_Theme_Core() {
		$this->init();
	}

    /**
     * Hook class methods. 
     * 
     * @access public
     * @return void
     */
	function init() {
        add_action( 'after_setup_theme', array( &$this, 'theme_setup' ) );
        add_action( 'widgets_init', array( &$this, 'register_sidebars' ) );
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
     **/
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
}
endif;

if ( class_exists('Directory_Theme_Core') )
	$directory_theme_core = new Directory_Theme_Core();

?>
