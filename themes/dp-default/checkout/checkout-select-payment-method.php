<form action="" method="post"  class="dp-sys-info">

    <span class="dp-submit-txt"><?php _e('Choose Payment Method', 'directory'); ?></span>
    <table>
        <tr>
            <th><label for="payment_method"><?php _e('PayPal', 'directory'); ?></label></th>
            <td>
                <input type="radio" name="payment_method" value="paypal"/>
                <img  src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" border="0" alt="Acceptance Mark">
            </td>
        </tr>
        <tr>
            <th><label for="payment_method"><?php _e('Credit Card','directory'); ?></label></th>
            <td>
                <input type="radio" name="payment_method" value="cc" />
                <img  src="<?php bloginfo('stylesheet_directory') ?>/images/cc-logos-small.jpg" border="0" alt="Solution Graphics">
            </td>
        </tr>
    </table>

    <?php if ( $_POST['billing'] == 'annual' ): ?>
        <input type="hidden" name="cost" value="<?php echo $_POST['annual_cost']; ?>" />
        <input type="hidden" name="billing" value="annual" />
    <?php elseif ( $_POST['billing'] == 'one_time' ): ?>
        <input type="hidden" name="cost" value="<?php echo $_POST['one_time_cost']; ?>" />
        <input type="hidden" name="billing" value="one_time" />
    <?php endif; ?>

    <input type="submit" name="payment_method_submit" value="Continue" />
</form>