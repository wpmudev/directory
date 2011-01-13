<?php

/**
 * Classifieds Core Class
 **/
if ( !class_exists('Directory_Core') ):
class Directory_Core {

    /** @var string $plugin_version plugin version */
    var $plugin_version = DP_VERSION;
    /** @var string $plugin_db_version plugin database version */
    var $plugin_db_version = DP_DB_VERSION;
    /** @var string $plugin_url Plugin URL */
    var $plugin_url    = DP_PLUGIN_URL;
    /** @var string $plugin_dir Path to plugin directory */
    var $plugin_dir    = DP_PLUGIN_DIR;
    /** @var string $text_domain The text domain for strings localization */
    var $text_domain   = 'directory';
    /** @var string User role */
    var $user_role = 'dp_member';

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
        /* Register Directory themes contained within the dp-themes folder */
        if ( function_exists( 'register_theme_directory' ))
            register_theme_directory( $this->plugin_dir . 'dp-themes' );
        
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'admin_init', array( &$this, 'hook' ) );
        add_action( 'init', array( &$this, 'roles' ) );
        add_action( 'init', array( &$this, 'add_pages' ) );
        add_action( 'init', array( &$this, 'dp_set_billing_type' ) );
        add_action( 'init', array( &$this, 'dp_redirect_after_payment' ) );
        add_action( 'init', array( &$this, 'dp_core_process_paypal_express_settings' ) );
        add_action( 'init', array( &$this, 'dp_core_init_submit_site_settings' ) );
        add_action( 'init', array( &$this, 'dp_core_process_ads_settings' ) );
        add_action( 'init', array( &$this, 'dp_core_process_general_settings' ) );
        add_action( 'dp_loaded', array( &$this, 'dp_init' ) );
    }

    /**
     * Initiate variables.
     *
     * @return void
     **/
    function init_vars() {

    }

    /**
     * Register all admin menues.
     *
     * @return void
     **/
    function admin_menu() {
        add_menu_page( __( 'Directory', $this->text_domain ), __( 'Directory', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'load_admin_ui' ) );
        add_submenu_page( 'dp_main', __( 'Settings', $this->text_domain ), __( 'Settings', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'load_admin_ui' ) );
    }

    /**
     * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
     * 
     * @return void
     **/
    function hook() {
        $page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : NULL;
        if ( function_exists( 'get_plugin_page_hook' ))
            $hook = get_plugin_page_hook( $page, 'dp_main' );
        else
            $hook = 'directory' . '_page_' . $page;

        add_action( 'admin_print_styles-' .  $hook, array( &$this, 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $hook, array( &$this, 'enqueue_scripts' ) );
    }

    /**
     * Load styles on plugin admin pages only.
     *
     * @return void
     **/
    function enqueue_styles() {
        wp_enqueue_style( 'dp-admin-styles',
                           $this->plugin_url . 'dp-admin-ui/css/dp-admin-ui-styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     **/
    function enqueue_scripts() {
        wp_enqueue_script( 'dp-admin-scripts',
                            $this->plugin_url . 'dp-admin-ui/js/dp-admin-ui-scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * dp_core_load_admin_ui()
     *
     * Loads admin page templates based on $_GET request values and passes variables.
     */
    function load_admin_ui() {
        include_once $this->plugin_dir . 'dp-admin-ui/dp-admin-ui-main.php';

        // load settings ui
        if ( $_GET['page'] == 'dp_main' )
            dp_admin_ui_main();
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
     * Add neccessary pages.
     *
     * @return void
     **/
    function add_pages() {
        $page      = get_page_by_title( 'Submit Listing' );
        $options   = get_site_option( 'dp_options' );

        // check whether page exists
        if ( !isset( $page ) ) {
            $args = array(
                'post_title' => 'Submit Listing',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'ping_status' => 'closed',
                'comment_status' => 'closed'
            );

            // insert page and get the id
            $post_id = wp_insert_post( $args );
            // update post meta with the template file which we will use for submit listing
            update_post_meta( $post_id , '_wp_page_template', 'submit-listing.php' );

            $id_record = array( 'submit_page_id' => $post_id );
            $options   = array_merge( $options, $id_record );
            update_site_option( 'dp_options', $options );
        }
        elseif ( isset( $page ) && !isset( $options['submit_page_id'] )) {
            // update post meta with the template file which we will use for submit listing
            update_post_meta( $page->ID , '_wp_page_template', 'submit-listing.php' );

            $id_record = array( 'submit_page_id' => $page->ID );
            $options   = array_merge( $options, $id_record );
            update_site_option( 'dp_options', $options );
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
    function dp_insert_user( $email, $first_name, $last_name, $billing ) {

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
     * dp_set_billing_type()
     *
     * @return <type>
     */
    function dp_set_billing_type() {
        if ( !isset( $_POST['payment_method_submit'] ))
            return;

        $_SESSION['billing'] = $_POST['billing'];
    }

    /**
     * dp_redirect_after_payment()
     */
    function dp_redirect_after_payment() {

        if ( isset( $_REQUEST['redirect_admin_profile'] )) {
            wp_redirect( admin_url( 'profile.php' ));
            exit;
        } elseif ( isset( $_REQUEST['redirect_admin_listings'] )) {
            wp_redirect( admin_url( 'post-new.php?post_type=directory_listing' ));
            exit;
        }
    }

    /**
     * dp_core_process_paypal_express_settings()
     *
     * Process PayPal admin settings add/update
     */
    function dp_core_process_paypal_express_settings() {

        // stop execution and return if no request is made
        if ( !isset( $_POST['dp_submit_paypal_express_settings'] ))
            return;

        // verify wp_nonce
        if ( !wp_verify_nonce( $_POST['dp_submit_paypal_express_settings_secret'], 'dp_submit_paypal_express_settings_verify'))
            return;

        $post_options = array( 'paypal' => array(
            'api_url'       => $_POST['paypal_express_url'],
            'api_username'  => $_POST['paypal_express_api_username'],
            'api_password'  => $_POST['paypal_express_api_password'],
            'api_signature' => $_POST['paypal_express_api_signature'],
            'currency_code' => $_POST['paypal_express_currency_code']
        ));

        $old_options = get_site_option( 'dp_options' );

        if ( isset( $old_options['paypal'] )) {
            $new_options = array_merge( $old_options, $post_options );
            update_site_option( 'dp_options', $new_options );
        } else {
            update_site_option( 'dp_options', $post_options );
        }
    }


    /**
     * dp_core_init_submit_site_settings()
     *
     * @return <type>
     */
    function dp_core_init_submit_site_settings() {

        $options = get_site_option( 'dp_options' );

        if ( !isset( $options['submit_site_settings'] )) {
            $submit_site_settings = array( 'submit_site_settings' => array(
                'annual_price'   => 10,
                'annual_txt'     => 'Annually Recurring Charge',
                'one_time_price' => 50,
                'one_time_txt'   => 'One-Time Only Charge',
                'tos_txt'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.'
            ));
            $options = array_merge( $options, $submit_site_settings );
            update_site_option( 'dp_options', $options );
            return;
        }

        // stop execution and return if no request is made
        if ( !isset( $_POST['dp_submit_site_settings'] ))
            return;
        // verify wp_nonce
        if ( !wp_verify_nonce( $_POST['dp_submit_settings_submit_site_secret'], 'dp_submit_settings_submit_site_verify'))
            return;

        $submit_site_settings = array( 'submit_site_settings' => array(
            'annual_price'   => $_POST['annual_payment_option_price'],
            'annual_txt'     => $_POST['annual_payment_option_txt'],
            'one_time_price' => $_POST['one_time_payment_option_price'],
            'one_time_txt'   => $_POST['one_time_payment_option_txt'],
            'tos_txt'        => $_POST['tos_txt']
        ));

        $options = array_merge( $options, $submit_site_settings );
        update_site_option( 'dp_options', $options );
    }

    /**
     * dp_core_process_ads_settings()
     *
     * @return <type>
     */
    function dp_core_process_ads_settings() {

        $options = get_site_option( 'dp_options' );

        if ( !isset( $options['ads']['h_ad'] )) {
            $ads     = array( 'ads' => array( 'h_ad' => '<div class="h-ads"><span>Advertise Here</span></div>' ));
            $options = array_merge( $options, $ads );
            update_site_option( 'dp_options', $options );
            return;
        }

        // return if no post request is made
        if ( !isset( $_POST['dp_submit_ads_settings'] ))
            return;
        // verify wp_nonce
        if ( !wp_verify_nonce( $_POST['dp_submit_settings_ads_secret'], 'dp_submit_settings_ads_verify'))
            return;

        $ads     = array( 'ads' => array( 'h_ad' => stripslashes( $_POST['h_ad_code'] )));
        $options = array_merge( $options, $ads );
        update_site_option( 'dp_options', $options );
    }

    function dp_core_process_general_settings() {
        // return if no post request is made
        if ( !isset( $_POST['dp_submit_general_settings'] ))
            return;
        // verify wp_nonce
        if ( !wp_verify_nonce( $_POST['dp_submit_settings_general_secret'], 'dp_submit_settings_general_verify'))
            return;

        $settings = array( 'general_settings' => array( 'order_taxonomies' => $_POST['order_taxonomies'] ));
        $options = get_site_option( 'dp_options' );
        $options = array_merge( $options, $settings );
        update_site_option( 'dp_options', $options );
    }

    /**
     * dp_init()
     *
     * Allow components to initialize themselves cleanly
     */
    function dp_init() {
        do_action( 'dp_init' );
    }

}
endif;

/* Initiate Class */
if ( class_exists('Directory_Core') )
	$__directory_core = new Directory_Core();

?>