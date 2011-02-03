				<div class="clear"></div>
	</div>
</div>
<div id="footer-wrapper">
	<div id="footer">
				<?php locate_template( array( '/includes/components/stateresults.php' ), true ); ?>
		<div class="clear"></div>
		<div id="copyright-links">
			<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'Theme by WPMU DEV', THEME_TEXT_DOMAIN )?>" ><?php _e( 'WPMU DEV', THEME_TEXT_DOMAIN ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', THEME_TEXT_DOMAIN ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', THEME_TEXT_DOMAIN ); ?></a>
		</div>
		        <?php wp_footer(); ?>
	</div>
</div>
	</body>
</html>
