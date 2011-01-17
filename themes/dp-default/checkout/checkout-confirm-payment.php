<?php $transaction_details = dp_gateway_paypal_express_get_payment_details(); ?>

<form action="" method="post">

    <span class="dp-submit-txt"><?php _e('Confirm Payment', 'directory'); ?></span>

    <table>
        <tr>
            <th><label><?php _e('Email Adress','directory'); ?>:</label></th>
            <td><?php echo $transaction_details['EMAIL']; ?></td>
        </tr>
        <tr>
            <th><label><?php _e('Name', 'directory'); ?>:</label></th>
            <td><?php echo $transaction_details['FIRSTNAME'] . ' ' . $transaction_details['LASTNAME']; ?></td>
        </tr>
        <tr>
            <th><label><?php _e('Address', 'directory'); ?>:</label></th>
            <td><?php echo $transaction_details['SHIPTOSTREET']; ?>, <?php echo $transaction_details['SHIPTOCITY']; ?>, <?php echo $transaction_details['SHIPTOSTATE']; ?>, <?php echo $transaction_details['SHIPTOZIP']; ?>, <?php echo $transaction_details['SHIPTOCOUNTRYNAME']; ?></td>
        </tr>
        <tr>
            <th><label><?php _e('Total Amount', 'directory'); ?>:</label></th>
            <td>
                <strong><?php echo $transaction_details['AMT'] . ' ' . $transaction_details['CURRENCYCODE'] ?></strong>
                <input type="hidden" name="total_amount" value="<?php echo $transaction_details['AMT']; ?>" />
            </td>
        </tr>
    </table>

    <input type="hidden" name="email" value="<?php echo $transaction_details['EMAIL']; ?>" />
    <input type="hidden" name="first_name" value="<?php echo $transaction_details['FIRSTNAME']; ?>" />
    <input type="hidden" name="last_name" value="<?php echo $transaction_details['LASTNAME']; ?>" />
    <input type="hidden" name="billing" value="<?php echo $_SESSION['billing']; ?>" />

    <input type="submit" name="confirm_payment_submit" value="Confirm Payment" />
    
</form>