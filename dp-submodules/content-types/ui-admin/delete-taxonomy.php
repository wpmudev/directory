<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$taxonomy = $this->taxonomies[$_GET['ct_delete_taxonomy']];
$nonce = wp_create_nonce('delete_taxonomy');
?>

<form action="" method="post" class="ct-form-single-btn">
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    <input type="submit" class="button-secondary" name="submit" value="<?php _e('Delete Permanently', 'content_types'); ?>" />
</form>
<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Taxonomy', 'content_types'); ?></th>
            <th><?php _e('Name', 'content_types'); ?></th>
            <th><?php _e('Post Types', 'content_types'); ?></th>
            <th><?php _e('Embed Code', 'content_types'); ?></th>
            <th><?php _e('Public', 'content_types'); ?></th>
            <th><?php _e('Hierarchical', 'content_types'); ?></th>
            <th><?php _e('Rewrite', 'content_types'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><?php _e('Taxonomy', 'content_types'); ?></th>
            <th><?php _e('Name', 'content_types'); ?></th>
            <th><?php _e('Post Types', 'content_types'); ?></th>
            <th><?php _e('Embed Code', 'content_types'); ?></th>
            <th><?php _e('Public', 'content_types'); ?></th>
            <th><?php _e('Hierarchical', 'content_types'); ?></th>
            <th><?php _e('Rewrite', 'content_types'); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td>
                <strong>
                    <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $_GET['ct_delete_taxonomy'] )); ?>"><?php echo( $_GET['ct_delete_taxonomy'] ); ?></a>
                </strong>
                <div class="row-actions">
                    <span class="edit">
                        <a title="<?php _e('Edit this taxonomy', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $_GET['ct_delete_taxonomy'] )); ?>"><?php _e('Edit', 'content_types'); ?></a> |
                    </span>
                    <span class="trash">
                        <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_delete_taxonomy=' . $_GET['ct_delete_taxonomy'] . '&_wpnonce=' . $nonce . '&submit' )); ?>"><?php _e('Delete Permanently', 'content_types'); ?></a>
                    </span>
                </div>
            </td>
            <td><?php echo( $taxonomy['args']['labels']['name'] ); ?></td>
            <td>
                <?php foreach( $taxonomy['object_type'] as $object_type ): ?>
                    <?php echo( $object_type ); ?>
                <?php endforeach; ?>
            </td>
            <td><code>&lt;?php echo get_the_term_list( $post->ID, '<?php echo( $_GET['ct_delete_taxonomy'] ); ?>', 'Text before taxonomy: ', ', ', '' ); ?&gt;</code></td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $taxonomy['args']['public'] === null ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/advanced.png' ); ?>" alt="<?php _e('Advanced', 'content_types'); ?>" title="<?php _e('Advanced', 'content_types'); ?>" />
                <?php elseif ( $taxonomy['args']['public'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $taxonomy['args']['hierarchical'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
            <td class="ct-tf-icons-wrap">
                <?php if ( $taxonomy['args']['rewrite'] ): ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                <?php else: ?>
                    <img class="ct-tf-icons" src="<?php echo ( $this->submodule_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
<form action="" method="post" class="ct-form-single-btn">
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    <input type="submit" class="button-secondary" name="submit" value="<?php _e('Delete Permanently', 'content_types'); ?>" />
</form>
