<?php
/*
Plugin Name: DirectoryPress
Plugin URI: http://premium.wpmudev.org/project/directorypress
Description: DirectoryPress - Create full blown directory.
Version: 1.0.0
Author: Ivan Shaovchev
Author URI: http://ivan.sh
License: GNU General Public License (Version 2 - GPLv2)
*/

/*
Copyright 2010 Incsub, (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/*
 * Enable error repporting if in debug mode
 */
error_reporting( E_ALL ^ E_NOTICE );
ini_set( 'display_errors', 1 );

/* tmp debug func */
function dp_debug( $param ) {
    echo '<pre>';
    print_r ( $param );
    echo '</pre>';
}

/* Define plugin version */ 
define ( 'DP_VERSION', '1.0.0' );
define ( 'DP_DB_VERSION', '1.1' );

/* include core file */
include_once 'dp-core/dp-core.php';

/* include content types submodule */
include_once 'dp-submodules/content-types/ct-loader.php';

/**
 * dp_load_plugin_textdomain()
 *
 * Loads "custompress-[xx_XX].mo" language file from the "dp-languages" directory
 */
function dp_load_plugin_textdomain() {
    $plugin_dir = DP_PLUGIN_DIR . 'dp-languages';
    load_plugin_textdomain( 'directorypress', null, $plugin_dir );
}
add_action( 'init', 'dp_load_plugin_textdomain', 0 );

/**
 * dp_loaded()
 *
 * Allow dependent plugins and core actions to attach themselves in a safe way.
 *
 * See cm-admin-core.php for the following core actions: cp_init
 */
function dp_loaded() {
	do_action( 'dp_loaded' );
}
add_action( 'init', 'dp_loaded', 20 );

/**
 * dp_loader_activate()
 * 
 * Update plugin version
 */
function dp_plugin_activate() {
	$options = array( 'version' => DP_VERSION, 'db_version' => DP_DB_VERSION );
	update_site_option( 'dp_options', $options );
}
register_activation_hook( __FILE__, 'dp_plugin_activate' );

/**
 * dp_plugin_deactivate()
 *
 * Deactivate plugin. If $flush_dp_data is set to "true"
 * all plugin data will be deleted
 */
function dp_plugin_deactivate() {

    // set this to "true" if you want to delete all of the plugin stored data
    $flush_dp_data = false;

    // if $flush_dp_data is true it will delete all plugin data
    if ( $flush_dp_data ) {
        delete_site_option( 'dp_options' );
        delete_site_option( 'dp_main_settings' );
        delete_site_option( 'ct_custom_post_types' );
        delete_site_option( 'ct_custom_taxonomies' );
        delete_site_option( 'ct_custom_fields' );
        delete_site_option( 'ct_flush_rewrite_rules' );
    }
}
register_deactivation_hook( __FILE__, 'dp_plugin_deactivate' );

?>