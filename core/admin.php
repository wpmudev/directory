<?php

if ( !class_exists('Directory_Core_Admin') ):

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
     **/
    function Directory_Core_Admin() {
        $this->init();
    }

    /**
     * Setup hooks.
     *
     * @return void
     **/
    function init() {
        add_action( 'admin_init', array( &$this, 'admin_head' ) );
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'render_admin_navigation', array( &$this, 'render_admin_navigation' ) );
        add_filter( 'allow_per_site_content_types', array( &$this, 'allow_per_site_content_types' ) );
    }

    /**
     * Initiate class variables.
     *
     * @return void
     **/
    function init_vars() {}

    /**
     * Register all admin menues.
     *
     * @return void
     **/
    function admin_menu() {
        add_menu_page( __( 'Directory', $this->text_domain ), __( 'Directory', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'handle_admin_requests' ) );
    //  add_submenu_page( 'dp_main', __( 'Settings', $this->text_domain ), __( 'Settings', $this->text_domain ), 'edit_users', 'dp_main', array( &$this, 'handle_admin_requests' ) );
    }

    /**
     * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
     *
     * @return void
     **/
    function admin_head() {
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
            if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' || empty( $_GET['tab'] ) ) {
                if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'ads' ) {
                    if ( isset( $_POST['save'] ) ) {
                        $this->save_options( $_POST );
                    }
                    $this->render_admin( 'settings-ads' );
                } else {
                    if ( isset( $_POST['save'] ) ) {
                        /* Set network-wide content types */
                        if ( !empty( $_POST['allow_per_site_content_types'] ) )
                            update_site_option( 'allow_per_site_content_types', true );
                        else
                            update_site_option( 'allow_per_site_content_types', false );
                        $this->save_options( $_POST );
                    }
                    $this->render_admin( 'settings-general' );
                }
            }
        }
        do_action('handle_module_admin_requests');
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
     * Render admin navigation.
     */
    function render_admin_navigation( $sub ) {
        $this->render_admin( 'navigation', array( 'sub' => $sub ) );
    }

}
endif;

?>
