<div class="clear"></div>
</div><!--end container -->
<div class="dp-widgets-stra"></div>
<?php
get_sidebar( 'footer' );
?>
<div id="footer"><!-- start #footer -->
<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'Theme by WPMU DEV', THEME_TEXT_DOMAIN )?>" ><?php _e( 'WPMU DEV', THEME_TEXT_DOMAIN ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', THEME_TEXT_DOMAIN ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', THEME_TEXT_DOMAIN ); ?></a>
</div><!-- end #footer -->
</div><!-- end #site-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
