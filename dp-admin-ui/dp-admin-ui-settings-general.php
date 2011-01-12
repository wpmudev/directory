<?php

/**
 * dp_admin_ui_settings_ads()
 *
 * Outputs "Directory Settings" admin page.
 *
 */
function dp_admin_ui_settings_general( $options ) { ?>

    <div class="clear"></div>
    <form action="" method="post" class="dp-general">
        <?php wp_nonce_field( 'dp_submit_settings_general_verify', 'dp_submit_settings_general_secret' ); ?>

        <?php /** @todo
        <div class="updated below-h2" id="message">
            <p><a href=""></a></p>
        </div> */ ?>
        <h3><?php _e( 'Import', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="import_taxonomies"><?php _e('Import Taxonomies', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" name="import_taxonomies" value="1" <?php if ( isset( $options['data']['taxonomies_loaded'] ) ) echo 'checked="checked" disabled="disabled"'; ?>  />
                    <span class="description"><?php _e('If you check this option a preconfigured set of taxonomies will be imported for you ( Recomended ). You can edit and remove them afterwards.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="import_custom_fields"><?php _e('Import Custom Fields', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" name="import_custom_fields" value="1" <?php if ( isset( $options['data']['custom_fields_loaded'] ) ) echo 'checked="checked" disabled="disabled"'; ?> />
                    <span class="description"><?php _e('If you check this option an example custom field will be imported for you ( Recomended ). You can edit and remove it afterwards.', 'directory'); ?></span>
                </td>
            </tr>
        </table>
        <h3><?php _e( 'Order', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="order_taxonomies"><?php _e('Order Taxonomies', 'directory') ?></label>
                </th>
                <td>
                    <select name="order_taxonomies">
                        <option value="default" <?php if ( $options['general_settings']['order_taxonomies'] == 'default' ) echo 'selected="selected"'; ?> >Order Entered</option>
                        <option value="alphabetical" <?php if ( $options['general_settings']['order_taxonomies'] == 'alphabetical' ) echo 'selected="selected"'; ?> >Alphabetically</option>
                    </select>
                    <span class="description"><?php _e('This setting will order the taxonomies.', 'directory'); ?></span>
                </td>
            </tr>
        </table>
        <br />
        <input type="submit" class="button-primary" name="dp_submit_general_settings" value="Save Changes">
    </form> <?php
} ?>