<?php
/*
Plugin Name: WPMU Assist
Plugin URI: http://premium.wpmudev.org/project/classifieds
Description: Allows single blog registration on Multisite. On registering from a blog other than the main site it saves the blog_id and sends the emails as the originating blog and on activation adds the user to the originating blog as well as the main blog.
Version: 1.0
Author: Arnold Bailey (Incsub)
Author URI: http://premium.wpmudev.org
License: GNU General Public License (Version 2 - GPLv2)
Network: true
*/


if(! class_exists('WPMU_Assist') ):

class WPMU_Assist{

	public $plugin_dir;

	function __construct(){
		if(is_multisite()){
			//Intercept wpmu_signup so user can be added to the
			add_filter('wpmu_signup_user_notification', array(&$this, 'on_signup_user'), 10, 4);
			add_filter('wpmu_signup_blog_notification', array(&$this, 'on_signup_blog'), 10, 7);
			add_filter('wpmu_activate_user', array(&$this, 'on_activate_user'), 10, 3);
			add_filter('wpmu_activate_blog', array(&$this, 'on_activate_blog'), 10, 5);
			add_filter('wp_loaded', array(&$this, 'on_template_redirect') );

			$this->plugin_dir = plugin_dir_path(__FILE__);

		}
	}

/**
* Traps the blog id that a wp-login.php registration is coming from so the user can be added to the blog.
* For a wp-signup registration you must set the transient in the calling form.
*/
	function on_template_redirect(){
		global $blog_id;

		//Save the incoming $blog_id for wp-signup.php
		if($_SERVER['SCRIPT_NAME'] == '/wp-signup.php'){
			if($blog_id > 1) set_site_transient('register_blog_id_'.$_SERVER['REMOTE_ADDR'], $blog_id, 60 * 60 );
		}
	}

	function on_signup_user($user, $user_email, $key, $meta){
		global $wpdb;

		// Get the saved $blog_id

		$blogid = get_site_transient('register_blog_id_'.$_SERVER['REMOTE_ADDR']);
		$blogid = empty($blogid) ? 1 : $blogid;
		delete_site_transient('register_blog_id_'.$_SERVER['REMOTE_ADDR']);

		$meta = maybe_unserialize($meta);

		if($blogid > 1){
			$meta = serialize(array_merge( $meta, array('register_blog_id' => $blogid) ) ); // Save the originating blog id
			$wpdb->update( $wpdb->signups, array('meta' => $meta ), array('activation_key' => $key) ); // update the db

			switch_to_blog($blogid); //So it will send mail under the blogs domain
		}
		return true;
	}

	function on_signup_blog( $domain, $path, $title, $user, $user_email, $key, $meta){
		return $this->on_signup_user($user, $user_email, $key, $meta);
	}

	function on_activate_user($user_id, $password, $meta){
		$default_role = get_option('default_role');
		$blogid = empty($meta['register_blog_id']) ? 1 : intval($meta['register_blog_id']);
		add_user_to_blog( 1, $user_id, $default_role);
		add_user_to_blog($blogid, $user_id, $default_role);
	}

	function on_activate_blog($blog_id, $user_id, $password, $title, $meta){
		$this->on_activate_user($user_id, $password, $meta);
	}

}

new WPMU_Assist;

endif;
