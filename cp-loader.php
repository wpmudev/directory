<?php
/*
Plugin Name: CustomPress
Plugin URI: http://premium.wpmudev.org/project/custompress
Description: CustomPress - Custom Post, Taxonomy and Field Manager.
Version: 1.0.3
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
// error_reporting( E_ALL ^ E_NOTICE );
// ini_set( 'display_errors', 1 );


/* Define plugin version */ 
define ( 'CP_VERSION', '1.0.3' );
define ( 'CP_DB_VERSION', '1.1' );

/* include CustomPress files */
include_once 'cp-core/cp-core.php';
include_once 'cp-admin-ui/cp-admin-ui-settings.php';

/* include content types submodule */
include_once 'cp-submodules/content-types/ct-loader.php';

/**
 * cp_load_plugin_textdomain()
 *
 * Loads "custompress-[xx_XX].mo" language file from the "cp-languages" directory
 */
function cp_load_plugin_textdomain() {
    $plugin_dir = CP_PLUGIN_DIR . 'cp-languages';
    load_plugin_textdomain( 'custompress', null, $plugin_dir );
}
add_action( 'init', 'cp_load_plugin_textdomain', 0 );

/**
 * cp_loaded()
 *
 * Allow dependent plugins and core actions to attach themselves in a safe way.
 *
 * See cm-admin-core.php for the following core actions: cp_init
 */
function cp_loaded() {
	do_action( 'cp_loaded' );
}
add_action( 'init', 'cp_loaded', 20 );

/**
 * cp_loader_activate()
 * 
 * Update plugin version
 */
function cp_plugin_activate() {
	$options = array( 'version' => CP_VERSION, 'db_version' => CP_DB_VERSION );
	update_site_option( 'cp_options', $options );
}
register_activation_hook( __FILE__, 'cp_plugin_activate' );

/**
 * cp_plugin_deactivate()
 *
 * Deactivate plugin. If $flush_cp_data is set to "true"
 * all plugin data will be deleted
 */
function cp_plugin_deactivate() {

    // set this to "true" if you want to delete all of the plugin stored data
    $flush_cp_data = false;

    // if $flush_cp_data is true it will delete all plugin data
    if ( $flush_cp_data ) {
        delete_site_option( 'cp_options' );
        delete_site_option( 'cp_main_settings' );
        delete_site_option( 'ct_custom_post_types' );
        delete_site_option( 'ct_custom_taxonomies' );
        delete_site_option( 'ct_custom_fields' );
        delete_site_option( 'ct_flush_rewrite_rules' );
    }
}
register_deactivation_hook( __FILE__, 'cp_plugin_deactivate' );

?>