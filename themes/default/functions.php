<?php

define( 'THEME_TEXT_DOMAIN', 'directory' );

function init_localization( $locale ) {
    return "en_EN";
}
// Uncomment add_filter below to test your localization, make sure to enter the right language code.
// add_filter('locale','init_localization');

if( function_exists( 'load_theme_textdomain' ) ) {
    load_theme_textdomain( THEME_TEXT_DOMAIN, TEMPLATEPATH . '/languages/' );
}

include_once 'includes/core/theme-core.php';
include_once 'includes/core/theme-colors.php';
include_once 'includes/core/theme-options.php';
include_once 'includes/functions/loop-functions.php';
include_once 'includes/functions/theme-functions.php';

?>
