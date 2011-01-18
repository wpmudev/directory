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

/* Define plugin version */ 
define ( 'DP_VERSION', '1.0.5' );
define ( 'DP_DB_VERSION', '1.1' );

/* define the plugin folder url */
define ( 'DP_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
/* define the plugin folder dir */
define ( 'DP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));

/* include core file */
include_once 'core/core.php';
include_once 'core/admin.php';
include_once 'core/data.php';

/* include payment PayPal Express payment gateway */
include_once 'modules/payments/loader.php';
/* include "Content Types" submodule */
include_once 'modules/content-types/loader.php';

?>