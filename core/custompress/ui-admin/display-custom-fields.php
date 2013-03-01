<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
global $post;

//See if a filter is defined
if(is_string($fields) ) $field_ids = array_filter( array_map('trim',( array)explode(',', $fields ) ) );

$style = (empty($style) ) ? 'table' : $style;

$custom_fields = array();

if(empty($field_ids)){
	//	if ( $type == 'local' )
	//	$custom_fields = $this->custom_fields;
	//	elseif ( $type == 'network' )
	//	$custom_fields = $this->network_custom_fields;
	$custom_fields = $this->all_custom_fields;
} else {
	foreach($field_ids as $field_id){
		$cid = preg_replace('/^(_ct|ct)_/', '', $field_id);
		if(array_key_exists($cid, $this->all_custom_fields)) {
			$custom_fields[$cid] = $this->all_custom_fields[$cid];
		}
	}
}

if (empty($custom_fields)) $custom_fields = array();

$output = false;

?>

<div class="form-wrap">
	<div class="form-field form-required ct-form-field">
		<input type="hidden" id="ct_custom_fields_form" value="" />

		<?php
		switch ($style){
			case 'editfield': {
				if(is_array($custom_fields)) :
				foreach ( $custom_fields as $key => $custom_field ) :
				if( in_array($post->post_type, $custom_field['object_type'] ) ){
					$output = true;
				} else {
					unset($custom_fields[$key]); //Filter out unused ones for Validation rules later
				}

				$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

				//Field ID to use
				$fid = $prefix . $custom_field['field_id'];

				$custom_field['field_title'] .= (empty($custom_field['field_required'])) ? '' : '*'; //required field class

				if ( $output ):
				?>
				<div class="editfield">
					<label for="<?php echo $fid; ?>"><?php echo ( $custom_field['field_title'] ); ?></label>
					<?php echo do_shortcode('[ct_in id="' . $fid . '" ]'); ?>
					<p class="description"><?php echo do_shortcode('[ct_in id="' . $fid . '" property="description"]'); ?></p>
				</div>
				<?php
				endif; $output = false;
				endforeach;
				endif; //is_array($custom_fields)
				break;
			}
			case 'table':
			default: {
				?>
				<table class="form-table">

					<?php
					if(is_array($custom_fields)) :
					foreach ( $custom_fields as $key => $custom_field ) :
					if( in_array($post->post_type, $custom_field['object_type'] ) ){
						$output = true;
					} else {
						unset($custom_fields[$key]); //Filter out unused ones for Validation rules later
					}

					$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

					//Field ID to use
					$fid = $prefix . $custom_field['field_id'];

					$custom_field['field_title'] .= (empty($custom_field['field_required'])) ? '' : '*'; //required field class

					if ( $output ):
					?>
					<tr>
						<th>
							<label for="<?php echo $fid; ?>"><?php echo ( $custom_field['field_title'] ); ?></label>
						</th>
						<td>
							<?php
							echo do_shortcode('[ct_in id="' . $fid . '" ]');
							?>
							<br /><span class="description"><?php echo do_shortcode('[ct_in id="' . $fid . '" property="description"]'); ?></span>
						</td>
					</tr>
					<?php
					endif; $output = false;
					endforeach;
					endif; //is_array($custom_fields)
					?>
				</table>
				<?php

			}
			break;
		}
		?>
	</div>
	<script type="text/javascript">
		<?php echo $this->validation_rules( $this->get_custom_fields_set($post->post_type) ); ?>
		
	</script>
</div>
