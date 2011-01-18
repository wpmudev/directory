<?php

/**
 * Directory Core Classs
 **/
if ( !class_exists('Directory_Core') ):
class Directory_Core {

    /** @var string $plugin_version plugin version */
    var $plugin_version = DP_VERSION;
    /** @var string $plugin_db_version plugin database version */
    var $plugin_db_version = DP_DB_VERSION;
    /** @var string $plugin_url Plugin URL */
    var $plugin_url = DP_PLUGIN_URL;
    /** @var string $plugin_dir Path to plugin directory */
    var $plugin_dir = DP_PLUGIN_DIR;
    /** @var string $text_domain The text domain for strings localization */
    var $text_domain = 'directory';
    /** @var string User role */
    var $user_role = 'dp_member';
    /** @var string Name of options DB entry */
    var $options_name = 'dp_options';
    /** @var array Name of options DB entry */
    var $admin_page_tabs;
    /** @var array Name of options DB entry */
    var $admin_page_subtabs;
    /** @var string Name of options DB entry */
    var $payments_module;

    /**
     * Constructor.
     *
     * @return void
     **/
    function Directory_Core() {
        $this->init();
        $this->init_vars();
    }

    /**
     * Intiate plugin.
     *
     * @return void
     **/
    function init() {
        add_action( 'plugins_loaded', array( &$this, 'init_submodules' ) );
        /* Load plugin translation file */
        add_action( 'init', array( &$this, 'load_plugin_textdomain' ), 0 );
        /* Register activation hook */
        register_activation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_activate' ) );
        /* Register deactivation hook */
        register_deactivation_hook( $this->plugin_dir . 'loader.php', array( &$this, 'plugin_deactivate' ) );
        /* Register Directory themes contained within the dp-themes folder */
        if ( function_exists( 'register_theme_directory' ) )
            register_theme_directory( $this->plugin_dir . 'themes' );
        
        add_action( 'init', array( &$this, 'roles' ) );
        add_filter( 'allow_per_site_content_types', array( &$this, 'allow_per_site_content_types' ) );
        
        $plugin = plugin_basename(__FILE__);
        add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_settings_link' ) );
    }

    /**
     * Initiate variables.
     *
     * @return void
     **/
    function init_vars() {

    }

    /**
     * Loads "directory-[xx_XX].mo" language file from the "languages" directory
     *
     * @return void
     **/
    function load_plugin_textdomain() {
        $plugin_dir = $this->plugin_dir . 'languages';
        load_plugin_textdomain( 'directory', null, $plugin_dir );
    }

    /**
     * Update plugin versions
     *
     * @return void
     **/
    function plugin_activate() {
        /* Update plugin versions */
        $versions = array( 'versions' => array( 'version' => $this->plugin_version, 'db_version' => $this->plugin_db_version ) );
        $options = $this->get_options();
        $options = ( isset( $options['versions'] ) ) ? array_merge( $options, $versions ) : $versions;
        update_option( $this->options_name, $options );
    }

    /**
     * Deactivate plugin. If $this->flush_plugin_data is set to "true"
     * all plugin data will be deleted
     *
     * @return void
     */
    function plugin_deactivate() {
        /* if true all plugin data will be deleted */
        if ( false ) {
            delete_option( $this->options_name );
            delete_option( 'ct_custom_post_types' );
            delete_option( 'ct_custom_taxonomies' );
            delete_option( 'ct_custom_fields' );
            delete_option( 'ct_flush_rewrite_rules' );
            delete_site_option( $this->options_name );
            delete_site_option( 'ct_custom_post_types' );
            delete_site_option( 'ct_custom_taxonomies' );
            delete_site_option( 'ct_custom_fields' );
            delete_site_option( 'ct_flush_rewrite_rules' );
        }
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
     * Initiate submodule to be used with this plugin.
     *
     * @return void
     */
    function init_submodules() {
        if ( class_exists('Content_Types_Core') )
            $content_types_submodule = new Content_Types_Core('dp_main');
        /* Initiate Class */
        if ( class_exists('Payments_Core') ) {
            $this->payments_module = new Payments_Core('dp_main');
        }
        /* Initiate Class */
        if ( class_exists('Directory_Core_Admin') )
            $__directory_core_admin = new Directory_Core_Admin( $this->payments_module );
    }

    /**
     * Add custom role for Classifieds members. Add new capabilities for admin.
     *
     * @global $wp_roles
     * @return void
     **/
    function roles() {
        global $wp_roles;
        if ( $wp_roles ) {
            /** @todo remove remove_role */
            $wp_roles->remove_role( $this->user_role );
            $wp_roles->add_role( $this->user_role, 'Directory Member', array(
                'publish_listings'       => true,
                'edit_listings'          => true,
                'edit_others_listings'   => false,
                'delete_listings'        => false,
                'delete_others_listings' => false,
                'read_private_listings'  => false,
                'edit_listing'           => true,
                'delete_listing'         => true,
                'read_listing'           => true,
                'upload_files'           => true,
                'assign_terms'           => true,
                'read'                   => true
            ) );
            /* Set administrator roles */
            $wp_roles->add_cap( 'administrator', 'publish_listings' );
            $wp_roles->add_cap( 'administrator', 'edit_listings' );
            $wp_roles->add_cap( 'administrator', 'edit_others_listings' );
            $wp_roles->add_cap( 'administrator', 'delete_listings' );
            $wp_roles->add_cap( 'administrator', 'delete_others_listings' );
            $wp_roles->add_cap( 'administrator', 'read_private_listings' );
            $wp_roles->add_cap( 'administrator', 'edit_listing' );
            $wp_roles->add_cap( 'administrator', 'delete_listing' );
            $wp_roles->add_cap( 'administrator', 'read_listing' );
            $wp_roles->add_cap( 'administrator', 'assign_terms' );
        }
    }

    /**
     * Insert User
     *
     * @param <type> $email
     * @param <type> $first_name
     * @param <type> $last_name
     * @return <type>
     */
    function insert_user( $email, $first_name, $last_name, $billing ) {

        require_once( ABSPATH . WPINC . '/registration.php' );

        // variables
        $user_login     = sanitize_user( strtolower( $first_name ));
        $user_email     = $email;
        $user_pass      = wp_generate_password();

        if ( username_exists( $user_login ) )
            $user_login .= '-' . sanitize_user( strtolower( $last_name ));

        if ( username_exists( $user_login ) )
            $user_login .= rand(1,9);

        if ( email_exists( $user_email )) {
            $user = get_user_by( 'email', $user_email );

            if ( $user ) {
                wp_update_user( array ('ID' => $user->ID, 'role' => 'dpmember' )) ;
                update_user_meta( $user->ID, 'dp_billing', $billing );
                $credentials = array( 'remember'=>true, 'user_login' => $user->user_login, 'user_password' => $user->user_pass );
                wp_signon( $credentials );
                return;
            }
        }

        $user_id = wp_insert_user( array( 'user_login'   => $user_login,
                                          'user_pass'    => $user_pass,
                                          'user_email'   => $email,
                                          'display_name' => $first_name . ' ' . $last_name,
                                          'first_name'   => $first_name,
                                          'last_name'    => $last_name,
                                          'role'         => 'dpmember'
                                        )) ;

        update_user_meta( $user_id, 'dp_billing', $billing );
        wp_new_user_notification( $user_id, $user_pass );
        $credentials = array( 'remember'=>true, 'user_login' => $user_login, 'user_password' => $user_pass );
        wp_signon( $credentials );
    }

    /**
     * Allow users to be able to register content types for their sites or
     * disallow it ( only super admin can add content types )
     *
     * @param <type> $bool
     * @return bool 
     */
    function allow_per_site_content_types( $bool ) {
        $options = $this->get_options('general_settings');
        if ( isset( $options['allow_per_site_content_types'] ) )
            return true;
        else
            return $bool;
    }

    /**
     * Save plugin options.
     *
     * @param  array $params The $_POST array
     * @return die() if _wpnonce is not verified
     **/
    function save_options( $params ) {
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

    /**
     * Get plugin options.
     *
     * @param  string|NULL $key The key for that plugin option.
     * @return array $options Plugin options or empty array if no options are found
     **/
    function get_options( $key = NULL ) {
        $options = get_option( $this->options_name );
        $options = is_array( $options ) ? $options : array();
        /* Check if specific plugin option is requested and return it */
        if ( isset( $key ) && array_key_exists( $key, $options ) )
            return $options[$key];
        else
            return $options;
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

}
endif;

/* Initiate Class */
if ( class_exists('Directory_Core') )
	$__directory_core = new Directory_Core();

?>