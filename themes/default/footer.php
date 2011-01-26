				<div class="clear"></div>
			</div><!--end container -->
			<div class="dp-widgets-stra"></div>
			<?php
				get_sidebar( 'footer' );
			?>
			<div id="footer"><!-- start #footer -->
					<?php $footerlinks = get_option('dev_directory_footer_links'); ?>
						<?php echo stripslashes($footerlinks); ?>						
				<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'Theme by WPMU DEV', TEMPLATE_DOMAIN )?>" ><?php _e( 'WPMU DEV', TEMPLATE_DOMAIN ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', TEMPLATE_DOMAIN ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a>
			</div><!-- end #footer -->
		</div><!-- end #site-wrapper -->
				<?php wp_footer(); ?>
					<!-- start google code-->
					<?php $googlecode = get_option('dev_directory_google');
					echo $googlecode;
					?>
					<!-- end google code -->
	</body>
</html>