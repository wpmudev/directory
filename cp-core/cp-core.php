<?php

/* define the plugin folder url */
define ( 'CP_PLUGIN_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__DIR__), '', plugin_basename(__DIR__) ));
/* define the plugin folder dir */
define ( 'CP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__DIR__), '', plugin_basename(__DIR__) ));

/**
 * cp_core_admin_menu()
 *
 * Register all admin menues.
 */
function cp_core_admin_menu() {
	add_menu_page( __('CustomPress', 'custompress'), __('CustomPress', 'custompress'), 'edit_users', 'cp_main', 'cp_core_load_admin_ui' );
    add_submenu_page( 'cp_main', __('Settings', 'custompress'), __('Settings', 'custompress'), 'edit_users', 'cp_main', 'cp_core_load_admin_ui' );
}
add_action( 'admin_menu', 'cp_core_admin_menu' );

/**
 * cp_core_hook()
 *
 * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
 */
function cp_core_hook() {
	if ( function_exists( 'get_plugin_page_hook' ))
		$hook = get_plugin_page_hook( $_GET['page'], 'cp_main' );
	else
		$hook = 'custom-manager' . '_page_' . $_GET['page']; /** @todo Make hook plugin specific */

    /** @todo CustomPress specific hook. Move to CustomPress package. */
    if ( $_GET['page'] == 'cp_main' )
        add_action( 'admin_head-' . $hook, 'cp_ajax_actions');
}
add_action( 'admin_init', 'cp_core_hook' );

/**
 * cp_core_load_admin_ui()
 * 
 * Loads admin page templates based on $_GET request values and passes variables.
 */
function cp_core_load_admin_ui() {
    
    // load CustomPress settings ui
    if ( $_GET['page'] == 'cp_main' ) {
        $post_types = get_site_option( 'ct_custom_post_types' );
        cp_admin_ui_settings( $post_types );
    } 
}

/**
 * cp_admin_process_update_settings()
 * 
 * Saves the main plugin settings.
 */
function cp_process_update_settings() {

    // check whether form is submited 
    if ( !isset( $_POST['cp_submit_settings'] ))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['cp_submit_settings_secret'], 'cp_submit_settings_verify' ))
        return;
    
    $args = array(
        'page'      => 'home',
        'post_type' => $_POST['post_type']
    );
    
    cp_create_post_type_files( $_POST['post_type_file'] );

    if ( !get_site_option( 'cp_main_settings' )) {
        $new_settings = array( $args['page'] => $args );
    } else {
        $old_settings = get_site_option( 'cp_main_settings' );
        $new_settings = array_merge( $old_settings, array( $args['page'] => $args ));
    }
    
    update_site_option('cp_main_settings', $new_settings );
}
add_action( 'init', 'cp_process_update_settings' );

/**
 * cp_display_custom_post_types()
 *
 * Display custom post types on home page.
 *
 * @param object $query
 * @return object $query
 *
 * @todo Resolve bug with query_posts resetings the is_home() function.
 */
function cp_display_custom_post_types( $query ) {
    $settings = get_site_option('cp_main_settings');

	if ( is_home() && !in_array( 'default', $settings['home']['post_type'] ) && false == $query->query_vars['suppress_filters'] )
		$query->set( 'post_type', $settings['home']['post_type'] );

	return $query;

    /** @todo Resolve bug with is_home reset for pages
    global $post;
    wp_reset_query();
    if ( is_home() && !in_array( 'default', $settings['home']['post_type'] ))
        query_posts( array( 'post_type' => $settings['home']['post_type'] ));
    elseif ( $settings[$post->post_name]['page'] == $post->post_name )
        query_posts( array( 'post_type' => $settings[$post->post_name]['post_type'] ));
     */
}
add_filter( 'pre_get_posts', 'cp_display_custom_post_types' );
// @todo
// add_action( 'template_redirect', 'cp_display_custom_post_types', 10 );

/**
 * cp_ajax_actions()
 *
 * Make AJAX POST request for getting the post type info associated with
 * a particular page.
 */
function cp_ajax_actions() { ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {
            // bind event to function
            $(window).bind('load', cp_ajax_post_process_request);
            //$('#cp-select-page').bind('change', cp_ajax_post_process_request)

            function cp_ajax_post_process_request() {
                // clear attributes 
                //$('.cp-main input[name="post_type[]"]').attr( 'checked', false );
                // assign variables
                var data = {
                    action: 'cp_get_post_types',
                    cp_ajax_page_name: 'home'
                    //@todo
                    //cp_ajax_page_name: $('#cp-select-page option:selected').val()
                };
                // make the post request and process the response
                $.post(ajaxurl, data, function(response) {
                    $.each(response, function(i,item) {
                        $('.cp-main input[name="post_type[]"][value="' + item + '"]').attr( 'checked', true );
                    });
                });
            }
        });
    </script> <?php
}

/**
 * cp_ajax_action_callback()
 *
 * Ajax callback which gets the post types associated with each page.
 */
function cp_ajax_action_callback() {

	$page_name = $_POST['cp_ajax_page_name'];
    $settings  = get_site_option('cp_main_settings');

    // json encode the response
    $response = json_encode( $settings[$page_name]['post_type'] );
    //$settings[$page_name]['page'] => $settings[$page_name]['post_type']

	// response output
	header( "Content-Type: application/json" );
    
	echo $response;
	die();
}
add_action('wp_ajax_cp_get_post_types', 'cp_ajax_action_callback');

/**
 * cp_create_post_type_files()
 *
 * Create a copy of the single.php file with the post type name added
 *
 * @param string $post_type
 */
function cp_create_post_type_files( $post_type ) {
    $file = TEMPLATEPATH . '/single.php';

    if ( !empty( $post_type )) {
        foreach ( $post_type as $post_type ) {
            $newfile = TEMPLATEPATH . '/single-' .  $post_type . '.php';

            if ( !file_exists( $newfile )) {
                if ( !copy( $file, $newfile ))
                    echo "failed to copy $file...\n";
            }
        }
    }
}

/**
 * cp_init()
 *
 * Allow components to initialize themselves cleanly
 */
function cp_init() {
	do_action( 'cp_init' );
}
add_action( 'cp_loaded', 'cp_init' );

/* tmp debug func */
function cp_debug( $param ) {
    echo '<pre>';
    print_r ( $param );
    echo '</pre>';
}

?>