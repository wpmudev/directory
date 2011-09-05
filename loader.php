<?php
/*
Plugin Name: Directory
Plugin URI: http://premium.wpmudev.org/project/directory
Description: Directory - Create full blown directory site.
Version: 2.0.0 Beta 3
Author: Ivan Shaovchev, Andrey Shipilov (Incsub)
Author URI: http://premium.wpmudev.org
WDP ID: 164
License: GNU General Public License (Version 2 - GPLv2)
*/

/*
Copyright 2011 Incsub, (http://incsub.com)

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

// Define plugin version
define( 'DR_VERSION', '2.0.0' );
// define the plugin folder url
define( 'DR_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
// define the plugin folder dir
define( 'DR_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
// The text domain for strings localization
define( 'DR_TEXT_DOMAIN', 'dr_text_domain' );
// The key for the options array
define( 'DR_OPTIONS_NAME', 'dr_options' );

// include core files
include_once 'core/core.php';
include_once 'core/functions.php';
include_once 'core/template-tags.php';
include_once 'core/payments.php';
include_once 'core/ratings.php';

if ( is_admin() )
	include_once 'core/admin.php';

// notice
if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );
	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'install_plugins' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}

?>
