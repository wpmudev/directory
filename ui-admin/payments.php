<?php

/**
 * dp_admin_ui_payments()
 *
 * Outputs "Directory Settings" admin page.
 *
 */
function dp_admin_ui_payments() { ?>

    <ul class="subsubsub dp-payments">
        <li><h3><a class="<?php if ( $_GET['dp_gateway'] == 'paypal_express' || empty( $_GET['dp_gateway'] )) echo 'current'; ?>" href="admin.php?page=dp_main&dp_settings=payments&dp_gateway=paypal_express"><?php _e('PayPal Express', 'directory'); ?></a> | </h3></li>
        <li><h3><a class="<?php if ( $_GET['dp_gateway'] == 'authorize_net' ) echo 'current'; ?>" href="admin.php?page=dp_main&dp_settings=payments&dp_gateway=authorize_net">Authorize.net</a></h3></li>
    </ul>

    <?php
    if ( $_GET['dp_gateway'] == 'paypal_express' || empty( $_GET['dp_gateway'] )) {
        include_once DP_PLUGIN_DIR . 'ui-admin/payments-paypal-express.php';
        $options = get_site_option( 'dp_options' );
        dp_admin_ui_payments_paypal_express( $options );
    }
    elseif (  $_GET['dp_gateway'] == 'authorize_net' ) {
        include_once DP_PLUGIN_DIR . 'dp-admin-ui/payments-authorize-net.php';
        dp_admin_ui_payments_authorize_net();
    }
}
?>