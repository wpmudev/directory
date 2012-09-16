<?php

$options = $this->get_options('payments');

?>
<!-- Begin My Credits -->

<div class="my-credits">

	<h3><?php _e( 'Available Directory Credits', $this->text_domain ); ?></h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="available_credits"><?php _e('Available Credits', $this->text_domain ) ?></label>
			</th>
			<td>
				<input type="text" id="available_credits" size="5" class="small-text" name="available_credits" value="<?php echo $this->transactions->credits; ?>" disabled="disabled" />
				<span class="description"><?php _e( 'All of your currently available credits.', $this->text_domain ); ?></span>
			</td>
		</tr>
	</table>

	<h3><?php _e( 'Purchase Directory Credits', $this->text_domain ); ?></h3>
	<table class="form-table">
		<tr>
			<th>
				<label><?php _e('Purchase Additional Directory Credits', $this->text_domain ) ?></label>
			</th>
			<td>
				<p class="submit">
					<?php echo do_shortcode('[dr_signup_btn text="' . __('Purchase Directory Credits', $this->text_domain) . '"]'); ?>
				</p>
			</td>
		</tr>
	</table>

	<?php $credits_log = $this->transactions->credits_log; ?>
	<h3><?php _e( 'Purchase History', $this->text_domain ); ?></h3>
	<?php if ( is_array( $credits_log ) ): ?>
	<table class="form-table">
		<?php foreach ( $credits_log as $log ): ?>
		<tr>
			<th>
				<label for="available_credits"><?php _e('Date:', $this->text_domain ) ?> <?php echo $this->format_date( $log['date'] ); ?></label>
			</th>
			<td>
				<input type="text" id="available_credits" size="5" class="small-text" name="available_credits" value="<?php echo $log['credits']; ?>" disabled="disabled" />
				<?php if($log['credits'] < 0): ?> 
				<span class="description"><?php _e( 'Directory Credits spent.', $this->text_domain ); ?></span>
				<?php else: ?>
				<span class="description"><?php _e( 'Directory Credits purchased.', $this->text_domain ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else: ?>
	<?php echo $credits_log; ?>
	<?php endif; ?>
</div>
