<?php

/**
* The template for displaying the Add/edit listing page.
* You can override this file in your active theme.
*
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

global $Directory_Core;

$post_statuses = get_post_statuses();

//The Query
$query_args = array(
'post_type' => 'directory_listing',
'post_status' => array('publish','pending','draft'),
'posts_per_page' => 1000,
'author' => get_current_user_id(),
);

$custom_query = new WP_Query( $query_args);

//Display status message
if ( isset( $_GET['updated'] ) ) {
	?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
}

?>
<script type="text/javascript" src="<?php echo $this->plugin_url .'ui-front/js/ui-front.js'; ?>">
</script>

<form method="post" id="action-form" class="action-form" action="#">
	<?php wp_nonce_field( 'action_verify' ); ?>
	<input type="hidden" name="action" />
	<input type="hidden" name="post_id" />
</form>

<?php if ( $this->is_full_access() ): ?>
<div class="av-credits"><?php _e( 'You have access to create new ads', $this->text_domain ); ?></div>
<?php elseif($this->use_credits): ?>
<div class="av-credits"><?php _e( 'Available Credits:', $this->text_domain ); ?> <?php echo $this->transactions->credits; ?></div>
<?php endif; ?>

<div>
	<?php echo do_shortcode('[dr_add_listing_btn view="loggedin"]' . __( 'Create New Listing',  $this->text_domain ) .  '[/dr_add_listing_btn]'); ?>
	<?php echo do_shortcode('[dr_my_credits_btn text="'. __('My Credits', $this->text_domain) . '" view="loggedin"]'); ?>
</div>

<?php if ( count( $custom_query->posts ) ) : ?>
<table class="wp-list-table widefat fixed posts">
	<tr>
		<th><?php _e( 'Title', DR_TEXT_DOMAIN ); ?></th>
		<th><?php _e( 'Date', DR_TEXT_DOMAIN ); ?></th>
	</tr>

	<?php foreach ( $custom_query->posts as $listing ) :?>

	<tr>
		<td>
			<a href="<?php echo get_permalink( $listing->ID ); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', DR_TEXT_DOMAIN ), $listing->post_title ); ?>" rel="bookmark"><?php echo $listing->post_title; ?></a> - <?php echo $post_statuses[$listing->post_status]; ?>

			<div class="listing-rating">
				<?php do_action('sr_avg_ratings_of_listings', $listing->ID ); ?>

			</div>
			<div class="row-actions">
				<span class="edit">
					<a title="Edit this listing" href="javascript:;" onclick="dr_listings.edit( '<?php echo $listing->ID; ?>' );" ><?php _e( 'Edit', DR_TEXT_DOMAIN ); ?></a>
				</span>
				
				<?php if(current_user_can( 'delete_listings' )): ?>
				<span class="delete" id="delete-<?php echo $listing->ID; ?>"> |
					<a title="Delete this listing" href="javascript:;" onclick="dr_listings.toggle_delete( '<?php echo $listing->ID; ?>' );" ><?php _e( 'Delete', DR_TEXT_DOMAIN ); ?></a>
				</span>
				<?php endif; ?>

				<span class="delete" id="delete-confirm-<?php echo $listing->ID; ?>" style="display: none;"> |
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

	<?php endforeach; ?>
</table>

<?php else : ?>

<h3><?php _e( 'No Listings', DR_TEXT_DOMAIN ); ?></h3>

<?php endif;
