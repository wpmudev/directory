<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$send_to = ( empty($_POST['manage_credits'])) ? '' : $_POST['manage_credits'];
$send_to_user = ( empty($_POST['manage_credits_user'])) ? '' : $_POST['manage_credits_user'];
$send_to_count = ( empty($_POST['manage_credits_count'])) ? '' : $_POST['manage_credits_count'];
?>

<div class="wrap">
	<?php screen_icon('options-general'); ?>

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_credits','tab' => 'send-credits' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>
	<h1><?php _e( 'Send Directory Credits', $this->text_domain ); ?></h1>

	<form action="#" method="post">
		<table class="form-table">
			<tr>
				<th><label for="manage_credits"><?php _e( 'Send Credits To Directory members', $this->text_domain ); ?></label></th>
				<td>
					<label><input type="radio" id="manage_credits" name="manage_credits" value="send_single"
						<?php if ( $send_to == 'send_single' ) echo 'checked="checked"';  ?>
					onchange="if(this.checked) jQuery('#username').css('display',''); else jQuery('#username').css('display','none');" />Send Credits to a single Directory Member</label>
					<br />
					<label><input type="radio" name="manage_credits" value="send_all"
						<?php if ( $send_to == 'send_all' ) echo 'checked="checked"';  ?>
					onchange="if(this.checked) jQuery('#username').css('display','none'); else jQuery('#username').css('display','');" />Send Credits to all Directory Members</label>
				</td>
			</tr>
			<tr>
				<th><label for="manage_credits_count"><?php _e( 'Number of credits to send', $this->text_domain ); ?></label></th>
				<td>
					<input type="text" id="manage_credits_count" name="manage_credits_count" value="<?php echo $send_to_count; ?>" />
				</td>
			</tr>
			<tr id="username" <?php if ( $send_to != 'send_single' ) echo 'style="display:none;"'; ?>>
				<th><label for="manage_credits_user"><?php _e( 'Username to send credits to', $this->text_domain ); ?></label></th>
				<td>
					<input type="text" id="manage_credits_user" name="manage_credits_user" value="<?php echo $send_to_user; ?>" />
				</td>
			</tr>
		</table>
		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="send-credits" />
			<input type="submit" class="button-primary" name="save" value="<?php _e( 'Send Credits', $this->text_domain ); ?>" />
		</p>
	</form>
</div>