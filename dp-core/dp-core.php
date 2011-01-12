<?php

/* define the plugin folder url */
define ( 'DP_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__DIR__), '', plugin_basename(__DIR__) ));
/* define the plugin folder dir */
define ( 'DP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__DIR__), '', plugin_basename(__DIR__) ));

/* Register DirectoryPress themes contained within the dp-themes folder */
if ( function_exists( 'register_theme_directory' ))
	register_theme_directory( DP_PLUGIN_DIR . 'dp-themes' );

/**
 * dp_core_admin_menu()
 *
 * Register all admin menues.
 */
function dp_core_admin_menu() {
	add_menu_page( __('DirectoryPress', 'directorypress'), __('DirectoryPress', 'directorypress'), 'edit_users', 'dp_main', 'dp_core_load_admin_ui' );
    add_submenu_page( 'dp_main', __('Settings', 'directorypress'), __('Settings', 'directorypress'), 'edit_users', 'dp_main', 'dp_core_load_admin_ui' );
}
add_action( 'admin_menu', 'dp_core_admin_menu' );

/**
 * dp_core_hook()
 *
 * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
 */
function dp_core_hook() {
	if ( function_exists( 'get_plugin_page_hook' ))
		$hook = get_plugin_page_hook( $_GET['page'], 'dp_main' );
	else
		$hook = 'directorypress' . '_page_' . $_GET['page']; /** @todo Make hook plugin specific */

    /** @deprecated
    if ( $_GET['page'] == 'dp_main' )
        add_action( 'admin_head-' . $hook, 'dp_ajax_actions');
    */
}
add_action( 'admin_init', 'dp_core_hook' );

/**
 * dp_core_load_admin_ui()
 * 
 * Loads admin page templates based on $_GET request values and passes variables.
 */
function dp_core_load_admin_ui() {
    include_once DP_PLUGIN_DIR . 'dp-admin-ui/dp-admin-ui-settings.php';

    // load settings ui
    if ( $_GET['page'] == 'dp_main' )
        dp_admin_ui_settings(); 
}

/**
 * dp_init()
 *
 * Allow components to initialize themselves cleanly
 */
function dp_init() {
	do_action( 'dp_init' );
}
add_action( 'dp_loaded', 'dp_init' );

?>