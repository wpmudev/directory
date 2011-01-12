<?php

/**
 * dp_admin_ui_payments()
 *
 * Outputs "DirectoryPress Settings" admin page.
 *
 */
function dp_admin_ui_payments_authorize_net() { ?>
    
    <form action="" method="post" class="dp-payments">
        <?php wp_nonce_field( 'dp_submit_settings_verify', 'dp_submit_settings_secret' ); ?>
        <?php /** @todo
        <div class="updated below-h2" id="message">
            <p><a href=""></a></p>
        </div> */ ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="post_type"><?php _e('Under Development', 'directorypress') ?></label>
                </th>
                <td>
                </td>
            </tr>
            <?php /*
            <tr>
                <th>
                    <label for="post_type"><?php _e('Authorize.net API Password', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="authorize_net_api_username" />
                    <span class="description"><?php _e('Your Authorize.net API Password.', 'directorypress'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="post_type"><?php _e('Authorize.net API Signature', 'directorypress') ?></label>
                </th>
                <td>
                    <input type="text" name="authorize_net_api_username" />
                    <span class="description"><?php _e('Your Authorize.net API Signature.', 'directorypress'); ?></span>
                </td>
            </tr>
             *        <br />
        <input type="submit" class="button-primary" name="dp_submit_auth_settings" value="Save Changes">
             */ ?>
        </table>

    </form> <?php
} ?>