<?php
/**
* Uninstall Directory plugin
* @package Directory
* @version 1.0.0
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit();

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
