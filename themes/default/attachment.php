<?php get_header() ?>
<div id="content-fullwidth"><!-- start #content -->
	<div class="padder">
		<div class="page" id="attachments-page">
				<?php if (have_posts()) :  ?>	
					<?php wpmu_attachmentloop(); ?>
				<?php else: ?>
						<p><?php _e( 'Sorry, no attachments matched your criteria.', THEME_TEXT_DOMAIN ) ?></p>
				<?php endif; ?>
		</div><!-- end #blog-latest -->
	</div>
</div><!-- end #content -->
<?php get_footer() ?>
