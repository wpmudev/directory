<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
if ( is_network_admin() )
$custom_fields = get_site_option('ct_custom_fields');
else
$custom_fields = $this->custom_fields;

//Nonce for reorder
$nonce = wp_create_nonce('reorder_custom_fields');

?>

<?php $this->render_admin('update-message'); ?>

<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_custom_field" value="<?php _e('Add Custom Field', $this->text_domain); ?>" />
</form>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Order', $this->text_domain); ?></th>
			<th><?php _e('Field Name', $this->text_domain); ?></th>
			<th><?php _e('Field ID', $this->text_domain); ?></th>
			<th><?php _e('WP/Plugins', $this->text_domain); ?></th>
			<th><?php _e('Field Type', $this->text_domain); ?></th>
			<th><?php _e('Description', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Order', $this->text_domain); ?></th>
			<th><?php _e('Field Name', $this->text_domain); ?></th>
			<th><?php _e('Field ID', $this->text_domain); ?></th>
			<th><?php _e('WP/Plugins', $this->text_domain); ?></th>
			<th><?php _e('Field Type', $this->text_domain); ?></th>
			<th><?php _e('Description', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
		</tr>
	</tfoot>
	<tbody>

		<?php if ( !empty( $custom_fields ) ): ?>
		<?php

		$last = count($custom_fields);
		$i = 0;
		foreach ( $custom_fields as $custom_field ): ?>

		<?php
		if ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] )
		$prefix = 'ct_';
		else
		$prefix = '_ct_';
		?>

		<?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
		<tr class="<?php echo ( $class ); ?>">
			<td>
				<?php if($i != 1): ?>
				<span class="ct-up"><a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . "&ct_content_type=custom_field&direction=up&_wpnonce=$nonce&ct_reorder_custom_field=" . $custom_field['field_id'] )); ?>"><img src="<?php echo $this->plugin_url . 'ui-admin/images/up.png'; ?>" /></a> </span>
				<?php endif; ?>
				<?php if($i != $last): ?>
				<span class="ct-down"><a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . "&ct_content_type=custom_field&direction=down&_wpnonce=$nonce&ct_reorder_custom_field=" . $custom_field['field_id'] )); ?>"><img src="<?php echo $this->plugin_url . 'ui-admin/images/down.png'; ?>" /></a></span>
				<?php endif; ?>
			</td>
			<td>
				<strong>
					<a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] )); ?>"><?php echo( $custom_field['field_title'] ); ?></a>
				</strong>
				<div class="row-actions" id="row-actions-<?php echo $custom_field['field_id']; ?>" >
					<span class="edit">
						<a title="<?php _e('Edit this custom field', $this->text_domain); ?>" href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] ) ); ?>"><?php _e( 'Edit', $this->text_domain ); ?></a> |
					</span>
					<span>
						<a title="<?php _e('Show embed code', $this->text_domain); ?>" href="#" onclick="javascript:content_types.toggle_embed_code('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e('Embed Code', $this->text_domain); ?></a> |
					</span>
					<span class="trash">
						<a class="submitdelete" href="#" onclick="javascript:content_types.toggle_delete('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e( 'Delete', $this->text_domain ); ?></a>
					</span>
				</div>
				<form action="#" method="post" id="form-<?php echo $custom_field['field_id']; ?>" class="del-form">
					<?php wp_nonce_field('delete_custom_field'); ?>
					<input type="hidden" name="custom_field_id" value="<?php echo $custom_field['field_id']; ?>" />
					<input type="submit" class="button confirm" value="<?php _e( 'Field and values', $this->text_domain ); ?>" name="delete_cf_values" />
					<input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onClick="content_types.cancel('<?php echo $custom_field['field_id']; ?>'); return false;" />
					<input type="submit" class="button confirm" value="<?php _e( 'Only field', $this->text_domain ); ?>" name="submit" />
				</form>
			</td>
			<td><?php echo $prefix . $custom_field['field_id']; ?></td>
			<td><?php echo ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] ) ? __( 'Allow', $this->text_domain ) : __( 'Deny', $this->text_domain ); ?></td>
			<td><?php echo( $custom_field['field_type'] ); ?></td>
			<td><?php echo( $custom_field['field_description'] ); ?></td>
			<td>
				<?php foreach( $custom_field['object_type'] as $object_type ): ?>
				<?php echo( $object_type ); ?>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr id="embed-code-<?php echo $custom_field['field_id']; ?>" class="embed-code <?php echo ( $class ); ?>">
			<td colspan="10">
				<div class="embed-code-wrap">
					<span class="description"><?php _e( 'Embed code returns the values of the custom field with the specified key from the specified post. Property may be one of "title", "description" or "value". If property is not used "value" wil be returned. Use inside the loop in templates and PHP code ', $this->text_domain ); ?></span>
					<br />
					<code><span style="color:red">&lt;?php</span> echo <strong>do_shortcode('[ct id="<?php echo( $prefix . $custom_field['field_id'] ); ?>" property="title | description | value"]')</strong>; <span style="color:red">?&gt;</span></code>
					<br /><br />
					<span class="description"><?php _e( 'Shortcode returns the values of the custom field with the specified key from the specified post. Property may be one of "title", "description" or "value". If property is not used "value" wil be returned. Use inside the loop in Posts and Widgets', $this->text_domain ); ?></span>
					<br />
					<code><strong>[ct id="<?php echo $prefix . $custom_field['field_id'] ; ?>" property="title | description | value"]</strong></code>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_custom_field" value="<?php _e('Add Custom Field', $this->text_domain); ?>" />
</form>
