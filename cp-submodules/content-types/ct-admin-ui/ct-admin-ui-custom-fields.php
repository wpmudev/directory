<?php

/**
 * ct_admin_ui_custom_fields()
 *
 * Outputs "Custom Fields" admin page.
 *
 * @param array/false $custom_fields All available custom fields with all
 * their settings.
 */
function ct_admin_ui_custom_fields( $custom_fields ) { ?>

    <?php /** @todo
    <div class="updated below-h2" id="message">
        <p><a href=""></a></p>
    </div> */ ?>
    <form name="ct_form_redirect_add_custom_field" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_custom_field" value="Add Custom Field" />
    </form>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Custom Field', 'content_types'); ?></th>
                <th><?php _e('Field Type', 'content_types'); ?></th>
                <th><?php _e('Description', 'content_types'); ?></th>
                <th><?php _e('Post Types', 'content_types'); ?></th>
                <th><?php _e('Embed Code', 'content_types'); ?></th>
                <?php /** @todo implement required fields
                <th><?php _e('Required', 'content_types'); ?></th>
                 */ ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?php _e('Custom Field', 'content_types'); ?></th>
                <th><?php _e('Field Type', 'content_types'); ?></th>
                <th><?php _e('Description', 'content_types'); ?></th>
                <th><?php _e('Post Types', 'content_types'); ?></th>
                <th><?php _e('Embed Code', 'content_types'); ?></th>
                <?php /** @todo implement required fields
                <th><?php _e('Required', 'content_types'); ?></th>
                 */ ?>
            </tr>
        </tfoot>
        <tbody>
            <?php if ( !empty( $custom_fields )): ?>
                <?php $i = 0; foreach ( $custom_fields as $custom_field ): ?>
                <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
                <tr class="<?php echo ( $class ); ?>">
                    <td>
                        <strong>
                            <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] )); ?>"><?php echo( $custom_field['field_title'] ); ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="<?php _e('Edit this custom field', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] ) ); ?>">Edit</a> |
                            </span>
                            <span class="trash">
                                <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_delete_custom_field=' . $custom_field['field_id'] ) ); ?>">Delete</a>
                            </span>
                        </div>
                    </td>
                    <td><?php echo( $custom_field['field_type'] ); ?></td>
                    <td><?php echo( $custom_field['field_description'] ); ?></td>
                    <td>
                        <?php foreach( $custom_field['object_type'] as $object_type ): ?>
                            <?php echo( $object_type ); ?>
                        <?php endforeach; ?>
                    </td>
                    <?php if ( $custom_field['field_type'] == 'text'|| $custom_field['field_type'] == 'textarea' ):  ?>
                        <td class="ct-embed-code"><code>&lt;?php echo get_post_meta( $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true ); ?&gt;</code></td>
                    <?php else: ?>
                        <td class="ct-embed-code"><code>&lt;?php if ( get_post_meta( $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true )) { foreach ( get_post_meta( $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true ) as $value ) { echo $value . ', '; }} ?&gt;</code>
                        </td>
                    <?php endif; ?>
                    <?php /** @todo implement required fields
                    <td>
                        <?php if ( $custom_field['required'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                    */ ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <form name="ct_form_redirect_add_custom_field" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_custom_field" value="Add Custom Field" />
    </form> <?php
}
?>