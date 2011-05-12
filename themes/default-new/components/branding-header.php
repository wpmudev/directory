<!-- start branding -->
<div id="branding">
<div id="site-logo"><!-- start #site-logo -->
	<a href="<?php echo get_option('home') ?>" title="<?php _e( 'Home', THEME_TEXT_DOMAIN ) ?>"><?php bloginfo('name'); ?></a>
	</div><!-- end #site-logo -->
    <div id="h-banner"><?php if ( isset( $options['ads_settings']['header_ad_code'] ) ) echo $options['ads_settings']['header_ad_code']; ?></div>
	<div class="clear"></div>
</div>
<!-- end branding -->
