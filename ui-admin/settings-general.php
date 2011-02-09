<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$options = $this->get_options();
$allow_per_site_content_types = get_site_option('allow_per_site_content_types');
?>

<div class="wrap">
    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'sub' => 'general' ) ); ?>
    
    <form action="" method="post" class="dp-general">

        <?php if ( is_multisite() && is_super_admin() ): ?>
        <h3><?php _e( 'Allow', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="allow_per_site_content_types"><?php _e('Allow per site content types.', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" id="allow_per_site_content_types" name="allow_per_site_content_types" value="1" <?php if ( !empty( $allow_per_site_content_types ) ) echo 'checked="checked"'; ?>  />
                    <span class="description"><?php _e('If you enable this setting, sites on your network will be able to define their own content types. Please note that these content types are local to each site including the root one and the already defined network-wide content types types will not be present ( unless you uncheck this setting - they are preserved ). If you disable this setting ( default behaviour ) all sites on your network can use the network-wide content types set by you the Super Admin but they cannot modify them.', 'directory'); ?></span>
                </td>
            </tr>
        </table>
        <?php endif; ?>
        
        <h3><?php _e( 'Import', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="import_post_types"><?php _e('Import Post Types', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" id="import_post_types" name="import_post_types" value="1" <?php if ( isset( $options['data']['post_types_loaded'] ) ) echo 'checked="checked" disabled="disabled"'; ?>  />
                    <span class="description"><?php _e('If you check this option a preconfigured post type will be imported for you ( Recomended ). You can edit and remove it afterwards.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="import_taxonomies"><?php _e('Import Taxonomies', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" id="import_taxonomies" name="import_taxonomies" value="1" <?php if ( isset( $options['general_settings']['import_taxonomies'] ) ) echo 'checked="checked" disabled="disabled"'; ?>  />
                    <span class="description"><?php _e('If you check this option a preconfigured set of taxonomies will be imported for you ( Recomended ). You can edit and remove them afterwards.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="import_custom_fields"><?php _e('Import Custom Fields', 'directory') ?></label>
                </th>
                <td>
                    <input type="checkbox" id="import_custom_fields" name="import_custom_fields" value="1" <?php if ( isset( $options['general_settings']['import_custom_fields'] ) ) echo 'checked="checked" disabled="disabled"'; ?> />
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
                    <select id="order_taxonomies" name="order_taxonomies">
                        <option value="default" <?php if ( isset( $options['general_settings']['order_taxonomies'] ) && $options['general_settings']['order_taxonomies'] == 'default' ) echo 'selected="selected"'; ?> ><?php _e( 'Order Entered', 'directory' ); ?></option>
                        <option value="alphabetical" <?php if ( isset( $options['general_settings']['order_taxonomies'] ) && $options['general_settings']['order_taxonomies'] == 'alphabetical' ) echo 'selected="selected"'; ?> ><?php _e( 'Alphabetically', 'directory' ); ?></option>
                    </select>
                    <span class="description"><?php _e('This setting will order the available taxonomies.', 'directory'); ?></span>
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
