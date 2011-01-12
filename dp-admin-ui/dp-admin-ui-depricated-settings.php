<?php

/**
 * dp_admin_ui_settings()
 *
 * Outputs "DirectoryPress Settings" admin page.
 *
 * @param array/false $post_types All available post types which
 * can be associated with the taxonomy that is about to be added.
 */
function dp_admin_ui_settings( $post_types ) { ?>

    <div class="wrap dp-wrap">
        <div class="icon32" id="icon-options-general"><br></div>
        <h2><?php _e('DirectoryPress Settings', 'directorypress'); ?></h2>
        <?php $settings = get_site_option('dp_main_settings'); ?>
        <form action="" method="post" class="dp-main">
            <?php wp_nonce_field( 'dp_submit_settings_verify', 'dp_submit_settings_secret' ); ?>
            <?php /** @todo
            <div class="updated below-h2" id="message">
                <p><a href=""></a></p>
            </div> */ ?>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="post_type"><?php _e('Display post types on "Home": ', 'directorypress') ?></label>
                    </th>
                    <td>
                        <?php /** @todo Resolve bug with query_posts resetings the is_home() function.
                        <span>Page: </span>
                        <select name="page" id="dp-select-page">
                           <option value="home" selected="selected">home</option>
                        </select>
                        <span class="description"><?php _e('Select page on which you want to display custom post types. You can define custom post types for more than one page.', 'directorypress'); ?></span>
                        <br /><br />
                        */ ?>
                        <input type="checkbox" name="post_type[]" value="post" />
                        <span class="description"><strong>post</strong></span>
                        <br />
                        <input type="checkbox" name="post_type[]" value="page" />
                        <span class="description"><strong>page</strong></span>
                        <br />
                        <input type="checkbox" name="post_type[]" value="attachment" />
                        <span class="description"><strong>attachment</strong></span>
                        <br />
                        <?php if ( !empty( $post_types )): ?>
                            <?php foreach ( $post_types as $post_type => $args ): ?>
                            <input type="checkbox" name="post_type[]" value="<?php echo( $post_type ); ?>" />
                            <span class="description"><strong><?php echo( $post_type ); ?></strong></span>
                            <br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <br />
                        <span class="description"><?php _e('Check the custom post types you want to display on the "Home" page.', 'directorypress'); ?></span>
                        <br /><br />
                        <input type="checkbox" name="post_type[]" value="default" />
                        <span class="description"><strong>default</strong></span><br /><br />
                        <span class="description"><?php _e('If "default" is checked the "Home" page will display the default post types.', 'directorypress'); ?></span>
                    </td>
                </tr>
            </table>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="post_type"><?php _e('Create theme file for: ', 'directorypress') ?></label>
                    </th>
                    <td>
                        <?php if ( !empty( $post_types )): ?>
                            <?php foreach ( $post_types as $post_type => $args ): ?>
                            <input type="checkbox" name="post_type_file[]" value="<?php echo( $post_type ); ?>" <?php if ( file_exists( TEMPLATEPATH . '/single-' .  $post_type . '.php' )) echo( 'checked="checked" disabled="disabled"' ); ?> />
                            <span class="description"><strong><?php echo( $post_type ); ?></strong></span>
                            <br />
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="description"><strong><?php _e('No custom post types available', 'directorypress'); ?></strong></span>
                        <?php endif; ?>
                        <br />
                        <span class="description"><?php _e('This will create "single-[post_type].php" file inside your theme. This file will be the custom template for your custom post type. You can then edit the file and customize it however you like.', 'directorypress'); ?></span><br />
                        <span class="description"><?php _e('In some cases you may not want to do that. For example if you don\'t have a template for your custom post type the default "single.php" will be used.', 'directorypress'); ?></span><br />
                        <span class="description"><?php _e('Your active theme folder permissions have to be set to 777 for this option to work. After the file is created you can set your active theme folder permission back to 755.', 'directorypress'); ?></span>
                    </td>
                </tr>
            </table>
            <input type="submit" class="button-primary" name="dp_submit_settings" value="Save Changes">
        </form>  
    </div> <?php
} ?>