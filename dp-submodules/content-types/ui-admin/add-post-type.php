<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<h3><?php _e('Add Post Type', 'content_types'); ?></h3>
<form action="" method="post" class="ct-post-type">
    <div class="ct-wrap-left">
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Post Type', 'content_types') ?></h3>
            <table class="form-table <?php do_action('ct_invalid_field_post_type'); ?>">
                <tr>
                    <th>
                        <label for="post_type"><?php _e('Post Type', 'content_types') ?> <span class="ct-required">( <?php _e('required', 'content_types'); ?> )</span></label>
                    </th>
                    <td>
                        <input type="text" name="post_type" value="<?php echo( $_POST['post_type'] ); ?>">
                        <span class="description"><?php _e('The new post type system name ( max. 20 characters ). Alphanumeric characters and underscores only. Min 2 letters. Once added the post type system name cannot be changed.', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Supports', 'content_types') ?></h3>
            <table class="form-table supports">
                <tr>
                    <th>
                        <label for="supports"><?php _e('Supports', 'content_types') ?></label>
                    </th>
                    <td>
                        <span class="description"><?php _e('An alias for calling add_post_type_support() directly.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="checkbox" name="supports[title]" value="title" <?php if ( $_POST['supports']['title'] == 'title') { echo( 'checked="checked"' ); } elseif ( $_POST['supports']['title'] === null && !isset( $_POST['ct_submit_add_post_type'] )) { echo( 'checked="checked"' ); } ?>>
                        <span class="description"><strong><?php _e('Title', 'content_types') ?></strong></span>
                        <br />
                        <input type="checkbox" name="supports[editor]" value="editor" <?php if ( $_POST['supports']['editor'] == 'editor') echo( 'checked="checked"' ); elseif ( $_POST['supports']['editor'] === null && !isset( $_POST['ct_submit_add_post_type'] )) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Editor', 'content_types') ?></strong> - <?php _e('Content', 'content_types') ?></span>
                        <br />
                        <input type="checkbox" name="supports[author]" value="author" <?php if ( $_POST['supports']['author'] == 'author') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Author', 'content_types') ?></strong></span>
                        <br />
                        <input type="checkbox" name="supports[thumbnail]" value="thumbnail" <?php if ( $_POST['supports']['thumbnail'] == 'thumbnail') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Thumbnail', 'content_types') ?></strong> - <?php _e('Featured Image - current theme must also support post-thumbnails.', 'content_types') ?></span>
                        <br />
                        <input type="checkbox" name="supports[excerpt]" value="excerpt" <?php if ( $_POST['supports']['excerpt'] == 'excerpt') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Excerpt', 'content_types') ?></strong></span>
                        <br />
                        <input type="checkbox" name="supports[trackbacks]" value="trackbacks" <?php if ( $_POST['supports']['trackbacks'] == 'trackbacks') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Trackbacks', 'content_types') ?></strong></span>
                        <br />
                        <input type="checkbox" name="supports[custom_fields]" value="custom_fields" <?php if ( $_POST['supports']['custom_fields'] == 'custom_fields') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Custom Fields', 'content_types') ?></strong></span>
                        <br />
                        <input type="checkbox" name="supports[comments]" value="comments" <?php if ( $_POST['supports']['comments'] == 'comments') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Comments', 'content_types') ?></strong> - <?php _e('Also will see comment count balloon on edit screen.', 'content_types') ?></span>
                        <br />
                        <input type="checkbox" name="supports[revisions]" value="revisions" <?php if ( $_POST['supports']['revisions'] == 'revisions') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Revisions', 'content_types') ?></strong> - <?php _e('Will store revisions.', 'content_types') ?></span>
                        <br />
                        <input type="checkbox" name="supports[page_attributes]" value="page-attributes" <?php if ( $_POST['supports']['page_attributes'] == 'page_attributes') echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('Page Attributes', 'content_types') ?></strong> - <?php _e('Template and menu order - Hierarchical must be true!', 'content_types') ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Capability Type', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="capability_type"><?php _e('Capability Type', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="capability_type" value="post">
                        <input type="checkbox" name="capability_type_edit" value="1" />
                        <span class="description ct-capability-type-edit"><strong><?php _e('EDIT' , 'content_types'); ?></strong> (<?php _e('advanced' , 'content_types'); ?>)</span>
                        <span class="description"><?php _e('The post type to use for checking read, edit, and delete capabilities. Default: "post".' , 'content_types'); ?></span>

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
                        <input type="text" name="labels[name]" value="<?php echo( $_POST['labels']['name'] ); ?>">
                        <span class="description"><?php _e('General name for the post type, usually plural.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="singular_name"><?php _e('Singular Name', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[singular_name]" value="<?php echo( $_POST['labels']['singular_name'] ); ?>">
                        <span class="description"><?php _e('Name for one object of this post type. Defaults to value of name.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="add_new"><?php _e('Add New', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[add_new]" value="<?php echo( $_POST['labels']['add_new'] ); ?>">
                        <span class="description"><?php _e('The add new text. The default is Add New for both hierarchical and non-hierarchical types.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="add_new_item"><?php _e('Add New Item', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[add_new_item]" value="<?php echo( $_POST['labels']['add_new_item'] ); ?>">
                        <span class="description"><?php _e('The add new item text. Default is Add New Post/Add New Page.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="edit_item"><?php _e('Edit Item', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[edit_item]" value="<?php echo( $_POST['labels']['edit_item'] ); ?>">
                        <span class="description"><?php _e('The edit item text. Default is Edit Post/Edit Page.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="new_item"><?php _e('New Item', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[new_item]" value="<?php echo( $_POST['labels']['new_item'] ); ?>">
                        <span class="description"><?php _e('The new item text. Default is New Post/New Page.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="view_item"><?php _e('View Item', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[view_item]" value="<?php echo( $_POST['labels']['view_item'] ); ?>">
                        <span class="description"><?php _e('The view item text. Default is View Post/View Page.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="search_items"><?php _e('Search Items', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[search_items]" value="<?php echo( $_POST['labels']['search_items'] ); ?>">
                        <span class="description"><?php _e('The search items text. Default is Search Posts/Search Pages.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="not_found"><?php _e('Not Found', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[not_found]" value="<?php echo( $_POST['labels']['not_found'] ); ?>">
                        <span class="description"><?php _e('The not found text. Default is No posts found/No pages found.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="not_found_in_trash"><?php _e('Not Found In Trash', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[not_found_in_trash]" value="<?php echo( $_POST['labels']['not_found_in_trash'] ); ?>">
                        <span class="description"><?php _e('The not found in trash text. Default is No posts found in Trash/No pages found in Trash.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="parent_item_colon"><?php _e('Parent Item Colon', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="labels[parent_item_colon]" value="<?php echo( $_POST['labels']['parent_item_colon'] ); ?>">
                        <span class="description"><?php _e('The parent text. This string isn\'t used on non-hierarchical types. In hierarchical ones the default is Parent Page', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Description', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="description"><?php _e('Description', 'content_types') ?></label>
                    </th>
                    <td>
                        <textarea class="ct-field-description" name="description" rows="3"><?php echo( $_POST['description'] ); ?></textarea>
                        <span class="description"><?php _e('A short descriptive summary of what the post type is.', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Menu Position', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="menu_position"><?php _e('Menu Position', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="menu_position" value="<?php if ( $_POST['menu_position'] ) echo( $_POST['menu_position'] ); elseif ( $_POST['menu_position'] === null ) echo( '50' ); ?>">
                        <span class="description"><?php _e('5 - below Posts; 10 - below Media; 20 - below Pages; 60 - below first separator; 100 - below second separator', 'content_types'); ?></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Menu Icon', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="menu_icon"><?php _e('Menu Icon', 'content_types') ?></label>
                    </th>
                    <td>
                        <input type="text" name="menu_icon" value="<?php echo( $_POST['menu_icon'] ); ?>">
                        <span class="description"><?php _e('The url to the icon to be used for this menu.', 'content_types'); ?></span>
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
                        <span class="description"><?php _e('Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search.', 'content_types'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="public" value="1"  <?php if ( $_POST['public'] === '1' ) echo( 'checked="checked"' ); elseif ( $_POST['public'] === null ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong><br />
                        <?php _e('Display a user-interface for this "post_type"', 'content_types');?><br /><code>( show_ui = TRUE )</code><br /><br />
                        <?php _e('Show "post_type" for selection in navigation menus', 'content_types'); ?><br /><code>( show_in_nav_menus = TRUE )</code><br /><br />
                        <?php _e('"post_type" queries can be performed from the front-end', 'content_types'); ?><br /><code>( publicly_queryable = TRUE )</code><br /><br />
                        <?php _e('Exclude posts with this post type from search results', 'content_types'); ?><br /> <code>( exclude_from_search = FALSE )</code></span>
                        <br /><br />
                        <input type="radio" name="public" value="0" <?php if ( $_POST['public'] === '0' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong><br />
                        <?php _e('Don not display a user-interface for this "post_type"', 'content_types');?><br /><code>( show_ui = FALSE )</code><br /><br />
                        <?php _e('Hide "post_type" for selection in navigation menus', 'content_types'); ?><br /><code>( show_in_nav_menus = FALSE )</code><br /><br />
                        <?php _e('"post_type" queries cannot be performed from the front-end', 'content_types'); ?><br /><code>( publicly_queryable = FALSE )</code><br /><br />
                        <?php _e('Exclude posts with this post type from search results', 'content_types'); ?><br /> <code>( exclude_from_search = TRUE )</code></span>
                        <br /><br />
                        <input type="radio" name="public" value="advanced" <?php if ( $_POST['public'] == 'advanced' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('ADVANCED', 'content_types'); ?></strong> - <?php _e('You can set each component manualy.', 'content_types'); ?></span>
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
                        <span class="description"><?php _e('Whether to generate a default UI for managing this post type. Note that built-in post types, such as post and page, are intentionally set to false.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="show_ui" value="1" <?php if ( $_POST['public'] == 'advanced' && $_POST['show_ui'] === '1' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong> - <?php _e('Display a user-interface (admin panel) for this post type.', 'content_types'); ?></span>
                        <br />
                        <input type="radio" name="show_ui" value="0" <?php if ( $_POST['public'] == 'advanced' && $_POST['show_ui'] === '0' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong> - <?php _e('Do not display a user-interface for this post type.', 'content_types'); ?></span>
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
                        <span class="description"><?php _e('Whether post_type is available for selection in navigation menus.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="show_in_nav_menus" value="1" <?php if ( $_POST['public'] == 'advanced' && $_POST['show_in_nav_menus'] === '1' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="show_in_nav_menus" value="0" <?php if ( $_POST['public'] == 'advanced' && $_POST['show_in_nav_menus'] === '0' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Publicly Queryable', 'content_types') ?></h3>
            <table class="form-table public-queryable">
                <tr>
                    <th>
                        <label for="publicly_queryable"><?php _e('Publicly Queryable', 'content_types' ) ?></label>
                    </th>
                    <td>
                        <span class="description"><?php _e('Whether post_type queries can be performed from the front end.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="publicly_queryable" value="1" <?php if ( $_POST['public'] == 'advanced' && $_POST['publicly_queryable'] === '1' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="publicly_queryable" value="0" <?php if ( $_POST['public'] == 'advanced' && $_POST['publicly_queryable'] === '0' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Exclude From Search', 'content_types') ?></h3>
            <table class="form-table exclude-from-search">
                <tr>
                    <th>
                        <label for="exclude_from_search"><?php _e('Exclude From Search', 'content_types') ?></label>
                    </th>
                    <td>
                        <span class="description"><?php _e('Whether to exclude posts with this post type from search results.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="exclude_from_search" value="1" <?php if ( $_POST['public'] == 'advanced' && $_POST['exclude_from_search'] === '1' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="exclude_from_search" value="0" <?php if ( $_POST['public'] == 'advanced' && $_POST['exclude_from_search'] === '0' ) echo( 'checked="checked"' ); ?>>
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
                        <input type="radio" name="hierarchical" value="1" <?php if ( $_POST['hierarchical'] === '1' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="hierarchical" value="0" checked="checked" <?php if ( $_POST['hierarchical'] === '0' ) echo( 'checked="checked"' ); elseif ( $_POST['hierarchical'] === null ) echo( 'checked="checked"' ); ?>>
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
                        <span class="description"><?php _e('Rewrite permalinks with this format. If TRUE, the post type system name will be used for the rewrite slug. ', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="rewrite" value="1" <?php if ( $_POST['rewrite'] === '1' ) echo( 'checked="checked"' ); elseif ( $_POST['rewrite'] === null ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="rewrite" value="0" <?php if ( $_POST['rewrite'] === '0' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="rewrite" value="advanced" <?php if ( $_POST['rewrite'] === 'advanced' ) echo( 'checked="checked"' ); ?>>
                        <span class="description"><strong><?php _e('CUSTOM SLUG', 'content_types'); ?></strong></span>
                        <br />
                        <input type="text" name="rewrite_slug" value="<?php echo( $_POST['rewrite_slug'] ); ?>" />
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
                        <span class="description"><?php _e('Can queries be performed on this post_type.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="query_var" value="1" checked="checked">
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="query_var" value="0">
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="ct-table-wrap">
            <div class="ct-arrow"><br></div>
            <h3 class="ct-toggle"><?php _e('Can Export', 'content_types') ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="hierarchical"><?php _e('Can Export', 'content_types') ?></label>
                    </th>
                    <td>
                        <span class="description"><?php _e('Can this post_type be exported.', 'content_types'); ?></span>
                    </td>
                </tr>
               <tr>
                    <th></th>
                    <td>
                        <input type="radio" name="can_export" value="1" checked="checked">
                        <span class="description"><strong><?php _e('TRUE', 'content_types'); ?></strong></span>
                        <br />
                        <input type="radio" name="can_export" value="0">
                        <span class="description"><strong><?php _e('FALSE', 'content_types'); ?></strong></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <p class="submit">
        <?php wp_nonce_field('submit_post_type'); ?>
        <input type="submit" class="button-primary" name="submit" value="Add Post Type" />
    </p>
    <br /><br /><br /><br />
</form>