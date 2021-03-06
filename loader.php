<?php
/*
Plugin Name: Directory
Plugin URI: http://premium.wpmudev.org/project/wordpress-directory
Description: Directory - Create full blown directory site.
Version: 2.2.6.5
Author: WPMU DEV
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

Authors - Ivan Shaovchev, Andrey Shipilov, Arnold Bailey
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
define( 'DR_VERSION', '2.2.6.5' );
// define the plugin folder url
define( 'DR_PLUGIN_URL', plugin_dir_url(__FILE__) );
// define the plugin folder dir
define( 'DR_PLUGIN_DIR', plugin_dir_path(__FILE__) );
// The text domain for strings localization
define( 'DR_TEXT_DOMAIN', 'dr_text_domain' );
// The key for the options array
define( 'DR_OPTIONS_NAME', 'dr_options' );
// The key for the captcha transient
define( 'DR_CAPTCHA', 'dr_captcha_' );

// include core files
//If another version of CustomPress not loaded, load ours.
if(!class_exists('CustomPress_Core')) include_once 'core/custompress/loader.php';

register_deactivation_hook( __FILE__, function() {
        //Remove Directory custom post types
        $ct_custom_post_types = get_site_option( 'ct_custom_post_types' );
        unset($ct_custom_post_types['directory_listing']);
        update_site_option( 'ct_custom_post_types', $ct_custom_post_types );
        
        $ct_custom_post_types = get_option( 'ct_custom_post_types' );
        unset($ct_custom_post_types['directory_listing']);
        update_option( 'ct_custom_post_types', $ct_custom_post_types );
        
        $ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
        unset($ct_custom_taxonomies['listing_tag']);
        update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
        
        $ct_custom_taxonomies = get_option('ct_custom_taxonomies');
        unset($ct_custom_taxonomies['listing_tag']);
        update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
        
        $ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
        unset($ct_custom_taxonomies['listing_category']);
        update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
        
        $ct_custom_taxonomies = get_option('ct_custom_taxonomies');
        unset($ct_custom_taxonomies['listing_category']);
        update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
        
        flush_rewrite_rules();

} );

include_once 'core/core.php';
include_once 'core/functions.php';
include_once 'core/template-tags.php';
include_once 'core/payments.php';
include_once 'core/ratings.php';
