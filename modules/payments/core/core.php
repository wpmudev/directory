<?php

/**
 * Handles requests for "page-checkout.php". You can customize it for any
 * other request handling flow.
 **/
if ( !class_exists('Payments_Core') ):
class Payments_Core {

    /** @var object $paypal_api_module The PayPal API Module object */
    var $paypal_api_module;
    /** @var object $paypal_api_module The PayPal API Module object */
    var $admin_page_slug;

    /**
     * Constructor.
     **/
    function Payments_Core( $admin_page_slug ) {
        $this->admin_page_slug = $admin_page_slug;
        /* Hook the vars assignment to init hook */
        add_action( 'init', array( &$this, 'init_vars' ) );
        /* Handle all requests for checkout */
        add_action( 'template_redirect', array( &$this, 'handle_checkout_requests' ) );
    }

    /**
     * Initiate class variables.
     *
     * @return void
     **/
    function init_vars() {
        $this->paypal_api_module = new PayPal_API_Module( '' );
    }


    function navigation_tab() { ?>
        <a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' ) echo 'nav-tab-active'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=paypal"><?php _e( 'Payments', $this->text_domain ); ?></a>
        <?php
    }

    function navigation_sub() { ?>
        <?php if ( $sub == 'paypal' || $sub == 'authorizenet'  ): ?>
        <ul>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'paypal' )       echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=paypal"><?php _e( 'PayPal Express', $this->text_domain ); ?></a> | </h3></li>
            <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'authorizenet' ) echo 'current'; ?>" href="admin.php?page=<?php echo $this->admin_page_slug; ?>&tab=payments&sub=authorizenet"><?php _e( 'Authorize.net', $this->text_domain ); ?></a></h3></li>
        </ul>
        <?php endif; ?>
        <?php
    }

    /**
     * Handle all checkout requests.
     *
     * @uses session_start() We need to keep track of some session variables for the checkout
     * @return NULL If the payment gateway options are not configured.
     **/
    function handle_checkout_requests() {
        /* Only handle request if on the proper page */
        if ( is_page('checkout') ) {
            /* Start session */
            if ( !session_id() )
                session_start();
            /* Get site options */
            $options = get_site_option('dp_options');
            //var_dump($options);
            /* Redirect if user is logged in */
            if ( is_user_logged_in() ) {
                /** @todo Set redirect */
                //wp_redirect( get_bloginfo('url') );
            }
            /* If no PayPal API credentials are set, disable the checkout process */
            if ( empty( $options['paypal'] ) ) {
                /* Set the proper step which will be loaded by "page-checkout.php" */
                set_query_var( 'cf_step', 'disabled' );
                return;
            }
            /* If Terms and Costs step is submitted */
            if ( isset( $_POST['terms_submit'] ) ) {
                /* Validate fields */
                if ( empty( $_POST['tos_agree'] ) || empty( $_POST['billing'] ) ) {
                    if ( empty( $_POST['tos_agree'] ))
                        add_action( 'tos_invalid', create_function('', 'echo "class=\"error\"";') );
                    if ( empty( $_POST['billing'] ))
                        add_action( 'billing_invalid', create_function('', 'echo "class=\"error\"";') );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'terms' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'payment_method' );
                }
            }
            /* If login attempt is made */
            elseif ( isset( $_POST['login_submit'] ) ) {
                if ( isset( $this->login_error )) {
                    add_action( 'login_invalid', create_function('', 'echo "class=\"error\"";') );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'terms' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'cf_error', $this->login_error );
                } else {
                    wp_redirect( get_bloginfo('url') . '/classifieds/my-classifieds/' );
                }
            }
            /* If payment method is selected and submitted */
            elseif ( isset( $_POST['payment_method_submit'] )) {
                if ( $_POST['payment_method'] == 'paypal' ) {
                    /* Make API call */
                    $result = $this->paypal_api_module->call_shortcut_express_checkout( $_POST['cost'] );
                    /* Handle Success and Error scenarios */
                    if ( $result['status'] == 'error' ) {
                        /* Set the proper step which will be loaded by "page-checkout.php" */
                        set_query_var( 'cf_step', 'api_call_error' );
                        /* Pass error params to "page-checkout.php" */
                        set_query_var( 'cf_error', $result );
                    } else {
                        /* Set billing and credits so we can update the user account later */
                        $_SESSION['billing'] = $_POST['billing'];
                        $_SESSION['credits'] = $_POST['credits'];
                    }
                } elseif ( $_POST['payment_method'] == 'cc' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'cc_details' );
                }
            }
            /* If direct CC payment is submitted */
            elseif ( isset( $_POST['direct_payment_submit'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->direct_payment( $_POST['total_amount'], $_POST['cc_type'], $_POST['cc_number'], $_POST['exp_date'], $_POST['cvv2'], $_POST['first_name'], $_POST['last_name'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['country_code'] );
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'direct_payment' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'cf_error', $result );
                }
            }
            /* If PayPal has redirected us back with the proper TOKEN */
            elseif ( isset( $_REQUEST['token'] ) && !isset( $_POST['confirm_payment_submit'] ) && !isset( $_POST['redirect_my_classifieds'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->get_shipping_details();
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'confirm_payment' );
                    /* Pass transaction details params to "page-checkout.php" */
                    set_query_var( 'cf_transaction_details', $result );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'cf_error', $result );
                }
            }
            /* If payment confirmation is submitted */
            elseif ( isset( $_POST['confirm_payment_submit'] ) ) {
                /* Make API call */
                $result = $this->paypal_api_module->confirm_payment( $_POST['total_amount'] );
                /* Handle Success and Error scenarios */
                if ( $result['status'] == 'success' ) {
                    /* Insert/Update User */
                    $this->update_user( $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['billing'], $_POST['credits'] );
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'success' );
                } else {
                    /* Set the proper step which will be loaded by "page-checkout.php" */
                    set_query_var( 'cf_step', 'api_call_error' );
                    /* Pass error params to "page-checkout.php" */
                    set_query_var( 'cf_error', $result );
                }
            }
            /* If transaction processed successfully, redirect to my-classifieds */
            elseif( isset( $_POST['redirect_my_classifieds'] ) ) {
                wp_redirect( get_bloginfo('url') . '/classifieds/my-classifieds/' );
            }
            /* If no requests are made load default step */
            else {
                /* Set the proper step which will be loaded by "page-checkout.php" */
                set_query_var( 'cf_step', 'terms' );
            }
        }
    }
}
endif;

?>