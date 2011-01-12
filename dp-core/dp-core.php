<?php

/* Register DirectoryPress themes contained within the dp-themes folder */
if ( function_exists( 'register_theme_directory' ))
	register_theme_directory( DP_PLUGIN_DIR . 'dp-themes' );

/**
 * Add custom role for DirectoryPress members
 * 
 */
function dp_roles() {
    global $wp_roles;

    if ( $wp_roles ) {
        $wp_roles->add_role( 'dpmember', 'DirectoryPress Member', array(
            'read'                      => 1,
            'read_listing'              => 1,
            'edit_others_listings'      => 0,
            'edit_listing'              => 1,
            'edit_listings'             => 1,
            'publish_listings'          => 1,
            'delete_listing'            => 0,
            'upload_files'              => 1,
            'assign_terms'              => 1,
        ));
        $wp_roles->add_cap( 'administrator', 'read_listing' );
        $wp_roles->add_cap( 'administrator', 'edit_others_listings' );
        $wp_roles->add_cap( 'administrator', 'edit_listing' );
        $wp_roles->add_cap( 'administrator', 'edit_listings' );
        $wp_roles->add_cap( 'administrator', 'publish_listings' );
        $wp_roles->add_cap( 'administrator', 'delete_listing' );
    }
}
add_action( 'init', 'dp_roles', 20 );

/**
 * dp_core_admin_menu()
 *
 * Register all admin menues.
 */
function dp_core_admin_menu() {
	add_menu_page( __('DirectoryPress', 'directorypress'), __('DirectoryPress', 'directorypress'), 'edit_users', 'dp_main', 'dp_core_load_admin_ui' );
    add_submenu_page( 'dp_main', __('Settings', 'directorypress'), __('Settings', 'directorypress'), 'edit_users', 'dp_main', 'dp_core_load_admin_ui' );
}
add_action( 'admin_menu', 'dp_core_admin_menu' );

/**
 * dp_core_hook()
 *
 * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
 */
function dp_core_hook() {
	if ( function_exists( 'get_plugin_page_hook' ))
		$hook = get_plugin_page_hook( $_GET['page'], 'dp_main' );
	else
		$hook = 'directorypress' . '_page_' . $_GET['page']; /** @todo Make hook plugin specific */

    add_action( 'admin_print_styles-' .  $hook, 'dp_core_enqueue_styles' );
    add_action( 'admin_print_scripts-' . $hook, 'dp_core_enqueue_scripts' );

    /** @deprecated
    if ( $_GET['page'] == 'dp_main' )
        add_action( 'admin_head-' . $hook, 'dp_ajax_actions');
    */
}
add_action( 'admin_init', 'dp_core_hook' );


/**
 * dp_core_enqueue_styles()
 *
 * Load styles on plugin admin pages only.
 */
function dp_core_enqueue_styles() {
    wp_enqueue_style( 'dp-admin-styles',
                       DP_PLUGIN_URL . 'dp-admin-ui/css/dp-admin-ui-styles.css');
}

/**
 * dp_core_enqueue_scripts()
 *
 * Load scripts on plugin specific admin pages only.
 */
function dp_core_enqueue_scripts() {
    wp_enqueue_script( 'dp-admin-scripts',
                        DP_PLUGIN_URL . 'dp-admin-ui/js/dp-admin-ui-scripts.js',
                        array( 'jquery' ) );
}

/**
 * dp_core_load_admin_ui()
 * 
 * Loads admin page templates based on $_GET request values and passes variables.
 */
function dp_core_load_admin_ui() {
    include_once DP_PLUGIN_DIR . 'dp-admin-ui/dp-admin-ui-main.php';

    // load settings ui
    if ( $_GET['page'] == 'dp_main' )
        dp_admin_ui_main();
}
/**
 * dp_core_process_paypal_express_settings()
 * 
 */
function dp_core_process_paypal_express_settings() {

    // stop execution and return if no request is made
    if ( !isset( $_POST['dp_submit_paypal_express_settings'] ))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['dp_submit_paypal_express_settings_secret'], 'dp_submit_paypal_express_settings_verify'))
        return;

    $post_options = array( 'paypal' => array(
        'api_url'       => $_POST['paypal_express_url'],
        'api_username'  => $_POST['paypal_express_api_username'],
        'api_password'  => $_POST['paypal_express_api_password'],
        'api_signature' => $_POST['paypal_express_api_signature'],
        'currency_code' => $_POST['paypal_express_currency_code']
    ));

    $old_options = get_site_option( 'dp_options' );
    $new_options = array_merge( $old_options, $post_options );

	update_site_option( 'dp_options', $new_options );

}
add_action( 'init', 'dp_core_process_paypal_express_settings' );

/**
 *
 * @global <type> $wp_rewrite
 * @param <type> $query
 * @return <type> 
 */
function dp_query_filter( $query ) {
    global $wp_rewrite;
    $query->query_vars['dp_page'] = 'top';

    dp_debug( $wp_rewrite, $query );

    return $query;
}
//add_filter( 'pre_get_posts', 'dp_query_filter' );

/**
 * dp_create_rewrite_rules()
 * 
 * @global <type> $wp_rewrite
 * @return <type>
 */
function dp_create_rewrite_rules() {
	global $wp_rewrite;

	// add rewrite tokens
	$keytag = '%dp_signup%';
	$wp_rewrite->add_rewrite_tag( $keytag, '(.+?)', 'dp_signup=' );

	$keywords_structure = $wp_rewrite->root . $wp_rewrite->front . "/{$keytag}/";
    
	$keywords_rewrite = $wp_rewrite->generate_rewrite_rules($keywords_structure);

	$wp_rewrite->rules = $keywords_rewrite + $wp_rewrite->rules;
	return $wp_rewrite->rules;
}
//add_action('generate_rewrite_rules', 'createRewriteRules');

/**
 * dp_add_signup_page()
 *
 * Add neccessary pages.
 */
function dp_add_pages() {
    
    if ( !get_page_by_title( 'Submit Listing' )) {
        $args = array(
            'post_title' => 'Submit Listing',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'ping_status' => 'closed',
            'comment_status' => 'closed');

        $post_id = wp_insert_post( $args );
        update_post_meta( $post_id , '_wp_page_template', 'submit-listing.php' );

        $old_options = get_site_option( 'dp_options' );
        $id_record   = array( 'submit_page_id' => $post_id );
        $new_options = array_merge( $old_options, $id_record );
        update_site_option( 'dp_options', $new_options );
    }

}
add_action( 'init', 'dp_add_pages' );

/**
 * dp_insert_user()
 * 
 * @param <type> $email
 * @param <type> $first_name
 * @param <type> $last_name
 * @return <type>
 */
function dp_insert_user( $email, $first_name, $last_name, $billing ) {

    require_once( ABSPATH . WPINC . '/registration.php' );

    // variables
    $user_login     = sanitize_user( strtolower( $first_name ));
    $user_email     = $email;
    $user_pass      = wp_generate_password();

    if ( username_exists( $user_login ) )
        $user_login .= '-' . sanitize_user( strtolower( $last_name ));

    if ( username_exists( $user_login ) )
        $user_login .= rand(1,9);

    if ( email_exists( $user_email )) {
        $user = get_user_by( 'email', $user_email );
        
        if ( $user ) {
            wp_update_user( array ('ID' => $user->ID, 'role' => 'dpmember' )) ;
            update_user_meta( $user->ID, 'dp_billing', $billing );
            $credentials = array( 'remember'=>true, 'user_login' => $user->user_login, 'user_password' => $user->user_pass );
            wp_signon( $credentials );
            return;
        }
    }

    $user_id = wp_insert_user( array( 'user_login'   => $user_login,
                                      'user_pass'    => $user_pass,
                                      'user_email'   => $email,
                                      'display_name' => $first_name . ' ' . $last_name,
                                      'first_name'   => $first_name,
                                      'last_name'    => $last_name,
                                      'role'         => 'dpmember'
                                    )) ;

    update_user_meta( $user_id, 'dp_billing', $billing );
    wp_new_user_notification( $user_id, $user_pass );
    $credentials = array( 'remember'=>true, 'user_login' => $user_login, 'user_password' => $user_pass );
    wp_signon( $credentials );
}

/**
 *
 * @return <type> 
 */
function dp_set_billing_type() {
    if ( !isset( $_POST['payment_method_submit'] ))
        return;
    
    $_SESSION['billing'] = $_POST['billing'];
}
add_action('init', 'dp_set_billing_type');

/**
 * 
 */
function dp_redirect_after_payment() {

    if ( isset( $_REQUEST['redirect_admin_profile'] )) {
        wp_redirect( admin_url( 'profile.php' ));
        exit();
    }

    if ( isset( $_REQUEST['redirect_admin_listings'] )) {
        wp_redirect( admin_url( 'post-new.php?post_type=directory_listing' ));
        exit();
    }   
}
add_action( 'init', 'dp_redirect_after_payment' );

/**
 * dp_core_init_submit_site_settings()
 * 
 * @return <type>
 */
function dp_core_init_submit_site_settings() {
    
    $old_options = get_site_option( 'dp_options' );

    if ( empty( $old_options['submit_site_settings'] )) {
        $submit_site_settings = array( 'submit_site_settings' => array(
            'annual_price'   => 10,
            'annual_txt'     => 'Annually Recurring Charge',
            'one_time_price' => 50,
            'one_time_txt'   => 'One-Time Only Charge',
            'tos_txt'        => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at sem libero. Pellentesque accumsan consequat porttitor. Curabitur ut lorem sed ipsum laoreet tempus at vel erat. In sed tempus arcu. Quisque ut luctus leo. Nulla facilisi. Sed sodales lectus ut tellus venenatis ac convallis metus suscipit. Vestibulum nec orci ut erat ultrices ullamcorper nec in lorem. Vivamus mauris velit, vulputate eget adipiscing elementum, mollis ac sem. Aliquam faucibus scelerisque orci, ut venenatis massa lacinia nec. Phasellus hendrerit lorem ornare orci congue elementum. Nam faucibus urna a purus hendrerit sit amet pulvinar sapien suscipit. Phasellus adipiscing molestie imperdiet. Mauris sit amet justo massa, in pellentesque nibh. Sed congue, dolor eleifend egestas egestas, erat ligula malesuada nulla, sit amet venenatis massa libero ac lacus. Vestibulum interdum vehicula leo et iaculis.'
        ));

        $new_options = array_merge( $old_options, $submit_site_settings );
        update_site_option( 'dp_options', $new_options );

        return;
    }

    // stop execution and return if no request is made
    if ( !isset( $_POST['dp_submit_site_settings'] ))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['dp_submit_settings_submit_site_secret'], 'dp_submit_settings_submit_site_verify'))
        return;

    $submit_site_settings = array( 'submit_site_settings' => array(
        'annual_price'   => $_POST['annual_payment_option_price'],
        'annual_txt'     => $_POST['annual_payment_option_txt'],
        'one_time_price' => $_POST['one_time_payment_option_price'],
        'one_time_txt'   => $_POST['one_time_payment_option_txt'],
        'tos_txt'        => $_POST['tos_txt']
    ));
    
    $new_options = array_merge( $old_options, $submit_site_settings );

    update_site_option( 'dp_options', $new_options );
}
add_action( 'init', 'dp_core_init_submit_site_settings' );

/**
 *
 * @return <type> 
 */
function dp_core_process_ads_settings() {
    if ( !isset( $_POST['dp_submit_ads_settings'] ))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['dp_submit_settings_ads_secret'], 'dp_submit_settings_ads_verify'))
        return;

    $old_options = get_site_option( 'dp_options' );
    $ads         = array( 'ads' => array( 'h_ad' => stripslashes( $_POST['h_ad_code'] )));
    $new_options = array_merge( $old_options, $ads );
    update_site_option( 'dp_options', $new_options );
}
add_action( 'init', 'dp_core_process_ads_settings' );


/**
 *
 * @return <type> 
 */
function dp_login_user() {

    $credentials = array( 'remember'=>true, 'user_login' => $_POST['username'], 'user_password' => $_POST['password'] );
    $result = wp_signon( $credentials );
    
    if ( isset( $result->errors )) {
        add_action( 'login_invalid', 'dp_invalid_class' );
        return $result;
    }
    
    wp_safe_redirect( get_bloginfo( 'url' ));
    exit();
}

/**
 *
 * @param <type> $field
 * @return <type> 
 */
function dp_validate_field( $field ) {

    if ( $field == 'tos_agree' ) {
        if ( empty( $_POST['tos_agree'] )) {
            add_action( 'tos_agree_invalid', 'dp_invalid_class' );
            return false;   
        } else {
            return true;
        }
    }

    if ( $field == 'billing' ) {
        if ( empty( $_POST['billing'] )) {
            add_action( 'billing_invalid', 'dp_invalid_class' );
            return false;
        } else {
            return true;
        }
    }
}

/**
 * dp_invalid_login()
 */
function dp_invalid_login() {
    echo 'class="dp-error"';
    
}

/**
 * dp_invalid_class()
 */
function dp_invalid_class() {
    echo 'class="dp-error"';
}

function dp_checkout_submit_paypal() {

    if ( !isset( $_POST['payment_method_submit'] ))
        return;

    if ( $_POST['payment_method'] == 'paypal' )
        dp_geteway_paypal_express_call_checkout( $_POST['cost'] );
}
add_action( 'init', 'dp_checkout_submit_paypal' );

/**
 * dp_init()
 *
 * Allow components to initialize themselves cleanly
 */
function dp_init() {
	do_action( 'dp_init' );
}
add_action( 'dp_loaded', 'dp_init' );

?>