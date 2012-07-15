<?php
/**
* Directory Core Buddypress Class
**/

if ( !class_exists('Directory_Core_Buddypress') ):
class Directory_Core_Buddypress extends Directory_Core {

	/**
	* Constructor.
	*
	* @return void
	**/

	function Directory_Core_Buddypress() { __construct();}

	function __construct(){

		parent::__construct(); //Get the inheritance right

	}


}

/* Initiate Class */
if (!is_admin()) {
	global $Directory_Core;
	$Directory_Core = new Directory_Core_Buddypress();
}
endif;