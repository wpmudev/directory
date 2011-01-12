<?php

/**
 * ct_admin_ui_edit_taxonomy()
 *
 * Outputs "Edit Taxonomy" admin page.
 *
 * @param array $args The taxonomy that is about to be updated with all of
 * its settings.
 * @param array $post_types All available post types that can be associated
 * with the current taxonomy.
 */
function ct_admin_ui_edit_taxonomy( $args, $post_types ) { ?>

    <h3><?php _e('Edit Taxonomy', 'content_types'); ?></h3>
    <form action="" method="post" class="ct-taxonomy">
        <?php wp_nonce_field( 'ct_submit_taxonomy_verify', 'ct_submit_taxonomy_secret' ); ?>
        <div class="ct-wrap-left">
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Taxonomy', 'content_types') ?></h3>
                <table class="form-table <?php do_action('ct_invalid_field_taxonomy_class'); ?>">
                    <tr>
                        <th>
                            <label for="taxonomy"><?php _e('Taxonomy', 'content_types') ?> (<span class="ct-required"> required </span>)</label>
                        </th>
                        <td>
                            <input type="text" name="taxonomy" value="<?php echo ( $_GET['ct_edit_taxonomy'] ); ?>" disabled="disabled">
                            <span class="description"><?php _e('The system name of the taxonomy. Alphanumeric characters and underscores only. Min 2 letters. Once added the taxonomy system name cannot be changed.', 'content_types'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Post Type', 'content_types') ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="object_type[]"><?php _e('Post Type', 'content_types') ?> (<span class="ct-required"> required </span>)</label>
                        </th>
                        <td>
                            <select name="object_type[]" multiple="multiple" class="ct-object-type">
                                <?php if ( !empty( $post_types )): ?>
                                    <?php foreach( $post_types as $post_type ): ?>
                                        <option value="<?php echo ( $post_type ); ?>" <?php if ( is_object_in_taxonomy( $post_type, $_GET['ct_edit_taxonomy'] )) echo ( 'selected="selected"'); ?>><?php echo ( $post_type ); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <span class="description"><?php _e('Select one or more post types to add this taxonomy to', 'content_types'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Labels', 'content_types') ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="name"><?php _e('Name', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[name]" value="<?php echo ( $args['args']['labels']['name'] ); ?>">
                            <span class="description"><?php _e('General name for the taxonomy, usually plural.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="singular_name"><?php _e('Singular Name', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[singular_name]" value="<?php echo ( $args['args']['labels']['singular_name'] ); ?>">
                            <span class="description"><?php _e('Name for one object of this taxonomy. Defaults to value of name.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="add_new_item"><?php _e('Add New Item', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[add_new_item]" value="<?php echo ( $args['args']['labels']['add_new_item'] ); ?>">
                            <span class="description"><?php _e('The add new item text. Default is "Add New Tag" or "Add New Category".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="new_item_name"><?php _e('New Item Name', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[new_item_name]" value="<?php echo ( $args['args']['labels']['new_item_name'] ); ?>">
                            <span class="description"><?php _e('The new item name text. Default is "New Tag Name" or "New Category Name".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="edit_item"><?php _e('Edit Item', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[edit_item]" value="<?php echo ( $args['args']['labels']['edit_item'] ); ?>">
                            <span class="description"><?php _e('The edit item text. Default is "Edit Tag" or "Edit Category".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="update_item"><?php _e('Update Item', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[update_item]" value="<?php echo ( $args['args']['labels']['update_item'] ); ?>">
                            <span class="description"><?php _e('The update item text. Default is "Update Tag" or "Update Category".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="search_items"><?php _e('Search Items', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[search_items]" value="<?php echo ( $args['args']['labels']['search_items'] ); ?>">
                            <span class="description"><?php _e('The search items text. Default is "Search Tags" or "Search Categories".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="popular_items"><?php _e('Popular Items', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[popular_items]" value="<?php echo ( $args['args']['labels']['popular_items'] ); ?>">
                            <span class="description"><?php _e('The popular items text. Default is "Popular Tags" or null.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="all_items"><?php _e('All Items', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[all_items]" value="<?php echo ( $args['args']['labels']['all_items'] ); ?>">
                            <span class="description"><?php _e('The all items text. Default is "All Tags" or "All Categories".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="parent_item"><?php _e('Parent Item', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[parent_item]" value="<?php echo ( $args['args']['labels']['parent_item'] ); ?>">
                            <span class="description"><?php _e('The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or "Parent Category".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="parent_item_colon"><?php _e('Parent Item Colon', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[parent_item_colon]" value="<?php echo ( $args['args']['labels']['parent_item_colon'] ); ?>">
                            <span class="description"><?php _e('The same as parent_item, but with colon : in the end null, "Parent Category:".', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="add_or_remove_items"><?php _e('Add Or Remove Items', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[add_or_remove_items]" value="<?php echo ( $args['args']['labels']['add_or_remove_items'] ); ?>">
                            <span class="description"><?php _e('The add or remove items text is used in the meta box when JavaScript is disabled. This string isn\'t used on hierarchical taxonomies. Default is "Add or remove tags" or null.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="separate_items_with_commas"><?php _e('Separate Items With Commas', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[separate_items_with_commas]" value="<?php echo ( $args['args']['labels']['separate_items_with_commas'] ); ?>">
                            <span class="description"><?php _e('The separate item with commas text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Separate tags with commas", or null.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="choose_from_most_used"><?php _e('Choose From Most Used', 'content_types') ?></label>
                        </th>
                        <td>
                            <input type="text" name="labels[choose_from_most_used]" value="<?php echo ( $args['args']['labels']['choose_from_most_used'] ); ?>">
                            <span class="description"><?php _e('The choose from most used text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Choose from the most used tags" or null.', 'content_types'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="ct-wrap-right">
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Public', 'content_types') ?></h3>
                <table class="form-table publica">
                    <tr>
                        <th>
                            <label for="public"><?php _e('Public', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Should this taxonomy be exposed in the admin UI.', 'content_types'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="public" value="1" <?php if ( $args['args']['public'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="public" value="0" <?php if ( !$args['args']['public'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="public" value="advanced" <?php if ( $args['args']['public'] === null ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('ADVANCED', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Show UI', 'content_types') ?></h3>
                <table class="form-table show-ui">
                    <tr>
                        <th>
                            <label for="show_ui"><?php _e('Show UI', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Whether to generate a default UI for managing this taxonomy.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="show_ui" value="1" <?php if ( $args['args']['show_ui'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="show_ui" value="0" <?php if ( !$args['args']['show_ui'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Show Tagcloud', 'content_types') ?></h3>
                <table class="form-table show_tagcloud">
                    <tr>
                        <th>
                            <label for="show_tagcloud"><?php _e('Show Tagcloud', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Whether to show a tag cloud in the admin UI for this taxonomy.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="show_tagcloud" value="1" <?php if ( $args['args']['show_tagcloud'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="show_tagcloud" value="0" <?php if ( !$args['args']['show_tagcloud'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Show In Nav Menus ', 'content_types') ?></h3>
                <table class="form-table show-in-nav-menus">
                    <tr>
                        <th>
                            <label for="show_in_nav_menus"><?php _e('Show In Nav Menus', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Whether to make this taxonomy available for selection in navigation menus.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="show_in_nav_menus" value="1" <?php if ( $args['args']['show_in_nav_menus'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="show_in_nav_menus" value="0" <?php if ( !$args['args']['show_in_nav_menus'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Hierarchical', 'content_types') ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="hierarchical"><?php _e('Hierarchical', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Whether the post type is hierarchical. Allows Parent to be specified.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="hierarchical" value="1" <?php if ( $args['args']['hierarchical'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="hierarchical" value="0" <?php if ( !$args['args']['hierarchical'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Rewrite', 'content_types') ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="rewrite"><?php _e('Rewrite', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('Rewrite permalinks with this format.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="rewrite" value="1" <?php if ( $args['args']['rewrite'] == '1' ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="rewrite" value="0" <?php if ( $args['args']['rewrite'] == '0' ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="rewrite" value="advanced" <?php if ( isset( $args['args']['rewrite']['slug'] )) echo( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('CUSTOM SLUG', 'content_types'); ?></strong></span>
                            <br />
                            <input type="text" name="rewrite_slug" value="<?php echo( $args['args']['rewrite']['slug'] ); ?>" />
                            <br />
                            <span class="description"><?php _e('Prepend posts with this slug.', 'content_types'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="ct-table-wrap">
                <div class="ct-arrow"><br></div>
                <h3 class="ct-toggle"><?php _e('Query var', 'content_types') ?></h3>
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="query_var"><?php _e('Query var', 'content_types') ?></label>
                        </th>
                        <td>
                            <span class="description"><?php _e('False to prevent queries, or string to customize query var. Default will use $taxonomy as query var.', 'content_types'); ?></span>
                        </td>
                    </tr>
                   <tr>
                        <th></th>
                        <td>
                            <input type="radio" name="query_var" value="1" <?php if ( $args['args']['query_var'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                            <br />
                            <input type="radio" name="query_var" value="0" <?php if ( !$args['args']['query_var'] ) echo ( 'checked="checked"' ); ?>>
                            <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br style="clear: left" />
        <input type="submit" class="button-primary" name="ct_submit_update_taxonomy" value="Update Taxonomy">
        <br /><br /><br /><br />
    </form> <?php
} ?>