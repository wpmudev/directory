<div id="searchbox">
	<form method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
		<div>
			<label class="screen-reader-text" for="s"><?php _e( 'What can we find for you today?', THEME_TEXT_DOMAIN ); ?></label>
			<input type="text" value="" name="s" id="s" size="32"/>
			<input type="submit" id="searchsubmit" value="<?php _e('Find it now!', THEME_TEXT_DOMAIN); ?> " />
		</div>
	</form>
</div>
