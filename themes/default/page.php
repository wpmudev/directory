<?php get_header() ?>
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-page"><!-- start #blog-page -->

			<?php if ( have_posts() ) : ?>
		    	<?php locate_template( array( '/loops/loop-page.php' ), true ); ?>
		    	<?php locate_template( array( '/components/pagination.php' ), true ); ?>
			<?php else : ?>
				<?php locate_template( array( '/components/messages.php' ), true ); ?>
			<?php endif; ?>

		</div><!-- end #blog-page -->
	</div><!-- end .padder -->
</div><!-- end #content -->
<?php get_footer(); ?>
