<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
global $wp_roles;
$options = $this->get_options('general_settings');
?>

<div class="wrap">
	<?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'general', 'sub' => 'general' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<form action="" method="post" class="dr-general">

		<table class="form-table">
			<tr>
				<th>
					<label for="roles"><?php _e( 'Assign Capabilities', $this->text_domain ) ?></label>
					<img id="ajax-loader" src="<?php echo $this->plugin_url . 'ui-admin/images/ajax-loader.gif'; ?>" />
				</th>
				<td>
					<select id="roles" name="roles">
						<?php foreach ( $wp_roles->role_names as $role => $name ): ?>
							<option value="<?php echo $role; ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="description"><?php _e('Select a role to which you want to assign Directory capabilities.', $this->text_domain); ?></span>

					<br /><br />

					<div id="capabilities">
						<?php foreach ( $this->capability_map as $capability => $description ): ?>
							<input type="checkbox" name="capabilities[<?php echo $capability; ?>]" value="1" />
							<span class="description"><?php echo $description; ?></span>
							<br />
						<?php endforeach; ?>
					</div>
				</td>
			</tr>
		</table>
<!--
		<table class="form-table">
			<tr>
				<th>
					<label for="moderation"><?php _e('Moderation', $this->text_domain ) ?></label>
				</th>
				<td>
					<input type="checkbox" id="moderation" name="moderation" value="1"<?php checked( $options['moderation'] ) ?>  />
					<span class="description"><?php _e('Answers are held for moderation.', $this->text_domain ); ?></span>
				</td>
			</tr>
		</table>
-->
		<br /><br />
		<p class="submit">
			<?php wp_nonce_field('dir-verify'); ?>
			<input type="hidden" name="action" value="dr-save" />
			<input type="hidden" name="key" value="general_settings" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

</div>
