<?php

/**
 * ct_core_admin_menu()
 *
 * Register submodule submenue
 */
function ct_core_admin_menu() {
    add_submenu_page( CT_SUBMENU_PARENT_SLUG, __('Content Types', 'content_types'), __('Content Types', 'content_types'), 'edit_users', 'ct_content_types', 'ct_core_load_admin_ui' );
}
add_action( 'admin_menu', 'ct_core_admin_menu' );

/**
 * ct_core_hook()
 *
 * Get page hook and hook ct_core_enqueue_styles() and ct_core_enqueue_scripts() to it.
 */
function ct_core_hook() {
	if ( function_exists( 'get_plugin_page_hook' ))
		$hook = get_plugin_page_hook( $_GET['page'], CT_SUBMENU_PARENT_SLUG );
	else
		$hook = 'custom-manager' . '_page_' . $_GET['page']; /** @todo Make hook plugin specific */

	add_action( 'admin_print_styles-' .  $hook, 'ct_core_enqueue_styles' );
    add_action( 'admin_print_scripts-' . $hook, 'ct_core_enqueue_scripts' );
}
add_action( 'admin_init', 'ct_core_hook' );

/**
 * ct_core_enqueue_styles()
 *
 * Load styles on plugin admin pages only.
 */
function ct_core_enqueue_styles() {
    wp_enqueue_style( 'ct-admin-styles',
                       CT_SUBMODULE_URL . 'ct-admin-ui/css/ct-admin-ui-styles.css');
}

/**
 * ct_core_enqueue_custom_field_styles()
 *
 * Load styles for "Custom Fields" on add/edit post type pages only.
 */
function ct_core_enqueue_custom_field_styles() {
    wp_enqueue_style( 'ct-admin-custom-field-styles',
                       CT_SUBMODULE_URL . 'ct-admin-ui/css/ct-admin-ui-custom-field-styles.css');
}
add_action( 'admin_print_styles-post.php', 'ct_core_enqueue_custom_field_styles' );
add_action( 'admin_print_styles-post-new.php', 'ct_core_enqueue_custom_field_styles' );

/**
 * ct_core_enqueue_scripts()
 *
 * Load scripts on plugin specific admin pages only.
 */
function ct_core_enqueue_scripts() {
    wp_enqueue_script( 'ct-admin-scripts',
                        CT_SUBMODULE_URL . 'ct-admin-ui/js/ct-admin-ui-scripts.js',
                        array( 'jquery' ) );
}

/**
 * ct_core_load_admin_ui()
 * 
 * Loads admin page templates based on $_GET request values and passes variables.
 */
function ct_core_load_admin_ui() {  
    // load content type dispatcher ( which loads individual content type uis )
    if ( $_GET['page'] == 'ct_content_types' )
        ct_admin_ui_content_types();
}

/**
 * ct_admin_page_redirect()
 *
 * Hooks itself after the add/update/delete functions and redirects to the
 * appropriate pages.
 *
 * @global bool $ct_redirect Switch turning redirect on/off
 */
function ct_admin_page_redirect() {
    global $ct_redirect;

    // after post type is added redirect back to the post types page
    if ( isset( $_POST['ct_submit_add_post_type'] ) && $ct_redirect )
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type' ));

    // after post type is edited/deleted redirect back to the post types page
    if ( isset( $_POST['ct_submit_update_post_type'] ) || isset( $_REQUEST['ct_delete_post_type_secret'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type' ));

    // redirect to add post type page
    if ( isset( $_POST['ct_redirect_add_post_type'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=post_type&ct_add_post_type=true' ));

    // after taxonomy is added redirect back to the taxonomies page
    if ( isset( $_POST['ct_submit_add_taxonomy'] ) && $ct_redirect )
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy' ));

    // after taxonomy is edited/deleted redirect back to the taxonomies page
    if ( isset( $_POST['ct_submit_update_taxonomy'] ) || isset( $_REQUEST['ct_delete_taxonomy_secret'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy' ));

    // redirect to add taxonomy page
    if ( isset( $_POST['ct_redirect_add_taxonomy'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=taxonomy&ct_add_taxonomy=true' ));

    // after custom field is added redirect to custom fields page
    if ( isset( $_POST['ct_submit_add_custom_field'] ) && $ct_redirect )
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field' ));

    // after custom field add/update/deleted redirect back to the custom fields page 
    if ( isset( $_POST['ct_submit_update_custom_field'] ) || isset( $_REQUEST['ct_delete_custom_field_secret'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field' ));

    // redirect to add custom field page
    if ( isset( $_POST['ct_redirect_add_custom_field'] ))
        wp_redirect( admin_url( 'admin.php?page=ct_content_types&ct_content_type=custom_field&ct_add_custom_field=true' ));
}
add_action( 'init', 'ct_admin_page_redirect', 10 );

/**
 * ct_admin_process_add_update_post_type_requests()
 *
 * Intercept $_POST request and processes the custom post type submissions.
 *
 * @global bool $ct_redirect
 */
function ct_admin_process_add_update_post_type_requests() {
    global $ct_redirect;

    // stop execution and return if no add/update request is made
    if ( !( isset( $_POST['ct_submit_add_post_type'] ) || isset( $_POST['ct_submit_update_post_type'] )))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['ct_submit_post_type_secret'], 'ct_submit_post_type_verify'))
        return;

    // validate input fields and set redirect bool
    if ( isset( $_POST['ct_submit_add_post_type'] )) {
        if ( ct_validate_field( 'post_type', $_POST['post_type'] )) {
            $ct_redirect = true;
        } else {
            $ct_redirect = false;
            return;
        }
    }

    $post_type = ( isset( $_POST['post_type'] )) ? $_POST['post_type'] : $_GET['ct_edit_post_type'];

    $labels = array(
        'name'               => $_POST['labels']['name'],
        'singular_name'      => $_POST['labels']['singular_name'],
        'add_new'            => $_POST['labels']['add_new'],
        'add_new_item'       => $_POST['labels']['add_new_item'],
        'edit_item'          => $_POST['labels']['edit_item'],
        'new_item'           => $_POST['labels']['new_item'],
        'view_item'          => $_POST['labels']['view_item'],
        'search_items'       => $_POST['labels']['search_items'],
        'not_found'          => $_POST['labels']['not_found'],
        'not_found_in_trash' => $_POST['labels']['not_found_in_trash'],
        'parent_item_colon'  => $_POST['labels']['parent_item_colon']
    );
    $args = array(
        'labels'              => $labels,
        'supports'            => $_POST['supports'],
        'capability_type'     => 'post',
        'description'         => $_POST['description'],
        'menu_position'       => (int)  $_POST['menu_position'],
        'public'              => (bool) $_POST['public'] ,
        'show_ui'             => (bool) $_POST['show_ui'],
        'show_in_nav_menus'   => (bool) $_POST['show_in_nav_menus'],
        'publicly_queryable'  => (bool) $_POST['publicly_queryable'],
        'exclude_from_search' => (bool) $_POST['exclude_from_search'],
        'hierarchical'        => (bool) $_POST['hierarchical'],
        'rewrite'             => (bool) $_POST['rewrite'],
        'query_var'           => (bool) $_POST['query_var'],
        'can_export'          => (bool) $_POST['can_export']
    );

    // if custom capability type is set use it
    if ( !empty( $_POST['capability_type'] ))
        $args['capability_type'] = $_POST['capability_type'];

    // if custom rewrite slug is set use it
    if ( $_POST['rewrite'] == 'advanced' && !empty( $_POST['rewrite_slug'] )) {
        $args['rewrite'] = array( 'slug' => $_POST['rewrite_slug'] );
        ct_set_flush_rewrite_rules( true );
    }

    // remove empty labels so we can use the defaults
    foreach( $args['labels'] as $key => $value ) {
        if ( empty( $value ))
            unset( $args['labels'][$key] );
    }
    // remove keys so we can use the defaults
    if ( $_POST['public'] == 'advanced' ) {
        unset( $args['public'] );
    }
    else {
        unset( $args['show_ui'] );
        unset( $args['show_in_nav_menus'] );
        unset( $args['publicly_queryable'] );
        unset( $args['exclude_from_search'] );
    }

    // store multiple custom post types in the same array and in a single wp_options entry
    if ( !get_site_option('ct_custom_post_types' )) {
        $new_post_types = array( $post_type => $args );
        // set the flush rewrite rules
        ct_set_flush_rewrite_rules( true );
    }
    else {
        $old_post_types = get_site_option( 'ct_custom_post_types' );
        $new_post_types = array_merge( $old_post_types, array( $post_type => $args ));
        // set the flush rewrite rules
        if ( count( $new_post_types ) > count( $old_post_types ))
            ct_set_flush_rewrite_rules( true );
    }

    // update wp_options with the post type options
    update_site_option( 'ct_custom_post_types', $new_post_types );
}
add_action( 'init', 'ct_admin_process_add_update_post_type_requests', 0 );

/**
 * ct_admin_delete_post_type()
 *
 * Intercepts delete $_POST request and removes the deleted post type
 * from the database post types array
 */
function ct_admin_delete_post_type() {

    // verify wp_nonce
    if ( !wp_verify_nonce( $_REQUEST['ct_delete_post_type_secret'], 'ct_delete_post_type_verify' ))
        return;

    // get available post types from db
    $post_types = get_site_option( 'ct_custom_post_types' );

    // remove the deleted post type
    unset( $post_types[$_GET['ct_delete_post_type']] );

    // update the available post types
    update_site_option( 'ct_custom_post_types', $post_types );
}
add_action( 'init', 'ct_admin_delete_post_type', 0 );

/**
 * ct_admin_register_post_types()
 *
 * Get available custom post types from database and register them.
 * The function attach itself to the init hook and uses priority of 2.It loads
 * after the ct_admin_register_taxonomies() func which hook itself to the init
 * hook with priority of 1.
 */
function ct_admin_register_post_types() {

    $post_types = get_site_option( 'ct_custom_post_types' );

    if ( !empty( $post_types )) {
        foreach ( $post_types as $post_type => $args )
            register_post_type( $post_type, $args );

        // flush the rewrite rules
        ct_flush_rewrite_rules();
    }
}
add_action( 'init', 'ct_admin_register_post_types', 2 );

/**
 * ct_admin_process_add_update_taxonomy_requests()
 *
 * Intercepts $_POST request and processes the taxonomy submissions
 *
 * @global bool $ct_redirect
 */
function ct_admin_process_add_update_taxonomy_requests() {
    global $ct_redirect;

    // stop execution and return if no add/edit taxonomy request is made
    if ( !( isset( $_POST['ct_submit_add_taxonomy'] ) || isset( $_POST['ct_submit_update_taxonomy'] )))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['ct_submit_taxonomy_secret'], 'ct_submit_taxonomy_verify'))
        return;

    // validate input fields and set redirect rules
    if ( isset( $_POST['ct_submit_add_taxonomy'] )) {
        $field_taxonomy_valid    = ct_validate_field( 'taxonomy', $_POST['taxonomy'] );
        $field_object_type_valid = ct_validate_field( 'object_type', $_POST['object_type'] );

        if ( $field_taxonomy_valid && $field_object_type_valid ) {
            $ct_redirect = true;
        } else {
            $ct_redirect = false;
            return;
        }
    }

    $taxonomy = ( isset( $_POST['taxonomy'] )) ? $_POST['taxonomy'] : $_GET['ct_edit_taxonomy'];

    $object_type = $_POST['object_type'];
    $labels = array(
        'name'                       => $_POST['labels']['name'],
        'singular_name'              => $_POST['labels']['singular_name'],
        'add_new_item'               => $_POST['labels']['add_new_item'],
        'new_item_name'              => $_POST['labels']['new_item_name'],
        'edit_item'                  => $_POST['labels']['edit_item'],
        'update_item'                => $_POST['labels']['update_item'],
        'search_items'               => $_POST['labels']['search_items'],
        'popular_items'              => $_POST['labels']['popular_items'],
        'all_items'                  => $_POST['labels']['all_items'],
        'parent_item'                => $_POST['labels']['parent_item'],
        'parent_item_colon'          => $_POST['labels']['parent_item_colon'],
        'add_or_remove_items'        => $_POST['labels']['add_or_remove_items'],
        'separate_items_with_commas' => $_POST['labels']['separate_items_with_commas'],
        'choose_from_most_used'      => $_POST['labels']['all_items']
    );
    $args = array(
        'labels'              => $labels,
        'public'              => (bool) $_POST['public'] ,
        'show_ui'             => (bool) $_POST['show_ui'],
        'show_tagcloud'       => (bool) $_POST['show_tagcloud'],
        'show_in_nav_menus'   => (bool) $_POST['show_in_nav_menus'],
        'hierarchical'        => (bool) $_POST['hierarchical'],
        'rewrite'             => (bool) $_POST['rewrite'],
        'query_var'           => (bool) $_POST['query_var'],
        'capabilities'        => array ( 'assign_terms' => 'edit_listings' )
    );

    // if custom rewrite slug is set use it
    if ( $_POST['rewrite'] == 'advanced' && !empty( $_POST['rewrite_slug'] )) {
        $args['rewrite'] = array( 'slug' => $_POST['rewrite_slug'] );
        ct_set_flush_rewrite_rules( true );
    }

    // remove empty values from labels so we can use the defaults
    foreach( $args['labels'] as $key => $value ) {
        if ( empty( $value ))
            unset( $args['labels'][$key] );
    }
    // if no advanced is set, unset values so we can use the defaults
    if ( $_POST['public'] == 'advanced' ) {
        unset( $args['public'] );
    }
    else {
        unset( $args['show_ui'] );
        unset( $args['show_tagcloud'] );
        unset( $args['show_in_nav_menus'] );
    }

    // store multiple custom taxonomies in the same array and in a single wp_options entry
    if ( !get_site_option( 'ct_custom_taxonomies' )) {
        $new_taxonomies = array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args ));
        // set the flush rewrite rules
        ct_set_flush_rewrite_rules( true );
    }
    else {
        $old_taxonomies = get_site_option( 'ct_custom_taxonomies' );
        $new_taxonomies = array_merge( $old_taxonomies, array( $taxonomy => array( 'object_type' => $object_type, 'args' => $args )));
        // set the flush rewrite rules
        if ( count( $new_taxonomies ) > count( $old_taxonomies ))
            ct_set_flush_rewrite_rules( true );
    }

    // update wp_options with the taxonomies options
    update_site_option( 'ct_custom_taxonomies', $new_taxonomies );
}
add_action( 'init', 'ct_admin_process_add_update_taxonomy_requests', 0 );

/**
 * ct_admin_delete_taxonomy()
 *
 * Intercepts delete $_POST request and removes the deleted taxonomy
 * from the database taxonomies array
 */
function ct_admin_delete_taxonomy() {

    // verify wp_nonce
    if ( !wp_verify_nonce( $_REQUEST['ct_delete_taxonomy_secret'], 'ct_delete_taxonomy_verify' ))
        return;

    // get available taxonomies from db
    $taxonomies = get_site_option( 'ct_custom_taxonomies' );

    // remove the deleted taxonomy
    unset( $taxonomies[$_GET['ct_delete_taxonomy']] );

    // update the available taxonomies
    update_site_option( 'ct_custom_taxonomies', $taxonomies );
}
add_action( 'init', 'ct_admin_delete_taxonomy', 0 );

/**
 * ct_admin_register_taxonomies()
 *
 * Get available custom taxonomies from database and register them.
 * The function atach itself to the init hook and uses priority of 1. It loads
 * before the ct_admin_register_post_types() func which hook itself to the init
 * hook with priority of 2.
 */
function ct_admin_register_taxonomies() {

    $taxonomies = get_site_option( 'ct_custom_taxonomies' );

    if ( !empty( $taxonomies )) {
        foreach ( $taxonomies as $taxonomy => $args )
            register_taxonomy( $taxonomy, $args['object_type'], $args['args'] );
    }
}
add_action('init', 'ct_admin_register_taxonomies', 1 );


/**
 * ct_admin_process_add_update_custom_fields_requests()
 *
 * Intercepts $_POST request and processes the custom fields submissions
 */
function ct_admin_process_add_update_custom_field_requests() {
    global $ct_redirect;

    // stop execution and return if no add/edit custom field request is made
    if ( !( isset( $_POST['ct_submit_add_custom_field'] ) || isset( $_POST['ct_submit_update_custom_field'] )))
        return;

    // verify wp_nonce
    if ( !wp_verify_nonce( $_POST['ct_submit_custom_field_secret'], 'ct_submit_custom_field_verify'))
        return;

    // validate fields data
    $field_title_valid        = ct_validate_field( 'field_title', $_POST['field_title'] );
    $field_object_type_valid  = ct_validate_field( 'object_type', $_POST['object_type'] );

    if ( in_array( $_POST['field_type'], array( 'radio', 'checkbox', 'selectbox', 'multiselectbox' ))) {
        $field_options_valid = ct_validate_field( 'field_options', $_POST['field_options'][1] );

        if ( $field_title_valid && $field_object_type_valid && $field_options_valid ) {
            $ct_redirect = true;
        } else {
            $ct_redirect = false;
            return;
        }
    } else {
        if ( $field_title_valid && $field_object_type_valid ) {
            $ct_redirect = true;
        } else {
            $ct_redirect = false;
            return;
        }
    }

    $field_id = ( empty( $_GET['ct_edit_custom_field'] )) ? $_POST['field_type'] . '_' . uniqid() : $_GET['ct_edit_custom_field'];

    $args = array(
        'field_title'          => $_POST['field_title'],
        'field_type'           => $_POST['field_type'],
        'field_sort_order'     => $_POST['field_sort_order'],
        'field_options'        => $_POST['field_options'],
        'field_default_option' => $_POST['field_default_option'],
        'field_description'    => $_POST['field_description'],
        'object_type'          => $_POST['object_type'],
        'required'             => $_POST['required'],
        'field_id'             => $field_id
    );

    // unset if there are no options to be stored in the db
    if ( $args['field_type'] == 'text' || $args['field_type'] == 'textarea' )
        unset( $args['field_options'] );

    if ( !get_site_option( 'ct_custom_fields' )) {
        $new_custom_fields = array( $field_id => $args );
    }
    else {
        $old_custom_fields = get_site_option( 'ct_custom_fields' );
        $new_custom_fields = array_merge( $old_custom_fields, array( $field_id => $args ));
    }

    update_site_option('ct_custom_fields', $new_custom_fields );
}
add_action( 'init', 'ct_admin_process_add_update_custom_field_requests', 0 );

/**
 * ct_admin_delete_custom_fields()
 *
 * Delete custom fields
 */
function ct_admin_delete_custom_fields() {

    // verify wp_nonce
    if ( !wp_verify_nonce( $_REQUEST['ct_delete_custom_field_secret'], 'ct_delete_custom_field_verify' ))
        return;

    // get available custom fields from db
    $custom_fields = get_site_option( 'ct_custom_fields' );

    // remove the deleted custom field
    unset( $custom_fields[$_GET['ct_delete_custom_field']] );

    // update the available custom fields
    update_site_option( 'ct_custom_fields', $custom_fields );
}
add_action( 'init', 'ct_admin_delete_custom_fields', 0 );

/**
 * ct_create_custom_fields()
 *
 * Create the custom fields
 */
function ct_create_custom_fields() {
    $custom_fields = get_site_option( 'ct_custom_fields' );

    if ( !empty( $custom_fields )) {
        foreach ( $custom_fields as $custom_field ) {
            foreach ( $custom_field['object_type'] as $object_type )
                add_meta_box( 'ct-custom-fields', 'Custom Fields', 'ct_admin_ui_display_custom_fields', $object_type, 'normal', 'high' );
        }
    }
}
add_action( 'admin_menu', 'ct_create_custom_fields', 2 );

/**
 * ct_save_custom_fields()
 *
 * Save custom fields data
 *
 * @param int $post_id The post id of the post being edited
 */
function ct_save_custom_fields( $post_id ) {

    // prevent autosave from deleting the custom fields
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return;

    $prefix = '_ct_';
    $custom_fields = get_site_option( 'ct_custom_fields' );

    if ( !empty( $custom_fields )) {
        foreach ( $custom_fields as $custom_field ) {
            if ( isset( $_POST[$prefix . $custom_field['field_id']] ))
                update_post_meta( $post_id, $prefix . $custom_field['field_id'], $_POST[$prefix . $custom_field['field_id']] );
            else
                delete_post_meta( $post_id, $prefix . $custom_field['field_id'] );
        }
    }
}
add_action( 'save_post', 'ct_save_custom_fields', 1, 1 );

/**
 * ct_validate_field()
 *
 * Validates input fields data
 *
 * @param string $field
 * @param mixed $value
 * @return bool true/false depending on validation outcome
 */
function ct_validate_field( $field, $value ) {

    if ( $field == 'taxonomy' || $field == 'post_type' ) {
        if ( preg_match('/^[a-zA-Z0-9_]{2,}$/', $value )) {
            return true;
        } else {
            if ( $field == 'post_type' )
                add_action( 'ct_invalid_field_post_type', 'ct_invalid_field_action' );
            if ( $field == 'taxonomy' )
                add_action( 'ct_invalid_field_taxonomy', 'ct_invalid_field_action' );
            return false;
        }
    }

    if ( $field == 'object_type' || $field == 'field_title' || $field == 'field_options' ) {
        if ( empty( $value )) {
            if ( $field == 'object_type' )
                add_action( 'ct_invalid_field_object_type', 'ct_invalid_field_action' );
            if ( $field == 'field_title' )
                add_action( 'ct_invalid_field_title', 'ct_invalid_field_action' );
            if ( $field == 'field_options' )
                add_action( 'ct_invalid_field_options', 'ct_invalid_field_action' );
            return false;
        } else {
            return true;
        }
    }
}

/**
 * ct_invalid_field_action()
 *
 * Prints the invalid field class in the templates
 */
function ct_invalid_field_action() {
    echo( 'form-invalid' );
}

/**
 * ct_set_flush_rewrite_rules()
 *
 * Set the flush rewrite rules and write then in db for later reference
 *
 * @param bool $bool
 */
function ct_set_flush_rewrite_rules( $bool ) {
    // set the flush rewrite rules
    update_site_option( 'ct_flush_rewrite_rules', $bool );
}

/**
 * ct_flush_rewrite_rules()
 *
 * Flush rewrite rules based on db options check
 */
function ct_flush_rewrite_rules() {
    // flush rewrite rules
    if ( get_site_option('ct_flush_rewrite_rules')) {
        flush_rewrite_rules();
        ct_set_flush_rewrite_rules( false );
    }
}

/**
 * ct_core_check_user_register()
 *
 * Check whether new users are registered, since we need to flush the rewrite
 * rules for them, and upadte the rewrite options
 */
function ct_core_check_user_register() {
    ct_set_flush_rewrite_rules( true );
}
add_action( 'user_register', 'ct_core_check_user_register' );