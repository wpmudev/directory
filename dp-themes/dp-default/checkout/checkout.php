<?php if ( !is_user_logged_in()): ?>

    <?php if ( empty( $_POST ) && !isset( $_REQUEST['token'] )): ?>

        <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>

    <?php elseif( isset( $_POST['login_submit'] )): ?>

        <?php $result = dp_login_user(); ?>

        <?php if ( isset( $result->errors )): ?>

            <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>

            <?php if ( isset( $result->errors['invalid_username'] )): ?>
                <div class="dp-invalid-login"><?php echo $result->errors['invalid_username'][0]; ?></div>
            <?php elseif ( isset( $result->errors['incorrect_password'] )): ?>
                <div class="dp-invalid-login"><?php echo $result->errors['incorrect_password'][0]; ?></div>
            <?php elseif ( isset( $result->errors ) && empty( $result->errors )): ?>
                <div class="dp-invalid-login"><?php _e('Please fill in the required fields.'); ?></div>
            <?php endif; ?>
                
        <?php endif; ?>

    <?php elseif ( isset( $_POST['terms_submit'] )): ?>
                
        <?php $tos = dp_validate_field( 'tos_agree' ); $billing = dp_validate_field( 'billing' ); ?>
        <?php if ( $tos && $billing ): ?>
            <?php locate_template( array( 'checkout/checkout-select-payment-method.php' ), true ); ?>
        <?php else: ?>
            <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>
        <?php endif; ?>

    <?php elseif ( isset( $_POST['payment_method_submit'] ) ): ?>

        <?php if ( $_POST['payment_method'] == 'cc' ): ?>

            <?php locate_template( array( 'checkout/checkout-cc-details.php' ), true ); ?>

        <?php endif; ?>

    <?php elseif ( isset( $_POST['direct_payment_submit'] )): ?>

        <?php $direct_payment = dp_gateway_paypal_express_direct_payment( $_POST['total_amount'], $_POST['cc_type'], $_POST['cc_number'], $_POST['exp_date'], $_POST['cvv2'], $_POST['first_name'], $_POST['last_name'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['country_code'] ); ?>

    <?php elseif ( isset( $_REQUEST['token'] ) && !isset( $_POST['confirm_payment_submit'] )): ?>

        <?php locate_template( array( 'checkout/checkout-confirm-payment.php' ), true ); ?>

    <?php elseif ( isset( $_POST['confirm_payment_submit'] )): ?>

        <?php $transaction_details = dp_geteway_paypal_express_confirm_payment( $_POST['total_amount'] ); ?>

        <?php if ( strtoupper( $transaction_details['ACK'] ) == 'SUCCESS' || strtoupper( $transaction_details['ACK'] ) == 'SUCCESSWITHWARNING' ): ?>

            <?php dp_insert_user( $_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST[billing] ); ?>

            <?php locate_template( array( 'checkout/checkout-success-proceed.php' ), true ); ?>

        <?php endif; ?>

    <?php endif; ?>

    <div class="clear"></div>

<?php else: ?>

    <?php wp_safe_redirect( get_bloginfo( 'url' )); ?>
    
<?php endif; ?>