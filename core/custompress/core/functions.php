<?php
/**
* Forces a rewrite rules recalc on all blogs on Multisite
* format of uniq is "1 xxxxxxxxxxxxx' or '0 xxxxxxxxxxxxx' 1 if a hard flush is requested
* Sets flag so flush is only called once even if flush_network_rewrite_rules is called multiple times
*/
if( ! function_exists('flush_network_rewrite_rules') ):

function flush_network_rewrite_rules($hard = true, $log = 'Unknown'){
	
	if(is_multisite() ){
		update_site_option('ct_flush_rewrite_rules', uniqid( ($hard ? '1 ' : '0 '), true));
	} else {
		update_option('ct_flush_rewrite_rules', uniqid( ($hard ? '1 ' : '0 '), true));
	}
	flush_rewrite_rules( $hard );
}

endif;

if( ! function_exists('write_to_log') ):

function write_to_log($error, $log = 'flush_rewrite_rules') {

	//create filename for each month
	$filename = CPT_PLUGIN_DIR . "{$log}_" . date('Y_m') . '.log';

	//add timestamp to error
	$message = gmdate('[Y-m-d H:i:s] ') . $error;

	//write to file
	file_put_contents($filename, $message . "\n\n", FILE_APPEND);
}

endif;
