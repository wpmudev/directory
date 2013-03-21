<?php
/*
Plugin Name: Directory
Plugin URI: http://premium.wpmudev.org/project/wordpress-directory
Description: Directory - Create full blown directory site.
Version: 2.2.2.8
Author: Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)
Author URI: http://premium.wpmudev.org
Text Domain: dr_text_domain
Domain Path: /languages
WDP ID: 164
License: GNU General Public License (Version 2 - GPLv2)
*/

$plugin_header_translate = array(    
__('Directory - Create full blown directory site.', 'dr_text_domain'),    
__('Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)', 'dr_text_domain'),    
__('http://premium.wpmudev.org', 'dr_text_domain'),    
__('Directory', 'dr_text_domain'),
);

/*
Copyright 2012 Incsub, (http://incsub.com)

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
define( 'DR_VERSION', '2.2.2.8' );
// define the plugin folder url
define( 'DR_PLUGIN_URL', plugin_dir_url(__FILE__) );
// define the plugin folder dir
define( 'DR_PLUGIN_DIR', plugin_dir_path(__FILE__) );
// The text domain for strings localization
define( 'DR_TEXT_DOMAIN', 'dr_text_domain' );
// The key for the options array
define( 'DR_OPTIONS_NAME', 'dr_options' );

// include core files
include_once 'core/wpmudev-dash-notification.php';
//If another version of CustomPress not loaded, load ours.
if(!class_exists('CustomPress_Core')) include_once 'core/custompress/loader.php';

include_once 'core/core.php';
include_once 'core/functions.php';
include_once 'core/template-tags.php';
include_once 'core/payments.php';
include_once 'core/ratings.php';



