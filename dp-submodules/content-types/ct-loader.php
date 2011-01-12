<?php

/*
Submodule Name: Content Types
Description: Content Types - Custom Post, Taxonomy and Field Manager.
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

include_once 'ct-config.php';
include_once 'ct-core/ct-core.php';
include_once 'ct-admin-ui/ct-admin-ui-display-custom-fields.php';
include_once 'ct-admin-ui/ct-admin-ui-content-types.php';

/**
 * cp_load_plugin_textdomain()
 *
 * Loads "content_types-[xx_XX].mo" language file from the "ct-languages" directory
 */
function ct_load_plugin_textdomain() {
    $plugin_dir = CT_SUBMODULE_DIR . 'ct-languages';
    load_plugin_textdomain( 'content_types', null, $plugin_dir );
}
add_action( 'init', 'ct_load_plugin_textdomain', 0 );
