<?php

/**
 * dp_admin_ui_settings()
 *
 * Dispatches particular settings pages.
 */
function dp_admin_ui_main() { ?>

    <div class="wrap dp-wrap dp-settings">
        <?php screen_icon('options-general'); ?>
        <h2>
            <a class="nav-tab <?php if ( $_GET['dp_settings'] == 'main' || empty( $_GET['dp_settings'] )) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=dp_main&dp_settings=main"><?php _e('Directory', 'directory'); ?></a>
            <a class="nav-tab <?php if ( $_GET['dp_settings'] == 'payments' ) { echo( 'nav-tab-active' ); } ?>" href="admin.php?page=dp_main&dp_settings=payments"><?php _e('Payments', 'directory'); ?></a>
        </h2> <?php
        
        if ( $_GET['dp_settings'] == 'main' || empty( $_GET['dp_settings'] )) {
            include_once 'dp-admin-ui-settings.php';
            dp_admin_ui_settings();
        }
        elseif ( $_GET['dp_settings'] == 'payments' ) {
            include_once 'dp-admin-ui-payments.php';
            dp_admin_ui_payments();
        } ?>
        
    </div> <?php
} ?>