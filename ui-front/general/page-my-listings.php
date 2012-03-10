<?php

/**
* The template for displaying the Add/edit listing page.
* You can override this file in your active theme.
*
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

?>
    <?php

    //The Query
    $custom_query = new WP_Query( 'post_type=directory_listing&post_status=publish,draft&posts_per_page=1000&author=' . get_current_user_id() );

    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

    ?>

        <script type="text/javascript">
        //<![CDATA[
        var dr_listings = {
            add: function() {
                jQuery( '#action-form' ).attr( 'action', '<?php echo site_url() ?>/add-listing' );
                jQuery( '#action-form input[name="action"]' ).val( 'add_listing' );
                jQuery( '#action-form' ).submit();
            },
            edit: function( key ) {
                jQuery( '#action-form' ).attr( 'action', '<?php echo site_url() ?>/edit-listing' );
                jQuery( '#action-form input[name="action"]' ).val( 'edit_listing' );
                jQuery( '#action-form input[name="post_id"]' ).val( key );
                jQuery( '#action-form' ).submit();
            },
            toggle_delete: function( key ) {
                jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).hide();
                jQuery( '#delete-confirm-' + key ).show();
            },
            toggle_delete_yes: function( key ) {
                jQuery( '#action-form input[name="action"]' ).val( 'delete_listing' );
                jQuery( '#action-form input[name="post_id"]' ).val( key );
                jQuery( '#action-form' ).submit();

            },
            toggle_delete_no: function( key ) {
                jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).show();
                jQuery( '#delete-confirm-' + key ).hide();

            }
        };
        //]]>
        </script>

        <form method="post" id="action-form" class="action-form">
            <?php wp_nonce_field( 'action_verify' ); ?>
            <input type="hidden" name="action" />
            <input type="hidden" name="post_id" />
        </form>

        <span class="add"><a title="Add new listing" href="javascript:;" onclick="dr_listings.add();" ><?php _e( 'Add new listing', DR_TEXT_DOMAIN ); ?></a></span>

        <?php if ( count( $custom_query->posts ) ) : ?>
        <table class="wp-list-table widefat fixed posts">
            <tr>
                <th><?php _e( 'Title', DR_TEXT_DOMAIN ); ?></th>
                <th><?php _e( 'Date', DR_TEXT_DOMAIN ); ?></th>
            </tr>
            <?php foreach ( $custom_query->posts as $listing ) { ?>
            <tr>
                <td>
                    <a href="<?php echo get_permalink( $listing->ID ); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), $listing->post_title ); ?>" rel="bookmark"><?php echo $listing->post_title; ?></a> - <?php echo $listing->post_status; ?>
                    <div class="listing-rating">
                        <?php do_action('sr_avg_ratings_of_listings', $listing->ID ); ?>
                    </div>
                    <div class="row-actions">
                        <span class="edit">
                            <a title="Edit this listing" href="javascript:;" onclick="dr_listings.edit( '<?php echo $listing->ID; ?>' );" ><?php _e( 'Edit', DR_TEXT_DOMAIN ); ?></a>
                         | </span>
                         <span class="delete" id="delete-<?php echo $listing->ID; ?>">
                            <a title="Delite this listing" href="javascript:;" onclick="dr_listings.toggle_delete( '<?php echo $listing->ID; ?>' );" ><?php _e( 'Delete', DR_TEXT_DOMAIN ); ?></a>
                         | </span>
                         <span class="delete" id="delete-confirm-<?php echo $listing->ID; ?>" style="display: none;">
                         <?php _e( 'Delete? ', DR_TEXT_DOMAIN ); ?>
                            <a title="no" href="javascript:;" onclick="dr_listings.toggle_delete_no( '<?php echo $listing->ID; ?>' );"><?php _e( 'No', DR_TEXT_DOMAIN ); ?></a>
                            |
                            <a title="yes" href="javascript:;" onclick="dr_listings.toggle_delete_yes( '<?php echo $listing->ID; ?>' );"><?php _e( 'Yes', DR_TEXT_DOMAIN ); ?></a>
                        </span>
                        <span class="view">
                            <a rel="permalink" title="Preview" href="<?php echo get_permalink( $listing->ID ); ?>"><?php _e( 'Preview', DR_TEXT_DOMAIN ); ?></a>
                        </span>
                    </div>
                </td>
                <td><?php echo $listing->post_modified ?></td>
            </tr>
            <?php } ?>
         </table>


        <?php else : ?>

            <h3><?php _e( 'No Listings', DR_TEXT_DOMAIN ); ?></h3>

        <?php endif; ?>