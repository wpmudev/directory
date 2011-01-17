<?php

/**
 * dp_admin_ui_settings_ads()
 *
 * Outputs "Directory Settings" admin page.
 *
 */
function dp_admin_ui_settings_ads( $options ) { ?>
    
    <form action="" method="post" class="dp-ads">
        <?php wp_nonce_field( 'dp_submit_settings_ads_verify', 'dp_submit_settings_ads_secret' ); ?>

        <?php /** @todo
        <div class="updated below-h2" id="message">
            <p><a href=""></a></p>
        </div> */ ?>
        
        <table class="form-table">
            <tr>
                <th>
                    <label for="h_ad_code"><?php _e('Ad Code Header Banner', 'directory') ?></label>
                </th>
                <td>
                    <textarea name="h_ad_code" rows="15" cols="50"><?php echo $options['h_ad']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Place ad code here!', 'directory'); ?></span>
                </td>
            </tr>
        </table>
        <br />
        <input type="submit" class="button-primary" name="dp_submit_ads_settings" value="Save Changes">
    </form> <?php
} ?>