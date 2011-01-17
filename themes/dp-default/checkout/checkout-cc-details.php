<?php $options = get_site_option('dp_options'); ?>

<form action="" method="post">

    <span class="dp-submit-txt"><?php _e('Confirm Payment', 'directory'); ?></span>
    <div class="clear" ></div>
    <table class="dp-sys-info">
        <tr>
            <th><label for="email"><?php _e('Email Adress','directory'); ?>:</label></th>
            <td><input type="text" name="email" value="" /></td>
        </tr>
        <tr>
            <th><label for="first-name"><?php _e('First Name', 'directory'); ?>:</label></th>
            <td><input type="text" name="first_name" value="" /></td>
        </tr>
        <tr>
            <th><label for="last-name"><?php _e('Last Name', 'directory'); ?>:</label></th>
            <td><input type="text" name="last_name" value="" /></td>
        </tr>
        <tr>
            <th><label for="street"><?php _e('Street', 'directory'); ?>:</label></th>
            <td><input type="text" name="street" value="" /></td>
        </tr>
        <tr>
            <th><label for="city"><?php _e('City', 'directory'); ?>:</label></th>
            <td><input type="text" name="city" value="" /></td>
        </tr>
        <tr>
            <th><label for="state"><?php _e('State', 'directory'); ?>:</label></th>
            <td><input type="text" name="state" value="" /></td>
        </tr>
        <tr>
            <th><label for="zip"><?php _e('ZIP', 'directory'); ?>:</label></th>
            <td><input type="text" name="zip" value="" /></td>
        </tr>
        <tr>
            <th><label for="country-code"><?php _e('Country Code', 'directory'); ?>:</label></th>
            <td><input type="text" name="country_code" value="" /></td>
        </tr>
        <tr>
            <th><?php _e('Total Amount', 'directory'); ?>:</th>
            <td>
                <strong><?php echo $_POST['cost']; ?> <?php echo $options['paypal']['currency_code']; ?></strong>
                <input type="hidden" name="total_amount" value="<?php echo $_POST['cost']; ?>" />
            </td>
        </tr>
    </table>

    <table class="dp-prs-info">
        <tr>
            <th><label for="cc_type"><?php _e('Credit Card Type', 'directory'); ?>:</label></th>
            <td>
                <select name="cc_type">
                    <option><?php _e('Visa', 'directory'); ?></option>
                    <option><?php _e('MasterCard', 'directory'); ?></option>
                    <option><?php _e('Amex', 'directory'); ?></option>
                    <option><?php _e('Discover', 'directory'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="cc_number"><?php _e('Credit Card Number', 'directory'); ?>:</label></th>
            <td><input type="text" name="cc_number" /></td>
        </tr>
        <tr>
            <th><label for="exp_date"><?php _e('Expiration Date', 'directory'); ?>:</label></th>
            <td><input type="text" name="exp_date" /></td>
        </tr>
        <tr>
            <th><label for="cvv2"><?php _e('CVV2', 'directory'); ?>:</label></th>
            <td><input type="text" name="cvv2" /></td>
        </tr>
    </table>

    <div class="clear"></div>
    <input type="submit" name="direct_payment_submit" value="Continue" />

</form>