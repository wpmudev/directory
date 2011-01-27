<?php get_header() ?>
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-search"><!-- start #blog-search -->
			<?php if (have_posts()) : ?>
				<?php locate_template( array( '/includes/components/headers.php' ), true ); ?>
				<?php wpmu_excerptloop(); ?>
				<?php locate_template( array( '/includes/components/pagination.php' ), true ); ?>
			<?php else: ?>
				<?php locate_template( array( '/includes/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div><!-- end #blog-search -->
	</div>
</div><!-- end #content -->
<?php get_footer() ?>
