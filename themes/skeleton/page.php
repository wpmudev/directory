<?php get_header() ?>
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-page"><!-- start #blog-page -->
			<?php if (have_posts()) :  ?>
				<?php wpmu_pageloop(); ?>
			<?php endif; ?>
		</div><!-- end #blog-page -->
	</div>
</div><!-- end #content -->
<?php get_footer(); ?>
