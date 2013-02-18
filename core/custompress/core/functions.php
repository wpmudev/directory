<?php
/**
* Forces a rewrite rules recalc on all blogs on Multisite
* format of uniq is "1 xxxxxxxxxxxxx' or '0 xxxxxxxxxxxxx' 1 if a hard flush is requested 
*/
if( ! function_exists('flush_network_rewrite_rules') ){
	function flush_network_rewrite_rules($hard = true){
		update_site_option('ct_flush_rewrite_rules', uniqid( ($hard ? '1 ' : '0 '), true));
	}
}

