<?php
/*
Plugin Name: Directory
Plugin URI: http://premium.wpmudev.org/project/directory
Description: Directory - Create full blown directory site.
Version: 1.0.5
Author: Ivan Shaovchev (Incsub)
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
//  error_reporting( E_ALL ^ E_NOTICE );
//  ini_set( 'display_errors', 1 );

/* tmp debug func */
function dp_debug( $param ) {
    echo '<pre>';
    print_r( (array) $param );
    echo '</pre>';
}

/* Define plugin version */ 
define ( 'DP_VERSION', '1.0.5' );
define ( 'DP_DB_VERSION', '1.1' );

/* define the plugin folder url */
define ( 'DP_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
/* define the plugin folder dir */
define ( 'DP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));

/* include core file */
include_once 'dp-core/dp-core.php';
include_once 'dp-core/dp-core-checkout.php';
include_once 'dp-core/dp-core-load-data.php';

/* include payment PayPal Express payment gateway */
include_once 'dp-gateways/dp-gateways-paypal-express-core.php';

/* include "Content Types" submodule */
include_once 'dp-submodules/content-types/loader.php';

/**
 * dp_load_plugin_textdomain()
 *
 * Loads "custompress-[xx_XX].mo" language file from the "dp-languages" directory
 */
function dp_load_plugin_textdomain() {
    $plugin_dir = DP_PLUGIN_DIR . 'dp-languages';
    load_plugin_textdomain( 'directory', null, $plugin_dir );
}
add_action( 'init', 'dp_load_plugin_textdomain', 0 );

/**
 * dp_loaded()
 *
 * Allow dependent plugins and core actions to attach themselves in a safe way.
 *
 * See dp-core.php for the following core actions: dp_init
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

    $options  = get_site_option( 'dp_options' );
    $versions = array( 'versions' => array( 'dp_version' => DP_VERSION, 'dp_db_version' => DP_DB_VERSION ));

    if ( !isset( $options['versions'] )) {
        update_site_option( 'dp_options', $versions );
    } else {
        $options = array_merge( $options, $versions );
        update_site_option( 'dp_options', $options );
    }
    
    switch_theme( 'dp-default', 'dp-default' );
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
    $flush_dp_data = true;

    // if $flush_dp_data is true it will delete all plugin data
    if ( $flush_dp_data ) {
        delete_site_option( 'dp_options' );
        delete_site_option( 'ct_custom_post_types' );
        delete_site_option( 'ct_custom_taxonomies' );
        delete_site_option( 'ct_custom_fields' );
        delete_site_option( 'ct_flush_rewrite_rules' );
        delete_option( 'dir_colors' );

        $options = get_site_option( 'dp_options' );
        wp_delete_post( $options['submit_page_id'], true );
    }

    switch_theme('twentyten', 'twentyten' );
}
register_deactivation_hook( __FILE__, 'dp_plugin_deactivate' );

/**
 *
 * @param <type> $links
 * @return <type> 
 */
function dp_plugin_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=dp_main&dp_settings=main&dp_gen">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter( "plugin_action_links_$plugin", 'dp_plugin_settings_link' );

?>