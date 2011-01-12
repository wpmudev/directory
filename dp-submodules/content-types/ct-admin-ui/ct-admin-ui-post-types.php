<?php

/**
 * ct_admin_ui_post_types()
 *
 * Outputs "Post Types" admin page.
 *
 * @param array/false $post_types All available custom post types with all
 * their settings.
 */
function ct_admin_ui_post_types( $post_types ) { ?>

    <?php /** @todo
    <div class="updated below-h2" id="message">
        <p><a href=""></a></p>
    </div> */ ?>
    <form name="ct_form_redirect_add_post_type" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_post_type" value="<?php _e('Add Post Type', 'content_types'); ?>" />
    </form>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Post Type', 'content_types'); ?></th>
                <th><?php _e('Name', 'content_types'); ?></th>
                <th><?php _e('Description', 'content_types'); ?></th>
                <th><?php _e('Menu Icon', 'content_types'); ?></th>
                <th><?php _e('Supports', 'content_types'); ?></th>
                <th><?php _e('Capability Type', 'content_types'); ?></th>
                <th><?php _e('Public', 'content_types'); ?></th>
                <th><?php _e('Hierarchical', 'content_types'); ?></th>
                <th><?php _e('Rewrite', 'content_types'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?php _e('Post Type', 'content_types'); ?></th>
                <th><?php _e('Name', 'content_types'); ?></th>
                <th><?php _e('Description', 'content_types'); ?></th>
                <th><?php _e('Menu Icon', 'content_types'); ?></th>
                <th><?php _e('Supports', 'content_types'); ?></th>
                <th><?php _e('Capability Type', 'content_types'); ?></th>
                <th><?php _e('Public', 'content_types'); ?></th>
                <th><?php _e('Hierarchical', 'content_types'); ?></th>
                <th><?php _e('Rewrite', 'content_types'); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php if ( !empty( $post_types )): ?>
                <?php $i = 0; foreach ( $post_types as $post_type => $args ): ?>
                <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
                <tr class="<?php echo ( $class ); ?>">
                    <td>
                        <strong>
                            <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $post_type ) ); ?>"><?php echo( $post_type ); ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="<?php _e('Edit this post type', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $post_type ) ); ?>"><?php _e('Edit', 'content_types'); ?></a> |
                            </span>
                            <span class="trash">
                                <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_delete_post_type=' . $post_type ) ); ?>"><?php _e('Delete', 'content_types'); ?></a>
                            </span>
                        </div>
                    </td>
                    <td><?php echo( $args['labels']['name'] ); ?></td>
                    <td><?php echo( $args['description'] ); ?></td>
                    <td>
                        <img src="<?php echo( $args['menu_icon'] ); ?>" alt="<?php if ( empty( $args['menu_icon'] ) ) echo( 'No Icon'); ?>" />
                    </td>
                    <td class="ct-supports">
                        <?php foreach ( $args['supports'] as $value ): ?>
                            <?php echo( $value ); ?>
                        <?php endforeach; ?>
                    </td>
                    <td><?php echo( $args['capability_type'] ); ?></td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['public'] === null ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/advanced.png' ); ?>" alt="<?php _e('Advanced', 'content_types'); ?>" title="<?php _e('Advanced', 'content_types'); ?>" />
                        <?php elseif ( $args['public'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['hierarchical'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['rewrite'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <form name="ct_form_redirect_add_post_type" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_post_type" value="<?php _e('Add Post Type', 'content_types'); ?>" />
    </form> <?php
}
?>