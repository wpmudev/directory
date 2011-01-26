<?php get_header() ?>	
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-single"><!-- start #blog-single -->
			<?php if (have_posts()) :  ?>
				<?php wpmu_singleloop(); ?>
				<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
			<?php else: ?>
				<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div><!-- end #blog-single -->
	</div>
</div><!-- end #content -->
<?php get_footer() ?>