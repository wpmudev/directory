<?php

/**
 * dp_core_checkout_paypal_express()
 * 
 * @global <type> $dp_global
 * @global <type> $wp_query
 * @return <type>
 */
function dp_core_checkout_paypal_express() {
    global $dp_global, $wp_query;
    $options = get_site_option('dp_options');

    if ( is_user_logged_in() && $wp_query->query_vars['name'] == 'submit-listing' ) {
        wp_safe_redirect( get_bloginfo('url') );
        exit;
    }

    if ( empty( $options['paypal'] )) {
        $dp_global->checkout->paypal_express->current_step = 'paypal_disabled';
        return;
    }

    $dp_global->checkout->paypal_express->current_step = 'cost_terms';

    if ( isset( $_POST['login_submit'] )) {

        $credentials = array( 'remember' => true, 'user_login' => $_POST['username'], 'user_password' => $_POST['password'] );
        $result      = wp_signon( $credentials );

        if ( isset( $result->errors )) {
            
            if ( isset( $result->errors['invalid_username'] ))
                $dp_global->checkout->paypal_express->error = $result->errors['invalid_username'][0];
            elseif ( isset( $result->errors['incorrect_password'] ))
                $dp_global->checkout->paypal_express->error = $result->errors['incorrect_password'][0];
            elseif ( isset( $result->errors ) && empty( $result->errors ))
                $dp_global->checkout->paypal_express->error =  __('Please fill in the required fields.', 'directory');

            $dp_global->checkout->paypal_express->current_step = 'login_error';
            add_action( 'login_invalid', 'dp_invalid_class' );
            
            return;
        }

        wp_safe_redirect( get_bloginfo( 'url' ));
        exit;
    }

    if ( isset( $_POST['terms_submit'] )) {

        $dp_global->checkout->paypal_express->current_step = 'cost_terms_submit';

        if ( empty( $_POST['tos_agree'] )) {
            add_action( 'tos_agree_invalid', 'dp_invalid_class' );
            $dp_global->checkout->paypal_express->current_sub_step = 'cost_terms_error';
        }

        if ( empty( $_POST['billing'] )) {
            add_action( 'billing_invalid', 'dp_invalid_class' );
            $dp_global->checkout->paypal_express->current_sub_step = 'cost_terms_error';
        }

        return;
    }

    if ( isset( $_POST['payment_method_submit'] )) {
        
        $dp_global->checkout->paypal_express->current_step = 'payment_method_submit';

        if ( $_POST['payment_method'] == 'cc' )
            $dp_global->checkout->paypal_express->payment_method = 'cc';

        if ( $_POST['payment_method'] == 'paypal' )
            dp_geteway_paypal_express_call_checkout( $_POST['cost'] );

        return;
    }

    if ( isset( $_POST['direct_payment_submit'] )) {
        $direct_payment = dp_gateway_paypal_express_direct_payment( $_POST['total_amount'], $_POST['cc_type'], $_POST['cc_number'], $_POST['exp_date'], $_POST['cvv2'], $_POST['first_name'], $_POST['last_name'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['country_code'] );
        return;
    }

    if ( isset( $_REQUEST['token'] ) && !isset( $_POST['confirm_payment_submit'] )) {
        $dp_global->checkout->paypal_express->current_step = 'confitm_payment';
        return;
    }

    if ( isset( $_POST['confirm_payment_submit'] )) {
        $dp_global->checkout->paypal_express->current_step = 'confitm_payment_submit';

        $transaction_details = dp_geteway_paypal_express_confirm_payment( $_POST['total_amount'] );

        if ( strtoupper( $transaction_details['ACK'] ) == 'SUCCESS' || strtoupper( $transaction_details['ACK'] ) == 'SUCCESSWITHWARNING' ) {
            dp_insert_user( $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['billing'] );
        }
        return;
    }
}
add_action( 'template_redirect', 'dp_core_checkout_paypal_express' );

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

?>
