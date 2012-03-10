<?php

/**
* The template for displaying the Add/edit listing page.
* You can override this file in your active theme.
*
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

global $wp_query;

$listing_data   = '';
$selected_cats  = '';

if ( 'edit-listing' == $wp_query->query_vars['pagename'] ) {
    if ( !$this->user_can_edit_listing( $_POST['post_id'] ) ) {
        wp_redirect( site_url() . '/my-listings' );
        exit;
    }

   $listing_data = get_post(  $_POST['post_id'], ARRAY_A );

}

if ( isset( $_POST['listing_data'] ) )
    $listing_data = $_POST['listing_data'];


if ( isset( $_POST['tax_input']['listing_category'] ) )
    $selected_cats = $_POST['tax_input']['listing_category'];

?>


<div class="profile">

    <form class="standard-form base" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="listing_data[ID]" value="<?php echo ( isset( $listing_data['ID'] ) ) ? $listing_data['ID'] : ''; ?>" />

        <div class="editfield">
            <label for="title"><?php _e( 'Title', $this->text_domain ); ?></label>
            <input type="text" id="title" name="listing_data[post_title]" value="<?php echo ( isset( $listing_data['post_title'] ) ) ? $listing_data['post_title'] : ''; ?>" />
            <p class="description"><?php _e( 'Enter title here.', $this->text_domain ); ?></p>
        </div>

        <div class="editfield alt">
            <label for="listing_content"><?php _e( 'Content', $this->text_domain ); ?></label>
            <textarea id="listing_content" name="listing_data[post_content]" cols="40" rows="5"><?php echo ( isset( $listing_data['post_content'] ) ) ? $listing_data['post_content'] : ''; ?></textarea>
            <p class="description"><?php _e( 'The content of your listing.', $this->text_domain ); ?></p>
        </div>


        <?php
            require_once(ABSPATH . 'wp-admin/includes/template.php');

            $defaults = array('taxonomy' => 'listing_category');
            extract( $defaults, EXTR_SKIP );
            $tax = get_taxonomy( $taxonomy );

        ?>

        <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
            <label for="title"><?php echo $tax->labels->all_items; ?></label>

            <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
                <?php
                $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
                echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
                ?>
                <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
                    <?php wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy, 'selected_cats' => $selected_cats, 'checked_ontop' => false ) ) ?>
                </ul>
            </div>

        </div>


        <div class="editfield">
                <label for="title"><?php _e( 'Status', $this->text_domain ); ?></label>
                <div id="status-box">
                    <select name="listing_data[post_status]" id="listing_data[post_status]">
                        <option value="publish" <?php echo ( isset( $listing_data['post_status'] ) && 'publish' == $listing_data['post_status'] ) ? 'checked' : ''; ?>><?php _e( 'Published', $this->text_domain ); ?></option>
                        <option value="draft" <?php echo ( isset( $listing_data['post_status'] ) && 'draft' == $listing_data['post_status'] ) ? 'checked' : ''; ?>><?php _e( 'Draft', $this->text_domain ); ?></option>
                    </select>
                </div>
            <p class="description"><?php _e( 'Check a status for your Listing.', $this->text_domain ); ?></p>
        </div>


    <?php if ( class_exists( 'CustomPress_Content_Types' ) ) : ?>
        <div class="editfield">
            <?php
            global $post, $CustomPress_Content_Types;
            $post->post_type    = 'directory_listing';
            $post->ID           = $listing_data['ID'];
            $CustomPress_Content_Types->render_admin( 'display-custom-fields', array( 'type' => 'local' ) );
            ?>
        </div>
    <?endif; ?>


        <div class="submit">
            <?php wp_nonce_field( 'verify' ); ?>
            <input type="submit" value="<?php _e( 'Save Changes', $this->text_domain ); ?>" name="update_listing">
            <input type="submit" value="<?php _e( 'Cancel', $this->text_domain ); ?>" name="cancel_listing">
        </div>

    </form>
</div>