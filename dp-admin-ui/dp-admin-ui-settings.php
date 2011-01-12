<?php

/**
 * dp_admin_ui_settings()
 *
 * Outputs "DirectoryPress Settings" admin page.
 *
 */
function dp_admin_ui_settings() { ?>

    <ul class="subsubsub dp-settings">
        <?php /* <li><h3><a class="<?php if ( isset( $_GET['dp_gen'] ) || ( !isset( $_GET['dp_ss'] ) && !isset( $_GET['dp_ads'] ) ) ) echo 'current'; ?>" href="admin.php?page=dp_main&dp_settings=main&dp_gen"><?php _e('General', 'directorypress'); ?></a> | </h3></li> */ ?>
        <li><h3><a class="<?php if ( isset( $_GET['dp_ss'] ) || !isset( $_GET['dp_ads'] ) ) echo 'current'; ?>" href="admin.php?page=dp_main&dp_settings=main&dp_ss"><?php _e('Submit Site', 'directorypress'); ?></a> | </h3></li>
        <li><h3><a class="<?php if ( isset( $_GET['dp_ads'] ) ) echo 'current'; ?>" href="admin.php?page=dp_main&dp_settings=main&dp_ads"><?php _e('Advertising', 'directorypress'); ?></a></h3></li>
    </ul>

    <?php
    if ( isset( $_GET['dp_gen'] )) {
        //include_once DP_PLUGIN_DIR . 'dp-admin-ui/dp-admin-ui-payments-paypal-express.php';
        //$options = get_site_option( 'dp_options' );
        //dp_admin_ui_payments_paypal_express( $options );
    }
    elseif ( isset( $_GET['dp_ss'] ) || !isset( $_GET['dp_ads'] )) {
        include_once DP_PLUGIN_DIR . 'dp-admin-ui/dp-admin-ui-settings-submit-site.php';
        $options = get_site_option('dp_options');
        dp_admin_ui_settings_submit_site( $options['submit_site_settings'] );
    }
    elseif ( isset( $_GET['dp_ads'] )) {
        include_once DP_PLUGIN_DIR . 'dp-admin-ui/dp-admin-ui-settings-ads.php';
        $options = get_site_option('dp_options');
        dp_admin_ui_settings_ads( $options['ads'] );
    }
}
?>