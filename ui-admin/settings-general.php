<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$options = $this->get_options('general_settings');
?>

<div class="wrap">
	<?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'general', 'sub' => 'general' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<form action="" method="post">

		<table class="form-table">
            <tr>
                <th>
                    <label for="signin_url"><?php _e( 'Redirect URL (sign in):', $this->text_domain ) ?></label>
                </th>
                <td>
                    <input type="text" name="signin_url" value="<?php echo $options['signin_url'] ? $options['signin_url'] : ''; ?>" size="50" />
                    <span class="description"><?php _e( '(by default to Homepage)', $this->text_domain ) ?></span>
                </td>
            </tr>
			<tr>
                <th>
                    <label for="logout_url"><?php _e( 'Redirect URL (loguot):', $this->text_domain ) ?></label>
                </th>
				<td>
					<input type="text" name="logout_url" value="<?php echo $options['logout_url'] ? $options['logout_url'] : ''; ?>" size="50" />
                    <span class="description"><?php _e( '(by default to Homepage)', $this->text_domain ) ?></span>
				</td>
			</tr>
		</table>
		<br /><br />
		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="general_settings" />
			<input type="submit" class="button-primary" name="save" value="Save Changes">
		</p>

	</form>

</div>
