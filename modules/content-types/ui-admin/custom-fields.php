<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $custom_fields = $this->custom_fields; ?>

<?php $this->render_admin('update-message'); ?>

<form action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="redirect_add_custom_field" value="Add Custom Field" />
</form>
<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Field Name', 'content_types'); ?></th>
            <th><?php _e('Field ID', 'content_types'); ?></th>
            <th><?php _e('Field Type', 'content_types'); ?></th>
            <th><?php _e('Description', 'content_types'); ?></th>
            <th><?php _e('Post Types', 'content_types'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><?php _e('Field Name', 'content_types'); ?></th>
            <th><?php _e('Field ID', 'content_types'); ?></th>
            <th><?php _e('Field Type', 'content_types'); ?></th>
            <th><?php _e('Description', 'content_types'); ?></th>
            <th><?php _e('Post Types', 'content_types'); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <?php if ( !empty( $custom_fields ) ): ?>
            <?php $i = 0; foreach ( $custom_fields as $custom_field ): ?>
            <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
            <tr class="<?php echo ( $class ); ?>">
                <td>
                    <strong>
                        <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] )); ?>"><?php echo( $custom_field['field_title'] ); ?></a>
                    </strong>
                    <div class="row-actions" id="row-actions-<?php echo $custom_field['field_id']; ?>" >
                        <span class="edit">
                            <a title="<?php _e('Edit this custom field', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] ) ); ?>"><?php _e( 'Edit', $this->text_domain ); ?></a> |
                        </span>
                        <span>
                            <a title="<?php _e('Show embed code', 'content_types'); ?>" href="#" onclick="javascript:content_types.toggle_embed_code('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e('Embed Code', 'content_types'); ?></a> |
                        </span>
                        <span class="trash">
                            <a class="submitdelete" href="#" onclick="javascript:content_types.toggle_delete('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e( 'Delete', $this->text_domain ); ?></a>
                        </span>
                    </div>
                    <form action="" method="post" id="form-<?php echo $custom_field['field_id']; ?>" class="del-form">
                        <?php wp_nonce_field('delete_custom_field'); ?>
                        <input type="hidden" name="custom_field_id" value="<?php echo $custom_field['field_id']; ?>" />
                        <input type="submit" class="button confirm" value="<?php _e( 'Confirm', $this->text_domain ); ?>" name="submit" />
                        <input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onClick="content_types.cancel('<?php echo $custom_field['field_id']; ?>'); return false;" />
                    </form>
                </td>
                <td><?php echo '_ct_' . $custom_field['field_id']; ?></td>
                <td><?php echo( $custom_field['field_type'] ); ?></td>
                <td><?php echo( $custom_field['field_description'] ); ?></td>
                <td>
                    <?php foreach( $custom_field['object_type'] as $object_type ): ?>
                        <?php echo( $object_type ); ?>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr id="embed-code-<?php echo $custom_field['field_id']; ?>" class="embed-code <?php echo ( $class ); ?>">
                <td colspan="10">
                    <div class="embed-code-wrap">
                        <span class="description"><?php _e( 'Returns the values of the custom fields with the specified key from the specified post.', $this->text_domain ); ?></span>
                        <br /><br />
                        <?php if ( $custom_field['field_type'] == 'text'|| $custom_field['field_type'] == 'textarea' ):  ?>
                            <code><span style="color:red">&lt;?php</span> echo <strong>get_post_meta(</strong> $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true <strong>)</strong>; <span style="color:red">?&gt;</span></code>
                        <?php else: ?>
                            <code><span style="color:red">&lt;?php</span> if ( <strong>get_post_meta(</strong> $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true <strong>)</strong> ) { foreach ( <strong>get_post_meta(</strong> $post->ID, '_ct_<?php echo( $custom_field['field_id'] ); ?>', true <strong>)</strong> as $value ) { echo $value . ', '; }} <span style="color:red">?&gt;</span></code>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<form action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="redirect_add_custom_field" value="Add Custom Field" />
</form>