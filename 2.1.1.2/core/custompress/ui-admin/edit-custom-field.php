<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_types = get_post_types('','names');
$custom_field = $this->custom_fields[$_GET['ct_edit_custom_field']];
?>
<div class="wrap">
	<h3><?php _e('Edit Custom Field', $this->text_domain); ?></h3>
	<form action="#" method="post" class="ct-custom-fields">
		<?php wp_nonce_field( 'ct_submit_custom_field_verify', 'ct_submit_custom_field_secret' ); ?>
		<div class="ct-wrap-left">
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Title', $this->text_domain) ?></h3>
				<table class="form-table">
					<tr>
						<th>
							<label for="field_title"><?php _e('Field Title', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<input type="text" name="field_title" value="<?php echo ( $custom_field['field_title'] ); ?>" />
							<span class="description"><?php _e('The title of the custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="field_required"><?php _e('Required Field', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="checkbox" name="field_required" value="2" <?php echo ( isset( $custom_field['field_required'] ) ) ? 'checked' : ''; ?> >
							<span class="description"><?php _e('Make this a Required Field.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="field_title"><?php _e('Allow for WP/plugins', $this->text_domain) ?> <br /><span class="ct-required">(<?php _e("can't be changed", $this->text_domain) ?>)</span></label>
						</th>
						<td>
							<input type="hidden" name="field_wp_allow"  readonly="readonly" value="<?php echo ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] ) ? '2' : '0'; ?>" />
							<input type="checkbox" disabled value="1" <?php echo ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] ) ? 'checked' : ''; ?> />
							<span class="description"><?php _e('The WP and other plugins can use this custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Type', $this->text_domain) ?></h3>
				<table class="form-table">
					<tr>
						<th>
							<label for="field_type"><?php _e('Field Type', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<select name="field_type">
								<option value="text" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'text' ) echo ( 'selected="selected"' ); ?>>Text Box</option>
								<option value="textarea" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'textarea' ) echo ( 'selected="selected"' ); ?>>Multi-line Text Box</option>
								<option value="radio" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'radio' ) echo ( 'selected="selected"' ); ?>>Radio Buttons</option>
								<option value="checkbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'checkbox' ) echo ( 'selected="selected"' ); ?>>Checkboxes</option>
								<option value="selectbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'selectbox' ) echo ( 'selected="selected"' ); ?>>Drop Down Select Box</option>
								<option value="multiselectbox" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'multiselectbox' ) echo ( 'selected="selected"' ); ?>>Multi Select Box</option>
								<option value="datepicker" <?php if ( isset( $custom_field['field_type'] ) && $custom_field['field_type'] == 'datepicker' ) echo ( 'selected="selected"' ); ?>>Date Picker</option>
							</select>
							<span class="description"><?php _e('Select type of the custom field.', $this->text_domain); ?></span>
							<div class="ct-date-type-options">

								<?php

								$date_format = $custom_field['field_date_format'];
								$date_format = (empty($date_format)) ? $this->get_options('date_format') : $date_format;
								$date_format = (is_array($date_format)) ? 'mm/dd/yy' : $date_format;

								$this->jquery_ui_css(); //Load the current ui theme css

								?>
								<h4><?php _e('Fill in the options for this field', $this->text_domain); ?>:</h4>
								<p>
									<input type="text" id="field_date_format" name="field_date_format" size="38" value="<?php echo $date_format; ?>" onchange="jQuery('#datepicker').datepicker( 'option', 'dateFormat', this.value );"/><br />
									<span class="description"><?php _e('Select Date Format option or type your own', $this->text_domain) ?></span>
									<br />
									<input class="pickdate" id="datepicker" type="text" size="38" value="" /><br />
									<span class="description"><?php _e('Date picker sample', $this->text_domain) ?></span>
								</p>

							</div>
							<div class="ct-field-type-options">
								<h4><?php _e('Fill in the options for this field', $this->text_domain); ?>:</h4>
								<p>
									<?php _e('Order By', $this->text_domain); ?> :
									<select name="field_sort_order">
										<option value="default"><?php _e('Order Entered', $this->text_domain); ?></option>
										<?php /** @todo introduce the additional order options
										<option value="asc"><?php _e('Name - Ascending', $this->text_domain); ?></option>
										<option value="desc"><?php _e('Name - Descending', $this->text_domain); ?></option>
										*/ ?>
									</select>
								</p>

								<?php if ( isset( $custom_field['field_options'] ) && is_array( $custom_field['field_options'] )): ?>
								<?php foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
								<p>
									<?php _e('Option', $this->text_domain); ?> <?php echo( $key ); ?>:
									<input type="text" name="field_options[<?php echo( $key ); ?>]" value="<?php echo( $field_option ); ?>" />
									<input type="radio" value="<?php echo( $key ); ?>" name="field_default_option" <?php if ( $custom_field['field_default_option'] == $key ) echo ( 'checked="checked"' ); ?> />
									<?php _e('Default Value', $this->text_domain); ?>
									<?php if ( $key != 1 ): ?>
									<a href="#" class="ct-field-delete-option">[x]</a>
									<?php endif; ?>
								</p>
								<?php endforeach; ?>
								<?php else: ?>
								<p><?php _e('Option', $this->text_domain); ?> 1:
									<input type="text" name="field_options[1]" value="<?php if ( isset( $custom_field['field_options'][1] ) ) echo $custom_field['field_options'][1]; ?>" />
									<input type="radio" value="1" name="field_default_option" <?php if ( isset( $custom_field['field_default_option'] ) && $custom_field['field_default_option'] == '1' ) echo 'checked="checked"'; ?> />
									<?php _e('Default Value', $this->text_domain); ?>
								</p>
								<?php endif; ?>

								<div class="ct-field-additional-options"></div>
								<input type="hidden" value="<?php echo ( isset( $custom_field['field_options'] ) ) ? count( $custom_field['field_options'] ) : '1'; ?>" name="track_number" />
								<p><a href="#" class="ct-field-add-option"><?php _e('Add another option', $this->text_domain); ?></a></p>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Description', $this->text_domain) ?></h3>
				<table class="form-table">
					<tr>
						<th>
							<label for="field_description"><?php _e('Field Description', $this->text_domain) ?></label>
						</th>
						<td>
							<textarea name="field_description" cols="52" rows="3" ><?php echo( $custom_field['field_description'] ); ?></textarea>
							<span class="description"><?php _e('Description for the custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="ct-wrap-right">
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Post Type', $this->text_domain) ?></h3>
				<table class="form-table">
					<tr>
						<th>
							<label for="object_type"><?php _e('Post Type', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<select name="object_type[]" multiple="multiple" class="ct-object-type">
								<?php if ( !empty( $post_types ) ): ?>
								<?php foreach( $post_types as $post_type ): ?>
								<option value="<?php echo ( $post_type ); ?>" <?php foreach ( $custom_field['object_type'] as $key => $object_type ) { if ( $object_type == $post_type ) echo( 'selected="selected"' ); } ?>><?php echo ( $post_type ); ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<br />
							<span class="description"><?php _e('Select one or more post types to add this custom field to.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br style="clear: left" />
		<p class="submit">
			<?php wp_nonce_field( 'submit_custom_field' ); ?>
			<input type="submit" class="button-primary" name="submit" value="<?php _e('Update Custom Field', $this->text_domain); ?>" />
		</p>
		<br /><br /><br /><br />
	</form>

</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#datepicker').datepicker({ dateFormat : '<?php echo $date_format; ?>' });
		jQuery('#datepicker').attr('value', jQuery.datepicker.formatDate('<?php echo $date_format; ?>', new Date(), {}) );
	});
</script>

