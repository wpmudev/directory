<?php

/**
 * ct_admin_ui_add_custom_field()
 *
 * Outputs "Add Custom Field" admin page.
 *
 * @param array/false $post_types All available post types which
 * can be associated with the custom field that is about to be added.
 */
function ct_admin_ui_add_custom_field( $post_types ) { ?>

    <h3><?php _e('Add Custom Field', 'content_types'); ?></h3>
    <form action="" method="post" class="ct-custom-fields">
        <?php wp_nonce_field( 'ct_submit_custom_field_verify', 'ct_submit_custom_field_secret' ); ?>
        <div class="ct-wrap-left">
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Field Title', 'content_types') ?></h3>
                <table class="form-table <?php do_action('ct_invalid_field_title'); ?>">
                    <tr>
                        <th>
                            <label for="field_title"><?php _e('Field Title', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                        </th>
                        <td>
                            <input type="text" name="field_title" value="<?php echo( $_POST['field_title'] ); ?>">
                            <span class="description"><?php _e('The title of the custom field.', 'content_types'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Field Type', 'content_types') ?></h3>
                <table class="form-table <?php do_action('ct_invalid_field_options'); ?>">
                    <tr>
                        <th>
                            <label for="field_type"><?php _e('Field Type', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                        </th>
                        <td>
                            <select name="field_type">
                                <option value="text" <?php if ( $_POST['field_type'] == 'text' ) echo( 'selected="selected"' ); ?>>Text Box</option>
                                <option value="textarea" <?php if ( $_POST['field_type'] == 'textarea' ) echo( 'selected="selected"' ); ?>>Multi-line Text Box</option>
                                <option value="radio" <?php if ( $_POST['field_type'] == 'radio' ) echo( 'selected="selected"' ); ?>>Radio Buttons</option>
                                <option value="checkbox" <?php if ( $_POST['field_type'] == 'checkbox' ) echo( 'selected="selected"' ); ?>>Checkboxes</option>
                                <option value="selectbox" <?php if ( $_POST['field_type'] == 'selectbox' ) echo( 'selected="selected"' ); ?>>Drop Down Select Box</option>
                                <option value="multiselectbox" <?php if ( $_POST['field_type'] == 'multiselectbox' ) echo( 'selected="selected"' ); ?>>Multi Select Box</option>
                            </select>
                            <span class="description"><?php _e('Select one or more post types to add this custom field to.', 'content_types'); ?></span>
                            <div class="ct-field-type-options">
                                <h4><?php _e('Fill in the options for this field', 'content_types'); ?>:</h4>
                                <p><?php _e('Order By', 'content_types'); ?> :
                                    <select name="field_sort_order">
                                        <option value="default"><?php _e('Order Entered', 'content_types'); ?></option>
                                        <?php /** @todo introduce the additional order options
                                        <option value="asc"><?php _e('Name - Ascending', 'content_types'); ?></option>
                                        <option value="desc"><?php _e('Name - Descending', 'content_types'); ?></option>
                                        */ ?>
                                    </select
                                </p>
                                <p><?php _e('Option', 'content_types'); ?> 1:
                                    <input type="text" name="field_options[1]" value="<?php echo( $_POST['field_options'][1] ); ?>">
                                    <input type="radio" value="1" name="field_default_option" <?php if ( $_POST['field_default_option'] == '1' ) echo( 'checked="checked"' ); ?>>
                                    <?php _e('Default Value', 'content_types'); ?>
                                </p>
                                <div class="ct-field-additional-options"></div>
                                <input type="hidden" value="1" name="track_number">
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
                            <textarea class="ct-field-description" name="field_description" rows="3" ><?php echo( $_POST['field_description'] ); ?></textarea>
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
                <table class="form-table <?php do_action('ct_invalid_field_object_type'); ?>">
                    <tr>
                        <th>
                            <label for="object_type"><?php _e('Post Type', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                        </th>
                        <td>
                            <select name="object_type[]" multiple="multiple" class="ct-object-type">
                                <?php if ( !empty( $post_types )): ?>
                                    <?php foreach( $post_types as $post_type ): ?>
                                        <option value="<?php echo ( $post_type ); ?>" <?php foreach ( $_POST['object_type'] as $post_value ) { if ( $post_value == $post_type ) echo( 'selected="selected"' ); } ?>><?php echo ( $post_type ); ?></option>
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
        <input type="submit" class="button-primary" name="ct_submit_add_custom_field" value="Add Custom Field">
        <br /><br /><br /><br />
    </form> <?php
}
?>