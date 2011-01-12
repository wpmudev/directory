<?php

/**
 * ct_admin_ui_taxonomies()
 *
 * Outputs "Taxonomies" admin page.
 *
 * @param array/false $taxonomies All available custom taxonomies with all
 * their settings.
 */
function ct_admin_ui_taxonomies( $taxonomies ) { ?>

    <?php /** @todo
    <div class="updated below-h2" id="message">
        <p><a href=""></a></p>
    </div> */ ?>
    <form name="ct_form_redirect_add_taxonomy" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_taxonomy" value="<?php _e('Add Taxonomy', 'content_types'); ?>" />
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
            <?php if ( !empty( $taxonomies )): ?>
                <?php $i = 0; foreach ( $taxonomies as $taxonomy => $args ): ?>
                <?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
                <tr class="<?php echo ( $class ); ?>">
                    <td>
                        <strong>
                            <a href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $taxonomy ) ); ?>"><?php echo( $taxonomy ); ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a title="<?php _e('Edit this taxonomy', 'content_types'); ?>" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $taxonomy ) ); ?>"><?php _e('Edit', 'content_types'); ?></a> |
                            </span>
                            <span class="trash">
                                <a class="submitdelete" href="<?php echo( admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_delete_taxonomy=' . $taxonomy ) ); ?>"><?php _e('Delete', 'content_types'); ?></a>
                            </span>
                        </div>
                    </td>
                    <td><?php echo( $args['args']['labels']['name'] ); ?></td>
                    <td>
                        <?php foreach( $args['object_type'] as $object_type ): ?>
                            <?php echo( $object_type ); ?>
                        <?php endforeach; ?>
                    </td>
                    <td><code>&lt;?php echo get_the_term_list( $post->ID, '<?php echo( $taxonomy ); ?>', 'Text before taxonomy: ', ', ', '' ); ?&gt;</code></td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['args']['public'] === null ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/advanced.png' ); ?>" alt="<?php _e('Advanced', 'content_types'); ?>" title="<?php _e('Advanced', 'content_types'); ?>" />
                        <?php elseif ( $args['args']['public'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['args']['hierarchical'] ): ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/true.png' ); ?>" alt="<?php _e('True', 'content_types'); ?>" title="<?php _e('True', 'content_types'); ?>" />
                        <?php else: ?>
                            <img class="ct-tf-icons" src="<?php echo ( CT_SUBMODULE_URL . 'ct-admin-ui/images/false.png' ); ?>" alt="<?php _e('False', 'content_types'); ?>" title="<?php _e('False', 'content_types'); ?>" />
                        <?php endif; ?>
                    </td>
                    <td class="ct-tf-icons-wrap">
                        <?php if ( $args['args']['rewrite'] ): ?>
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
    <form name="ct_form_redirect_add_taxonomy" action="" method="post" class="ct-form-single-btn">
        <input type="submit" class="button-secondary" name="ct_redirect_add_taxonomy" value="<?php _e('Add Taxonomy', 'content_types'); ?>" />
    </form> <?php
}
?>
