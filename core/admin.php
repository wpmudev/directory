<?php

/**
 * DR_Admin
 *
 * @uses DR_Core
 * @copyright Incsub 2007-2011 {@link http://incsub.com}
 * @author Ivan Shaovchev (Incsub) {@link http://ivan.sh}
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Admin extends DR_Core {

	/** @var array Holds all capability names, along with descriptions. */
	var $capability_map;

    /**
     * Constructor.
     */
    function DR_Admin() {
		register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'init_defaults' ) );

        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

		add_action( 'wp_ajax_dr-get-caps', array( &$this, 'ajax_get_caps' ) );
		add_action( 'wp_ajax_dr-save', array( &$this, 'ajax_save' ) );

		// Render admin via action hook. Used mainly by modules.
		add_action( 'render_admin', array( &$this, 'render_admin' ), 10, 2 );

		$this->capability_map = array(
			'read_listings'             => __( 'View listings.', $this->text_domain ),
			'publish_listings'          => __( 'Add listings.', $this->text_domain ),
			'edit_published_listings'   => __( 'Edit listings.', $this->text_domain ),
			'delete_published_listings' => __( 'Delete listings.', $this->text_domain ),
			'edit_others_listings'      => __( 'Edit others\' listings.', $this->text_domain ),
			'delete_others_listings'    => __( 'Delete others\' listings.', $this->text_domain )
		);
    }

	/**
	 * Initiate admin default settings.
	 *
	 * @return void
	 */
	function init_defaults() {
		global $wp_roles;
		foreach ( array_keys( $this->capability_map ) as $capability )
			$wp_roles->add_cap( 'administrator', $capability );

		// add option to the autoload list
		add_option( $this->options_name, array() );
	}

    /**
     * Register all admin menues.
     *
     * @return void
     */
    function admin_menu() {
		$settings_page = add_submenu_page( 'edit.php?post_type=directory_listing', __( 'Settings', $this->text_domain ), __( 'Settings', $this->text_domain ), 'edit_users', 'settings', array( &$this, 'handle_settings_page_requests' ) );

		// Hook styles and scripts
        add_action( 'admin_print_styles-' .  $settings_page, array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $settings_page, array( &$this, 'enqueue_scripts' ) );
    }

    /**
     * Load styles on plugin admin pages only.
     *
     * @return void
     */
    function enqueue_styles() {
        wp_enqueue_style( 'dr-admin-styles',
                           $this->plugin_url . 'ui-admin/css/ui-styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     */
    function enqueue_scripts() {
        wp_enqueue_script( 'dr-admin-scripts',
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
					$this->save_admin_options( $_POST );
				}

				$this->render_admin( 'settings-general' );
			}
		} elseif ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) {

			if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'authorizenet' ) {

				$this->render_admin( 'payments-authorizenet' );

			} elseif ( isset( $_GET['sub'] ) && $_GET['sub'] == 'paypal' ) {
				if ( isset( $_POST['save'] ) ) {
					$this->save_admin_options( $_POST );
				}

				$this->render_admin( 'payments-paypal' );
			} else {
				if ( isset( $_POST['save'] ) ) {
					$this->save_admin_options( $_POST );
				}

				$this->render_admin( 'payments-settings' );
			}
		}

        do_action('dr_handle_settings_page_requests');
    }

	/**
	 * Ajax callback which gets the post types associated with each page.
	 *
	 * @return JSON Encoded string
	 */
	function ajax_get_caps() {
		if ( !current_user_can( 'manage_options' ) )
			die(-1);

		global $wp_roles;

		$role = $_POST['role'];

		if ( !$wp_roles->is_role( $role ) )
			die(-1);

		$role_obj = $wp_roles->get_role( $role );

		$response = array_intersect( array_keys( $role_obj->capabilities ), array_keys( $this->capability_map ) );
		$response = array_flip( $response );

		// response output
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

	/**
	 * Save admin options.
	 *
	 * @return void die() if _wpnonce is not verified
	 */
	function ajax_save() {
		check_admin_referer( 'dir-verify' );

		if ( !current_user_can( 'manage_options' ) )
			die(-1);

		// add/remove capabilities
		global $wp_roles;

		$role = $_POST['roles'];

		$all_caps = array_keys( $this->capability_map );
		$to_add = array_keys( $_POST['capabilities'] );
		$to_remove = array_diff( $all_caps, $to_add );

		foreach ( $to_remove as $capability ) {
			$wp_roles->remove_cap( $role, $capability );
		}

		foreach ( $to_add as $capability ) {
			$wp_roles->add_cap( $role, $capability );
		}

		die(1);
	}

    /**
	 * Renders an admin section of display code.
	 *
	 * @param  string $name Name of the admin file(without extension)
	 * @param  string $vars Array of variable name=>value that is available to the display code(optional)
	 * @return void
	 */
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
     */
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
new DR_Admin();

