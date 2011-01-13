<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_type = $this->post_types[$_GET['ct_delete_post_type']];
$nonce = wp_create_nonce('delete_post_type');
?>

<form action="" method="post" class="ct-form-single-btn">
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    <input type="submit" class="button-secondary" name="submit" value="<?php _e('Delete Permanently', 'content_types'); ?>" />
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
        <tr>
            <td>
                <strong>
                    <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $_GET['ct_delete_post_type'] ) ); ?>"><?php echo( $_GET['ct_delete_post_type'] ); ?></a>
                </strong>
                <div class="row-actions">
                    <span class="edit">
                        <a title="<?php _e('Edit this post type', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_edit_post_type=' . $_GET['ct_delete_post_type'] ) ); ?>"><?php _e('Edit', 'content_types'); ?></a> |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=post_type&ct_delete_post_type=' . $_GET['ct_delete_post_type'] . '&_wpnonce=' . $nonce . '&submit' ) ); ?>"><?php _e('Delete Permanently', 'content_types'); ?></a>
                    </span>
                </div>
            </td>
            <td><?php echo( $post_type['labels']['name'] ); ?></td>
            <td><?php echo( $post_type['description'] ); ?></td>
            <td>
                <img src="<?php echo( $post_type['menu_icon'] ); ?>" alt="<?php if ( empty( $post_type['menu_icon'] ) ) echo( 'No Icon'); ?>" />
            </td>
            <td class="ct-supports">
                <?php foreach ( $post_type['supports'] as $value ): ?>
                    <?php echo( $value ); ?>
                <?php endforeach; ?>
            </td>
            <td><?php echo( $post_type['capability_type'] ); ?></td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $post_type['public'] === null ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/advanced.png' ); ?>" alt="<?php _e('Advanced', 'content_types'); ?>" title="<?php _e('Advanced', 'content_types'); ?>" />
                <?php elseif ( $post_type['public'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $post_type['hierarchical'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $post_type['rewrite'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->cubmodule_url . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>

<form action="" method="post" class="ct-form-single-btn">
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    <input type="submit" class="button-secondary" name="submit" value="<?php _e('Delete Permanently', 'content_types'); ?>" />
</form>