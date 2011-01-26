<?php

////////////////////////////////////////////////////////////////////////////////
// load text domain
////////////////////////////////////////////////////////////////////////////////

define('TEMPLATE_DOMAIN', 'directory');

define('DEVLIB', TEMPLATEPATH . '/library');

function init_localization( $locale ) {
return "en_EN";
}
// Uncomment add_filter below to test your localization, make sure to enter the right language code.
// add_filter('locale','init_localization');

if( function_exists( 'load_theme_textdomain' ) ) {
load_theme_textdomain(TEMPLATE_DOMAIN, DEVLIB . '/languages/');
}

//require_once(DEVLIB . '/functions/custom-header.php');
require_once(DEVLIB . '/functions/custom-functions.php');
require_once(DEVLIB . '/functions/option-functions.php');
require_once(DEVLIB . '/functions/loop-functions.php');
require_once(DEVLIB . '/functions/theme-core.php');
require_once(DEVLIB . '/functions/theme-functions.php');

?>