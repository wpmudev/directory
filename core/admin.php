<?php

/**
 * Directory_Core_Admin 
 * 
 * @uses Directory_Core
 * @package Directory
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh} 
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class Directory_Core_Admin extends Directory_Core {

    /**
     * Constructor.
     */
    function Directory_Core_Admin() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'render_admin_navigation', array( &$this, 'render_admin_navigation' ) );

        // add_filter( 'allow_per_site_content_types', array( &$this, 'allow_per_site_content_types' ) );

		// TODO Fix link 
        $plugin = plugin_basename(__FILE__);
        add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_settings_link' ) );
    }

    /**
     * Register all admin menues.
     *
     * @return void
     */
    function admin_menu() {
		$settings_page = add_submenu_page( 'edit.php?post_type=listing', __( 'Settings', $this->text_domain ), __( 'Settings', $this->text_domain ), 'edit_users', 'settings', array( &$this, 'handle_settings_page_requests' ) );

		// Hook styles and scripts 
        add_action( 'admin_print_styles-' .  $settings_page, array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $settings_page, array( &$this, 'enqueue_scripts' ) );
    }

    /**
     *
     * @param <type> $links
     * @return <type>
     */
    function plugin_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=dp_main&dp_settings=main&dp_gen">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Load styles on plugin admin pages only.
     *
     * @return void
     */
    function enqueue_styles() {
        wp_enqueue_style( 'dp-admin-styles',
                           $this->plugin_url . 'ui-admin/css/ui-styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     */
    function enqueue_scripts() {
        wp_enqueue_script( 'dp-admin-scripts',
                            $this->plugin_url . 'ui-admin/js/ui-scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * Handles $_GET and $_POST requests for the settings page. 
     *
     * @return void
     */
    function handle_settings_page_requests() {
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' || empty( $_GET['tab'] ) ) {

			if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'ads' ) {
				if ( isset( $_POST['save'] ) ) {
					$this->save_admin_options( $_POST );
				}

				$this->render_admin( 'settings-ads' );
			} else {
				if ( isset( $_POST['save'] ) ) {
					// Set network-wide content types 
					if ( !empty( $_POST['allow_per_site_content_types'] ) )
						update_site_option( 'allow_per_site_content_types', true );
					else
						update_site_option( 'allow_per_site_content_types', false );

					$this->save_admin_options( $_POST );
				}

				$this->render_admin( 'settings-general' );
			}
		}

        do_action('directory_handle_settings_page_requests');
    }

    /**
     * Allow users to be able to register content types for their sites or
     * disallow it ( only super admin can add content types )
     *
     * @param <type> $bool
     * @return bool 
     */
    function allow_per_site_content_types( $bool ) {
        $option = get_site_option('allow_per_site_content_types');
        if ( !empty( $option ) )
            return true;
        else
            return $bool;
    }

    /**
	 * Renders an admin section of display code.
	 *
	 * @param  string $name Name of the admin file(without extension)
	 * @param  string $vars Array of variable name=>value that is available to the display code(optional)
	 * @return void
	 **/
    function render_admin( $name, $vars = array() ) {
		foreach ( $vars as $key => $val )
			$$key = $val;
		if ( file_exists( "{$this->plugin_dir}ui-admin/{$name}.php" ) )
			include "{$this->plugin_dir}ui-admin/{$name}.php";
		else
			echo "<p>Rendering of admin template {$this->plugin_dir}ui-admin/{$name}.php failed</p>";
	}

    /**
     * Save plugin options.
     *
     * @param  array $params The $_POST array
     * @return die() if _wpnonce is not verified
     **/
    function save_admin_options( $params ) {
        if ( wp_verify_nonce( $params['_wpnonce'], 'verify' ) ) {
            /* Remove unwanted parameters */
            unset( $params['_wpnonce'], $params['_wp_http_referer'], $params['save'] );
            /* Update options by merging the old ones */
            $options = $this->get_options();
            $options = array_merge( $options, array( $params['key'] => $params ) );
            update_option( $this->options_name, $options );
        } else {
            die( __( 'Security check failed!', $this->text_domain ) );
        }
    }
}

/* Initiate Admin */
new Directory_Core_Admin();
