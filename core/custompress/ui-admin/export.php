<?php if (!defined('ABSPATH')) die('No direct access allowed!');

$import = '';

if(defined( 'CT_ALLOW_IMPORT' )
&& ! empty($_POST['ct_import'])
&& check_admin_referer('import') ) {
	$import = stripslashes($_POST['ct_import']);
	eval($import);
}

if ( is_network_admin() ) {
	$post_types = get_site_option('ct_custom_post_types');
	$taxonomies = get_site_option('ct_custom_taxonomies');
	$custom_fields = get_site_option('ct_custom_fields');
} else {
	$post_types = get_option('ct_custom_post_types');
	$taxonomies = get_option('ct_custom_taxonomies');
	$custom_fields = get_option('ct_custom_fields');
}

$post_types = (empty($post_types))? array() : $post_types;
$taxonomies = (empty($taxonomies))? array() : $taxonomies;
$custom_fields = (empty($custom_fields))? array() : $custom_fields;

?>

<div class="wrap">

	<h2><?php _e('CustomPress Custom Types Export', $this->text_domain); ?></h2>
	<form action="#" method="post">

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br /></div>
			<h3 class="ct-toggle"><span><?php _e('Post Types Export', $this->text_domain); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<?php _e('Check the Post Types you wish to export.', $this->text_domain); ?>
						</th>
						<td>
							<?php foreach($post_types as $key => $post_type): ?>
							<label class="ct-list"><input type="checkbox" name="pt[<?php echo $key?>]" value="1" <?php checked(! empty($_POST['pt'][$key]) ); ?> />&nbsp;<?php echo $key?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<textarea id="post_export" rows="6" cols="80" ><?php esc_html_e(post_types_export($post_types)); ?></textarea>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<input type="submit" class="button" value="<?php _e('Create Post Type Export', $this->text_domain); ?>" />
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br /></div>
			<h3 class="ct-toggle"><span><?php _e('Taxonomies Export', $this->text_domain); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<?php _e('Check the Taxonomies you wish to export.', $this->text_domain); ?>
						</th>
						<td>
							<?php foreach($taxonomies as $key => $taxonomy): ?>
							<label class="ct-list"><input type="checkbox" name="tx[<?php echo $key?>]" value="1" <?php checked(! empty($_POST['tx'][$key]) ); ?> />&nbsp;<?php echo $key?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<textarea id="taxonomies_export" rows="6" cols="80" ><?php esc_html_e(taxonomies_export($taxonomies)); ?></textarea>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<input type="submit" class="button" value="<?php _e('Create Taxonomies Export', $this->text_domain); ?>" />
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="ct-table-wrap">
			<div class="ct-arrow"><br /></div>
			<h3 class="ct-toggle"><span><?php _e('Custom Fields Export', $this->text_domain); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<?php _e('Check the Custom Fields you wish to export.', $this->text_domain); ?>
						</th>
						<td>
							<?php foreach($custom_fields as $key => $custom_field): ?>
							<label class="ct-list-cf"><input type="checkbox" name="cf[<?php echo $key?>]" value="1" <?php checked(! empty($_POST['cf'][$key]) ); ?> />&nbsp;<?php echo $custom_field['field_title'] . ' : ' . $key?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<textarea id="field_export" rows="6" cols="80" ><?php esc_html_e(custom_fields_export($custom_fields)); ?></textarea>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<?php wp_nonce_field('export'); ?>
							<input type="submit" class="button" value="<?php _e('Create Custom Fields Export', $this->text_domain); ?>" />
						</td>
					</tr>
				</table>
			</div>
		</div>

	</form>


	<form action="#" method="post">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br /></div>
			<h3 class="ct-toggle"><span><?php _e('Custom Types Import', $this->text_domain); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<?php _e('Paste your Export code here and press Import to add the custom type to CustomPress', $this->text_domain); ?>
						</th>
						<td>
							<?php wp_nonce_field('import'); ?>
							<?php if( defined('CT_ALLOW_IMPORT') ): ?>
							<textarea id="ct_import" name="ct_import" rows="6" cols="80" ><?php esc_html_e($import); ?></textarea>
							<?php else: ?>
							<span class="description"><?php _e("Import is currently disabled on this site. To enable add the line<br /><code>define('CT_ALLOW_IMPORT', true);</code><br />to the wp-config.php file.", $this->text_domain); ?></span>
							<span class="description"><?php _e("Remove the line when it is no longer needed to prevent possible security problems.", $this->text_domain); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td>
							<?php if( defined('CT_ALLOW_IMPORT') ): ?>
							<input type="submit" class="button" value="<?php _e('Import', $this->text_domain); ?>" />
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</div>

<?php

function post_types_export($post_types = array() ){

	if(empty($_POST['pt']) ) return '';

	$output = "// Post Types Export code for CustomPress\n";
	$output .= 'global $CustomPress_Core;';
	$output .= "\n" . '$CustomPress_Core->import=';

	$export = array();

	foreach($_POST['pt'] as $key => $value){

		$export['post_types'][$key] = $post_types[$key];

	}

	$output .= var_export($export, true) . ";" ;

	return $output;

}

function taxonomies_export($taxonomies = array() ){

	if(empty($_POST['tx']) ) return '';

	$output = "// Taxonomies Export code for CustomPress\n";
	$output .= 'global $CustomPress_Core;';
	$output .= "\n" . '$CustomPress_Core->import=';

	$export = array();

	foreach($_POST['tx'] as $key => $value){

		$export['taxonomies'][$key] = $taxonomies[$key];

	}

	$output .= var_export($export, true) . ";" ;

	return $output;

}

function custom_fields_export($custom_fields = array() ){

	if(empty($_POST['cf']) ) return '';

	$output = "// Custom Fields Export code for CustomPress\n";
	$output .= 'global $CustomPress_Core;';
	$output .= "\n" . '$CustomPress_Core->import=';

	$export = array();

	foreach($_POST['cf'] as $key => $value){

		$export['custom_fields'][$key] = $custom_fields[$key];

	}

	$output .= var_export($export, true) . ";" ;

	return $output;

}
