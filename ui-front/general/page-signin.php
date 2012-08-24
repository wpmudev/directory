<?php

/**
* The template for displaying the Signin page.
* You can override this file in your active theme.
*
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/

$error = get_query_var('signin_error');

?>

<form action="#" method="post" class="checkout-login">
	<?php if ( $error ): ?>
	<span id="error"><?php echo $error; ?></span>
	<br />
	<?php endif;?>
			<div class="info" id="message">
			<p><?php _e( 'You have to login to view the contents of this page.', $this->text_domain ); ?></p>
		</div>
			<p>
				<strong><?php _e( 'Create new account', $this->text_domain ); ?></strong><br />
			<?php echo do_shortcode('[dr_signup_btn text="' . __('New Account', $this->text_domain) . '"]'); ?>
			</p>

			<p>
				<strong><?php _e( 'Existing Client', $this->text_domain ); ?></strong>
				</p>

	<table  <?php do_action( 'login_invalid' ); ?>>
		<tr>
			<td><label for="username"><?php _e( 'Username', $this->text_domain ); ?>:</label></td>
			<td><input type="text" id="username" name="user_login" value="<?php echo $_POST['user_login']; ?>" /></td>
		</tr>
		<tr>
			<td><label for="password"><?php _e( 'Password', $this->text_domain ); ?>:</label></td>
			<td><input type="password" id="password" name="user_pass" value="" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<label>
					<input type="checkbox" id="user_rem" name="user_rem" value="1" <?php echo $_POST['user_rem'] ? 'checked="checked"': '' ; ?> />
					<?php _e( 'Remember Me', $this->text_domain ); ?>
				</label>
			</td>
		</tr>
	</table>

	<div class="clear"></div>

	<div class="submit">
		<input type="submit" name="signin_submit" value="<?php _e( 'Sign in', $this->text_domain ); ?>" />
	</div>
</form>