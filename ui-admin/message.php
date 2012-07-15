<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $msg = __( 'Settings Saved.', $this->text_domain ); ?>

<?php if ( !empty($this->message) ): ?>
<div class="updated below-h2" id="message">
	<p><?php echo $this->message; ?></p>
</div>
<?php endif; ?>