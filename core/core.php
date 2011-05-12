<?php

/**
 * DR_Core 
 * 
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Core {

    /** @var string $plugin_url Plugin URL */
    var $plugin_url = DR_PLUGIN_URL;
    /** @var string $plugin_dir Path to plugin directory */
    var $plugin_dir = DR_PLUGIN_DIR;
    /** @var string $text_domain The text domain for strings localization */
    var $text_domain = DR_TEXT_DOMAIN;
    /** @var string Name of options DB entry */
    var $options_name = DR_OPTIONS_NAME;

    /**
     * Constructor.
     */
    function DR_Core() {
        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
        register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );

        register_theme_directory( $this->plugin_dir . 'themes' );

		add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( &$this, 'init' ) );

		// TODO: These are not properly implemented
        add_action( 'wp_loaded', array( &$this, 'scheduly_expiration_check' ) );
        add_action( 'check_expiration_dates', array( &$this, 'check_expiration_dates_callback' ) );

		if ( is_admin() )
			return;

		// add_action( 'template_redirect', array( &$this, 'load_default_style' ), 11 );
		add_action( 'template_redirect', array( &$this, 'template_redirect' ), 12 );

		add_filter( 'single_template', array( &$this, 'handle_template' ) );
		add_filter( 'archive_template', array( &$this, 'handle_template' ) );
		
        add_action( 'custom_banner_header', array( &$this, 'output_banners' ) );
    }

    /**
     * Intiate plugin.
     *
     * @return void
     */
    function init() {
		global $wp, $wp_rewrite;

		// Signup page
		$wp->add_query_var( 'dr_signup' );
		$this->add_rewrite_rule( 'signup/?$', array(
			'dr_signup' => 1
		) );

		register_taxonomy( 'listing_tag', 'directory_listing', array(
			'rewrite' => array( 'slug' => 'listings/tag', 'with_front' => false ),
			'labels' => array(
				'name'			=> __( 'Listing Tags', $this->text_domain ),
				'singular_name'	=> __( 'Listing Tag', $this->text_domain ),
				'search_items'	=> __( 'Search Listing Tags', $this->text_domain ),
				'popular_items'	=> __( 'Popular Listing Tags', $this->text_domain ),
				'all_items'		=> __( 'All Listing Tags', $this->text_domain ),
				'edit_item'		=> __( 'Edit Listing Tag', $this->text_domain ),
				'update_item'	=> __( 'Update Listing Tag', $this->text_domain ),
				'add_new_item'	=> __( 'Add New Listing Tag', $this->text_domain ),
				'new_item_name'	=> __( 'New Listing Tag Name', $this->text_domain ),
				'separate_items_with_commas'	=> __( 'Separate listing tags with commas', $this->text_domain ),
				'add_or_remove_items'			=> __( 'Add or remove listing tags', $this->text_domain ),
				'choose_from_most_used'			=> __( 'Choose from the most used listing tags', $this->text_domain ),
			)
		) );

		register_taxonomy( 'listing_category', 'directory_listing', array(
			'rewrite' => array( 'slug' => 'listings/category', 'with_front' => false, 'hierarchical' => true ),
			'hierarchical' => true,
			'labels' => array(
				'name'			=> __( 'Listing Categories', $this->text_domain ),
				'singular_name'	=> __( 'Listing Category', $this->text_domain ),
				'search_items'	=> __( 'Search Listing Categories', $this->text_domain ),
				'popular_items'	=> __( 'Popular Listing Categories', $this->text_domain ),
				'all_items'		=> __( 'All Listing Categories', $this->text_domain ),
				'parent_item'	=> __( 'Parent Category', $this->text_domain ),
				'edit_item'		=> __( 'Edit Listing Category', $this->text_domain ),
				'update_item'	=> __( 'Update Listing Category', $this->text_domain ),
				'add_new_item'	=> __( 'Add New Listing Category', $this->text_domain ),
				'new_item_name'	=> __( 'New Listing Category', $this->text_domain ),
				'parent_item_colon'		=> __( 'Parent Category:', $this->text_domain ),
				'add_or_remove_items'	=> __( 'Add or remove listing categories', $this->text_domain ),
			)
		) );

		register_post_type( 'directory_listing', array(
			'public' => true,
			'rewrite' => array( 'slug' => 'listings', 'with_front' => false ),
			'has_archive' => true,

			'capability_type' => 'listing',
			'capabilities' => array( 'read' => 'read_listings' ),
			'map_meta_cap' => true,

			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'post-formats' ),

			'labels' => array(
				'name'			=> __('Listings', $this->text_domain ),
				'singular_name'	=> __('Listing', $this->text_domain ),
				'add_new'		=> __('Add New', $this->text_domain ),
				'add_new_item'	=> __('Add New Listing', $this->text_domain ),
				'edit_item'		=> __('Edit Listing', $this->text_domain ),
				'new_item'		=> __('New Listing', $this->text_domain ),
				'view_item'		=> __('View Listing', $this->text_domain ),
				'search_items'	=> __('Search Listings', $this->text_domain ),
				'not_found'		=> __('No listings found', $this->text_domain ),
				'not_found_in_trash'	=> __('No listings found in trash', $this->text_domain ),
			)
		) );
    }

	/**
	 * Simple wrapper for adding straight rewrite rules,
	 * but with the matched rule as an associative array.
	 *
	 * @see http://core.trac.wordpress.org/ticket/16840
	 *
	 * @param string $regex The rewrite regex
	 * @param array $args The mapped args
	 * @param string $position Where to stick this rule in the rules array. Can be 'top' or 'bottom'
	 */
	function add_rewrite_rule( $regex, $args, $position = 'top' ) {
		global $wp, $wp_rewrite;

		$result = add_query_arg( $args, 'index.php' );
		add_rewrite_rule( $regex, $result, $position );
	}

    /**
     * Fire on plugin activation.
     *
     * @return void
     */
    function plugin_activate() {
		$this->init();
		flush_rewrite_rules();
    }

    /**
	 * Fire on plugin deactivation. 
	 * If $this->flush_plugin_data is set to "true"
     * all plugin data will be deleted
     *
     * @return void
     */
    function plugin_deactivate() {
		// if true all plugin data will be deleted 
        if ( true ) {
            delete_option( $this->options_name );
            delete_site_option( $this->options_name );
        }
    }

    /**
     * Loads "{$text_domain}-[xx_XX].mo" language file from the "languages" directory
	 *
     * @return void
     */
    function load_plugin_textdomain() {
        load_plugin_textdomain( $this->text_domain, null, plugin_basename( $this->plugin_dir . 'languages' ) );
    }

	/**
	 * Redirect templates using $wp_query.
	 */
	function template_redirect() {
		global $wp_query;

		if ( is_dr_page( 'signup' ) ) {
			$this->load_template( 'page-signup.php' );
		}

		// Redirect template loading to archive-listing.php rather than to archive.php
		if ( is_dr_page( 'tag' ) || is_dr_page( 'category' ) ) {
			$wp_query->set( 'post_type', 'directory_listing' );
		}
	}

	/**
	 * Loads default templates if the current theme doesn't have them.
	 */
	function handle_template( $path ) {
		global $wp_query;

		if ( 'directory_listing' != get_query_var( 'post_type' ) )
			return $path;

		$type = reset( explode( '_', current_filter() ) );

		$file = basename( $path );

		if ( empty( $path ) || "$type.php" == $file ) {
			// A more specific template was not found, so load the default one
			$path = $this->plugin_dir . "templates/$type-listing.php";
		}

		return $path;
	}

	/**
	 * Load a template, with fallback to default-templates.
	 */
	function load_template( $name ) {
		$path = locate_template( $name );

		if ( !$path ) {
			$path = $this->plugin_dir . "templates/$name";
		}

		load_template( $path );
		die;
	}

    /**
	 * Output banner.
	 *
	 * @access public
	 * @return void
     */
    function output_banners() { 
        $options = $this->get_options( 'ads_settings' );
        if ( !empty( $options['header_ad_code'] ) ) {
            echo stripslashes( $options['header_ad_code'] );
        } else {
            echo '<span>' .  __( 'Advartise Here', $this->text_domain ) . '</span>';
        }
    }    


    /**
     * Schedule expiration check for twice daily.
     *
     * @return void
     */
    function scheduly_expiration_check() {
        if ( !wp_next_scheduled( 'check_expiration_dates' ) ) {
            wp_schedule_event( time(), 'twicedaily', 'check_expiration_dates' );
        }
    }

    /**
     * Check each post from the used post type and compare the expiration date/time
     * with the current date/time. If the post is expired update it's status.
     *
     * @return void
     */
    function check_expiration_dates_callback() {}

    /**
     * Get plugin options.
     *
     * @param  string|NULL $key The key for that plugin option.
     * @return array $options Plugin options or empty array if no options are found
     */
    function get_options( $key = null ) {
        $options = get_option( $this->options_name );
        $options = is_array( $options ) ? $options : array();
        /* Check if specific plugin option is requested and return it */
        if ( isset( $key ) && array_key_exists( $key, $options ) )
            return $options[$key];
        else
            return $options;
    }
}

/* Initiate Class */
new DR_Core();

