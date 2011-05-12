<?php get_header() ?>	

<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-single"><!-- start #blog-single -->

			<?php if ( have_posts() ) : ?>
		    	<?php locate_template( array( '/loops/loop-single.php' ), true ); ?>
		    	<?php locate_template( array( '/components/pagination.php' ), true ); ?>
			<?php else : ?>
				<?php locate_template( array( '/components/messages.php' ), true ); ?>
			<?php endif; ?>

		</div><!-- end #blog-single -->
	</div>
</div><!-- end #content -->

<?php get_footer() ?>
