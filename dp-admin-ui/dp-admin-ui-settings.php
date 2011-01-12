<?php

/**
 * dp_admin_ui_settings()
 *
 * Dispatches particular settings pages.
 */
function dp_admin_ui_settings() { ?>

    <div class="wrap dp-wrap dp-settings">
        <div class="icon32" id="icon-edit"><br></div>
        <h2>
            <a class="nav-tab <?php if ( $_GET['dp_settings'] == 'main' || empty( $_GET['dp_settings'] )) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=post_type"><?php _e('DirectoryPres', 'directorypress'); ?></a>
            <a class="nav-tab <?php if ( $_GET['dp_settings'] == 'payments' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=ct_content_types&ct_content_type=taxonomy"><?php _e('Payments', 'directorypress'); ?></a>
        </h2> <?php
        
        if ( $_GET['dp_settings'] == 'main' || empty( $_GET['dp_settings'] )) {
            if ( isset( $_GET['ct_add_post_type'] )) {

            }
        }
        elseif ( $_GET['dp_settings'] == 'payments' ) {
            if ( isset( $_GET['ct_add_taxonomy'] )) {

            }
        } ?>
        
    </div> <?php
} ?>