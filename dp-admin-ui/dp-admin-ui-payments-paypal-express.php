<?php

/**
 * dp_admin_ui_payments()
 *
 * Outputs "DirectoryPress Settings" admin page.
 *
 */
function dp_admin_ui_payments_paypal_express( $options ) { ?>
    
    <form action="" method="post" class="dp-payments">
        <?php wp_nonce_field( 'dp_submit_paypal_express_settings_verify', 'dp_submit_paypal_express_settings_secret' ); ?>
        <?php /** @todo
        <div class="updated below-h2" id="message">
            <p><a href=""></a></p>
        </div> */ ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="paypal_express_url"><?php _e('PayPal API Calls URL', 'directorypress') ?></label>
                </th>
                <td>
                    <select name="paypal_express_url">
                        <option <?php if ( $options['paypal']['api_url'] == 'Sandbox' ) echo 'selected="selected"' ?>>Sandbox</option>
                        <option <?php if ( $options['paypal']['api_url'] == 'Live' )    echo 'selected="selected"' ?>>Live</option>
                    </select>
                    <span class="description"><?php _e('Choose between PayPal Snadbox and PayPal Live.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="paypal_express_api_username"><?php _e('API Username', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="paypal_express_api_username" value="<?php echo $options['paypal']['api_username']; ?>" />
                    <span class="description"><?php _e('Your PayPal API Username.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="paypal_express_api_password"><?php _e('API Password', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="paypal_express_api_password" value="<?php echo $options['paypal']['api_password']; ?>" />
                    <span class="description"><?php _e('Your PayPal API Password.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="paypal_express_api_signature"><?php _e('API Signature', 'directorypress') ?></label>
                </th>
                <td>
                    <textarea rows="1" cols="55" name="paypal_express_api_signature"><?php echo $options['paypal']['api_signature']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Your PayPal API Signature.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="paypal_express_currency_code"><?php _e('Currency Code', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="paypal_express_currency_code" value="<?php echo $options['paypal']['currency_code']; ?>" />
                    <br />
                    <span class="description"><?php _e('3 letter uppercase currency code: ex.: USD', 'directorypress'); ?></span>
                </td>
            </tr>
        </table>
        <br />
        <input type="submit" class="button-primary" name="dp_submit_paypal_express_settings" value="Save Changes">
    </form> <?php
} ?>