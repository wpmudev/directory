<?php get_header(); ?>
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-archives"><!-- start #blog-archives -->
			<?php locate_template( array( '/library/components/headers.php' ), true ); ?>
			<?php if ( have_posts() ) : ?>
				<?php wpmu_excerptloop(); ?>
				<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
			<?php else: ?>
				<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div><!-- end #blog-archives -->
	</div>
</div><!-- end #content -->
<?php get_footer(); ?>
