<?php global $dp_global; ?>

<?php if ( $dp_global->checkout->paypal_express->current_step == 'paypal_disabled' ): ?>

    <?php _e('This feature is currently disabled by the system administrator.', 'directory'); ?>

<?php endif; ?>

<?php if ( $dp_global->checkout->paypal_express->current_step == 'cost_terms' ): ?>

    <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>

<?php elseif ( $dp_global->checkout->paypal_express->current_step == 'login_error' ): ?>

    <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>

    <div class="dp-invalid-login"><?php echo $dp_global->checkout->paypal_express->error; ?></div>

<?php elseif ( $dp_global->checkout->paypal_express->current_step == 'cost_terms_submit' ): ?>

    <?php if ( $dp_global->checkout->paypal_express->current_sub_step == 'cost_terms_error' ): ?>

        <?php locate_template( array( 'checkout/checkout-cost-terms.php' ), true ); ?>

    <?php else: ?>

        <?php locate_template( array( 'checkout/checkout-select-payment-method.php' ), true ); ?>

    <?php endif; ?>

<?php elseif ( $dp_global->checkout->paypal_express->current_step == 'payment_method_submit' ): ?>

    <?php if ( $dp_global->checkout->paypal_express->payment_method == 'cc' ): ?>

        <?php locate_template( array( 'checkout/checkout-cc-details.php' ), true ); ?>

    <?php endif; ?>

<?php elseif ( $dp_global->checkout->paypal_express->current_step == 'confitm_payment' ): ?>

    <?php locate_template( array( 'checkout/checkout-confirm-payment.php' ), true ); ?>

<?php elseif ( $dp_global->checkout->paypal_express->current_step == 'confitm_payment_submit' ): ?>

    <?php locate_template( array( 'checkout/checkout-success-proceed.php' ), true ); ?>

<?php endif; ?>

<div class="clear"></div>