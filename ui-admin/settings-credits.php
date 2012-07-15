<?php if (!defined('ABSPATH')) die('No direct access allowed!'); 

$options = $this->get_options('credits'); 
$options = (empty($options) ) ? array() : $options;

?>

<div class="wrap">
	<?php screen_icon('options-general'); ?>

	<?php $this->render_admin( 'navigation', array( 'page' => 'classifieds_settings','tab' => 'payments' ) ); ?>

	<?php $this->render_admin( 'message' ); ?>

	<form action="#" method="post">
		<table class="form-table">
			<tr>
				<th><label for="enable_credits"><?php _e( 'Enable Credits', $this->text_domain ); ?></label></th>
				<td>
					<input type="checkbox" id="enable_credits" name="enable_credits" value="1" <?php if ( isset( $options['enable_credits'] ) ) echo 'checked="checked"';  ?> />
					<span class="description"><?php _e( 'Enable credits for publishing an ad.', $this->text_domain ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="cost_credit"><?php _e( 'Cost Per Credit', $this->text_domain ); ?></label></th>
				<td>
					<input type="text" id="cost_credit" name="cost_credit" value="<?php if ( isset( $options['cost_credit'] ) ) echo $options['cost_credit']; ?>" class="small-text" />
					<span class="description"><?php _e( 'How much a credit should cost.', $this->text_domain ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="credits_per_week"><?php _e( 'Credits Per Week', $this->text_domain ); ?></label></th>
				<td>
					<input type="text" id="credits_per_week" name="credits_per_week" value="<?php if ( isset( $options['credits_per_week'] ) ) echo $options['credits_per_week']; ?>" class="small-text" />
					<span class="description"><?php _e( 'How much credits should you need to publish and ad for one week.', $this->text_domain ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="signup_credits"><?php _e( 'Signup Credits', $this->text_domain ); ?></label></th>
				<td>
					<input type="text" id="signup_credits" name="signup_credits" value="<?php if ( isset( $options['signup_credits'] ) ) echo $options['signup_credits']; ?>" class="small-text" />
					<span class="description"><?php _e( 'How much credits each user should recieve on signup.', $this->text_domain ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="description"><?php _e( 'Description', $this->text_domain ); ?></label></th>
				<td>
					<textarea id="description" name="description" rows="1" cols="55"><?php if ( isset( $options['description'] ) ) echo $options['description']; ?></textarea>
					<br />
					<span class="description"><?php _e( 'Description of the costs and durations associated with publishing an ad. Will be displayed in the admin area.', $this->text_domain ); ?></span>
				</td>
			</tr>
		</table>
		<p class="submit">
			<?php wp_nonce_field('verify'); ?>
			<input type="hidden" name="key" value="credits" />
			<input type="submit" class="button-primary" name="save" value="<?php _e( 'Save Changes', $this->text_domain ); ?>" />
		</p>
	</form>
</div>