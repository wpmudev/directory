<?php

/**
 * dp_admin_ui_settings_submit_site()
 *
 * Outputs "DirectoryPress Settings" admin page.
 *
 */
function dp_admin_ui_settings_submit_site( $options ) { ?>
    
    <form action="" method="post" class="dp-payments">
        <?php wp_nonce_field( 'dp_submit_settings_submit_site_verify', 'dp_submit_settings_submit_site_secret' ); ?>

        <?php /** @todo
        <div class="updated below-h2" id="message">
            <p><a href=""></a></p>
        </div> */ ?>
        
        <table class="form-table">
            <tr>
                <th>
                    <label for="annual_payment_option_price"><?php _e('Annual Payment Option', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="annual_payment_option_price" value="<?php echo $options['annual_price']; ?>" />
                    <span class="description"><?php _e('Price of "Annual" service.', 'directorypress'); ?></span>
                    <br /><br />
                    <input type="text" name="annual_payment_option_txt" value="<?php echo $options['annual_txt']; ?>" />
                    <span class="description"><?php _e('Text of "Annual" service.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="one_time_payment_option_price"><?php _e('One Time Payment Option', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="one_time_payment_option_price" value="<?php echo $options['one_time_price']; ?>" />
                    <span class="description"><?php _e('Price of "One Time" service.', 'directorypress'); ?></span>
                    <br /><br />
                    <input type="text" name="one_time_payment_option_txt" value="<?php echo $options['one_time_txt']; ?>" />
                    <span class="description"><?php _e('Text of "One Time" service.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="tos_txt"><?php _e('Terms of Service Text', 'directorypress') ?></label>
                </th>
                <td>
                    <textarea name="tos_txt" rows="15" cols="50"><?php echo $options['tos_txt']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Text for "Terms of Service"'); ?></span>
                </td>
            </tr>
        </table>
        <br />
        <input type="submit" class="button-primary" name="dp_submit_site_settings" value="Save Changes">
    </form> <?php
} ?>