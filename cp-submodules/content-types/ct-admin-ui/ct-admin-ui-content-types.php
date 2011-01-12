<?php

/**
 * ct_admin_ui_content_types()
 *
 * Dispatches particular content type pages based on $_GET requests.
 */
function ct_admin_ui_content_types() { ?>

    <div class="wrap ct-wrap ct-content-types">
        <div class="icon32" id="icon-edit"><br></div>
        <h2>
            <a class="nav-tab <?php if ( $_GET['ct_content_type'] == 'post_type' || !isset( $_GET['ct_content_type'] )) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=post_type"><?php _e('Post Types', 'content_types'); ?></a>
            <a class="nav-tab <?php if ( $_GET['ct_content_type'] == 'taxonomy' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=taxonomy"><?php _e('Taxonomies', 'content_types'); ?></a>
            <a class="nav-tab <?php if ( $_GET['ct_content_type'] == 'custom_field' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=custom_field"><?php _e('Custom Fields', 'content_types'); ?></a>
        </h2> <?php
        
        if ( $_GET['ct_content_type'] == 'post_type' || !isset( $_GET['ct_content_type'] )) {
            if ( isset( $_GET['ct_add_post_type'] )) {
                include_once 'ct-admin-ui-add-post-type.php';
                ct_admin_ui_add_post_type();
            }
            elseif ( isset( $_GET['ct_edit_post_type'] )) {
                include_once 'ct-admin-ui-edit-post-type.php';
                $post_types = get_site_option( 'ct_custom_post_types' );
                ct_admin_ui_edit_post_type( $post_types[$_GET['ct_edit_post_type']] );
            }
            elseif ( isset( $_GET['ct_delete_post_type'] )) {
                include_once 'ct-admin-ui-delete-post-type.php';
                $post_types = get_site_option( 'ct_custom_post_types' );
                ct_admin_ui_delete_post_type( $post_types[$_GET['ct_delete_post_type']] );
            }
            else {
                include_once 'ct-admin-ui-post-types.php';
                $post_types = get_site_option( 'ct_custom_post_types' );
                ct_admin_ui_post_types( $post_types );
            }
        }
        elseif ( $_GET['ct_content_type'] == 'taxonomy' ) {
            if ( isset( $_GET['ct_add_taxonomy'] )) {
                include_once 'ct-admin-ui-add-taxonomy.php';
                $post_types = get_post_types('','names');
                ct_admin_ui_add_taxonomy( $post_types );
            }
            elseif ( isset( $_GET['ct_edit_taxonomy'] )) {
                include_once 'ct-admin-ui-edit-taxonomy.php';
                $taxonomies = get_site_option( 'ct_custom_taxonomies' );
                $post_types = get_post_types('','names');
                ct_admin_ui_edit_taxonomy( $taxonomies[$_GET['ct_edit_taxonomy']], $post_types );
            }
            elseif ( isset( $_GET['ct_delete_taxonomy'] )) {
                include_once 'ct-admin-ui-delete-taxonomy.php';
                $taxonomies = get_site_option( 'ct_custom_taxonomies' );
                ct_admin_ui_delete_taxonomy( $taxonomies[$_GET['ct_delete_taxonomy']] );
            }
            else {
                include_once 'ct-admin-ui-taxonomies.php';
                $taxonomies = get_site_option( 'ct_custom_taxonomies' );
                ct_admin_ui_taxonomies( $taxonomies );
            }
        }
        elseif ( $_GET['ct_content_type'] == 'custom_field' ) {
            if ( isset( $_GET['ct_add_custom_field'] )) {
                include_once 'ct-admin-ui-add-custom-field.php';
                $post_types = get_post_types('','names');
                ct_admin_ui_add_custom_field( $post_types );
            }
            elseif ( isset( $_GET['ct_edit_custom_field'] )) {
                include_once 'ct-admin-ui-edit-custom-field.php';
                $custom_fields = get_site_option( 'ct_custom_fields' );
                $post_types = get_post_types('','names');
                ct_admin_ui_edit_custom_field( $custom_fields[$_GET['ct_edit_custom_field']], $post_types );
            }
            elseif ( isset( $_GET['ct_delete_custom_field'] )) {
                include_once 'ct-admin-ui-delete-custom-field.php';
                $custom_fields = get_site_option( 'ct_custom_fields' );
                ct_admin_ui_delete_custom_field( $custom_fields[$_GET['ct_delete_custom_field']] );
            }
            else {
                include_once 'ct-admin-ui-custom-fields.php';
                $custom_fields = get_site_option( 'ct_custom_fields' );
                ct_admin_ui_custom_fields( $custom_fields );
            }
        } ?>
        
    </div> <?php
} ?>
