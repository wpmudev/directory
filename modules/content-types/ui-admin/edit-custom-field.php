<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_types = get_post_types('','names');
$custom_field = $this->custom_fields[$_GET['ct_edit_custom_field']];
?>

<h3><?php _e('Edit Custom Field', 'content_types'); ?></h3>
<form action="" method="post" class="ct-custom-fields">
    <?php wp_nonce_field( 'ct_submit_custom_field_verify', 'ct_submit_custom_field_secret' ); ?>
    <div class="ct-wrap-left">
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Field Title', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="field_title"><?php _e('Field Title', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                    </th>
                    <td>
                        <input type="text" name="field_title" value="<?php echo ( $custom_field['field_title'] ); ?>">
                        <span class="description"><?php _e('The title of the custom field.', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Field Type', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="field_type"><?php _e('Field Type', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                    </th>
                    <td>
                        <select name="field_type">
                            <option value="text" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'text' ) echo ( 'selected="selected"' ); ?>>Text Box</option>
                            <option value="textarea" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'textarea' ) echo ( 'selected="selected"' ); ?>>Multi-line Text Box</option>
                            <option value="radio" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'radio' ) echo ( 'selected="selected"' ); ?>>Radio Buttons</option>
                            <option value="checkbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'checkbox' ) echo ( 'selected="selected"' ); ?>>Checkboxes</option>
                            <option value="selectbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'selectbox' ) echo ( 'selected="selected"' ); ?>>Drop Down Select Box</option>
                            <option value="multiselectbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'multiselectbox' ) echo ( 'selected="selected"' ); ?>>Multi Select Box</option>
                        </select>
                        <span class="description"><?php _e('Select one or more post types to add this custom field to.', 'content_types'); ?></span>
                        <div class="ct-field-type-options">
                            <h4><?php _e('Fill in the options for this field', 'content_types'); ?>:</h4>
                            <p>
                                <?php _e('Order By', 'content_types'); ?> :
                                <select name="field_sort_order">
                                    <option value="default"><?php _e('Order Entered', 'content_types'); ?></option>
                                    <?php /** @todo introduce the additional order options
                                    <option value="asc"><?php _e('Name - Ascending', 'content_types'); ?></option>
                                    <option value="desc"><?php _e('Name - Descending', 'content_types'); ?></option>
                                    */ ?>
                                </select
                            </p>

                            <?php if ( isset( $custom_field['field_options'] ) && is_array( $custom_field['field_options'] )): ?>
                                <?php foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
                                    <p>
                                        <?php _e('Option', 'content_types'); ?> <?php echo( $key ); ?>:
                                        <input type="text" name="field_options[<?php echo( $key ); ?>]" value="<?php echo( $field_option ); ?>" />
                                        <input type="radio" value="<?php echo( $key ); ?>" name="field_default_option" <?php if ( $custom_field['field_default_option'] == $key ) echo ( 'checked="checked"' ); ?> />
                                        <?php _e('Default Value', 'content_types'); ?>
                                        <?php if ( $key != 1 ): ?>
                                            <a href="#" class="ct-field-delete-option">[x]</a>
                                        <?php endif; ?>
                                    </p>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="ct-field-additional-options"></div>
                            <input type="hidden" value="<?php if ( isset( $custom_field['field_options'] ) ) echo count( $custom_field['field_options'] ); ?>" name="track_number">
                            <p><a href="#" class="ct-field-add-option"><?php _e('Add another option', 'content_types'); ?></a></p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Field Description', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="field_description"><?php _e('Field Description', 'content_types') ?></label>
                    </th>
                    <td>
                        <textarea name="field_description" cols="52" rows="3" ><?php echo( $custom_field['field_description'] ); ?></textarea>
                        <span class="description"><?php _e('Description for the custom field.', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="ct-wrap-right">
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Post Type', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="object_type"><?php _e('Post Type', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                    </th>
                    <td>
                        <select name="object_type[]" multiple="multiple" class="ct-object-type">
                            <?php if ( !empty( $post_types )): ?>
                                <?php foreach( $post_types as $post_type ): ?>
                                    <option value="<?php echo ( $post_type ); ?>" <?php foreach ( $custom_field['object_type'] as $key => $object_type ) { if ( $object_type == $post_type ) echo( 'selected="selected"' ); } ?>><?php echo ( $post_type ); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <br />
                        <span class="description"><?php _e('Select one or more post types to add this custom field to.', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <?php /** @todo implement required fields
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Required Field', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="required"><?php _e('Required Field', 'content_types') ?></label>
                    </th>
                    <td>
                        <span class="description"><?php _e('Should this field be required.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="required" value="1">
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="required" value="0" checked="checked">
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                    </td>
                </tr>
            </table>
        </div>
        */ ?>
    </div>
    <br style="clear: left" />
    <input type="submit" class="button-primary" name="ct_submit_update_custom_field" value="Update Custom Field">
    <br /><br /><br /><br />
</form>