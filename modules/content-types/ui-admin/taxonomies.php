<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $taxonomies = $this->taxonomies; ?>

<?php $this->render_admin('update-message'); ?>

<form action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="redirect_add_taxonomy" value="<?php _e('Add Taxonomy', 'content_types'); ?>" />
</form>
<table class="widefat">
    <thead>
        <tr>
            <th><?php _e('Taxonomy', 'content_types'); ?></th>
            <th><?php _e('Name', 'content_types'); ?></th>
            <th><?php _e('Post Types', 'content_types'); ?></th>
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
            <th><?php _e('Public', 'content_types'); ?></th>
            <th><?php _e('Hierarchical', 'content_types'); ?></th>
            <th><?php _e('Rewrite', 'content_types'); ?></th>
        </tr>
    </tfoot>
    <tbody>
        <?php if ( !empty( $taxonomies )): ?>
            <?php $i = 0; foreach ( $taxonomies as $name => $taxonomy ): ?>
            <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
            <tr class="<?php echo ( $class ); ?>">
                <td>
                    <strong>
                        <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $name ) ); ?>"><?php echo( $name ); ?></a>
                    </strong>
                    <div class="row-actions">
                        <span class="edit">
                            <a title="<?php _e('Edit this taxonomy', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $name ) ); ?>"><?php _e('Edit', 'content_types'); ?></a> |
                        </span>
                        <span>
                            <a id="embed-code-link-<?php echo $i; ?>" title="<?php _e('Show embed code', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $name ) ); ?>" onclick="javascript:embed_code.toggle_embed_code('<?php echo $i; ?>'); return false;"><?php _e('Embed Code', 'content_types'); ?></a> |
                        </span>
                        <span class="trash">
                            <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_delete_taxonomy=' . $name ) ); ?>"><?php _e('Delete', 'content_types'); ?></a>
                        </span>
                    </div>
                </td>
                <td><?php echo( $taxonomy['args']['labels']['name'] ); ?></td>
                <td>
                    <?php foreach( $taxonomy['object_type'] as $object_type ): ?>
                        <?php echo( $object_type ); ?>
                    <?php endforeach; ?>
                </td>
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
            <tr id="embed-code-<?php echo $i; ?>" class="embed-code <?php echo ( $class ); ?>">
                <td colspan="10">
                    <div class="embed-code-wrap">
                        <span class="description"><?php _e('Returns an HTML string of taxonomy terms associated with a post and given taxonomy. Terms are linked to their respective term listing pages. Use it inside the Loop.', $this->text_domain ); ?></span>
                        <br /><br />
                        <code><span style="color:red">&lt;?php</span> echo <strong>get_the_term_list(</strong> $post->ID, '<?php echo( $name ); ?>', 'Before: ', ', ', 'After' <strong>)</strong>; <span style="color:red">?&gt;</span></code>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<form name="ct_form_redirect_add_taxonomy" action="" method="post" class="ct-form-single-btn">
    <input type="submit" class="button-secondary" name="ct_redirect_add_taxonomy" value="<?php _e('Add Taxonomy', 'content_types'); ?>" />
</form>