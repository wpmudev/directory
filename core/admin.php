<?php

/**
 * Directory Admin Class
 */
if ( !class_exists('Directory_Core_Admin') ):
class Directory_Core_Admin extends Directory_Core {

    /**
     * Constructor.
     **/
    function Directory_Core_Admin() {
        $this->init();
        $this->init_vars();
    }

    function init() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'admin_init', array( &$this, 'hook' ) );
        add_action( 'init', array( &$this, 'init_submit_site_settings' ) );
    }

    function init_vars() {
        
    }


    /**
     * Register all admin menues.
     *
     * @return void
     **/
    function admin_menu() {
        add_menu_page( __( 'Directory', $this->text_domain ), __( 'Directory', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'handle_admin_requests' ) );
        add_submenu_page( 'dp_main', __( 'Settings', $this->text_domain ), __( 'Settings', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'handle_admin_requests' ) );
    }

    /**
     * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
     *
     * @return void
     **/
    function hook() {
        $page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : NULL;
        $hook = get_plugin_page_hook( $page, 'dp_main' );
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
                           $this->plugin_url . 'ui-admin/css/ui-styles.css');
    }

    /**
     * Load scripts on plugin specific admin pages only.
     *
     * @return void
     **/
    function enqueue_scripts() {
        wp_enqueue_script( 'dp-admin-scripts',
                            $this->plugin_url . 'ui-admin/js/ui-scripts.js',
                            array( 'jquery' ) );
    }

    /**
     * Loads admin page templates based on $_GET request values and passes variables.
     *
     * @return HTML
     **/
    function handle_admin_requests() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'dp_main' ) {
            if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) {
                if ( $_GET['sub'] == 'authorizenet' ) {
                    $this->render_admin( 'payments-authorizenet' );
                } else {
                    /* Save options */
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    /* Render admin template */
                    $this->render_admin( 'payments-paypal' );
                }
            } 
            else {
                if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'submit_site' ) {
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    $this->render_admin( 'settings-submit-site' );
                } elseif ( isset( $_GET['sub'] ) && $_GET['sub'] == 'ads' ) {
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    $this->render_admin( 'settings-ads' );
                } else {
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    $this->render_admin( 'settings-general' );
                }
            }
        }
    }

    /**
     * Init data for submit site. 
     *
     * @return <type>
     */
    function init_submit_site_settings() {
        $options = $this->get_options();
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
    }
}
endif;

/* Initiate Class */
if ( class_exists('Directory_Core_Admin') )
	$__directory_core_admin = new Directory_Core_Admin();
?>
