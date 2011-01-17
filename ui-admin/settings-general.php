<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options(); ?>

<div class="wrap">
    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'sub' => 'general' ) ); ?>
    
    <form action="" method="post" class="dp-general">
        
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
                        <option value="default" <?php if ( isset( $options['general_settings']['order_taxonomies'] ) && $options['general_settings']['order_taxonomies'] == 'default' ) echo 'selected="selected"'; ?> ><?php _e( 'Order Entered', 'directory' ); ?></option>
                        <option value="alphabetical" <?php if ( isset( $options['general_settings']['order_taxonomies'] ) && $options['general_settings']['order_taxonomies'] == 'alphabetical' ) echo 'selected="selected"'; ?> ><?php _e( 'Alphabetically', 'directory' ); ?></option>
                    </select>
                    <span class="description"><?php _e('This setting will order the taxonomies.', 'directory'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php wp_nonce_field('verify'); ?>
            <input type="hidden" name="key" value="general_settings" />
            <input type="submit" class="button-primary" name="save" value="Save Changes">
        </p>
        
    </form>
    
</div>